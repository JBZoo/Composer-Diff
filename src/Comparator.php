<?php

/**
 * JBZoo Toolbox - Composer-Diff.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Composer-Diff
 */

declare(strict_types=1);

namespace JBZoo\ComposerDiff;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function JBZoo\Data\json;

final class Comparator
{
    public const ENV_BOTH = 'both';
    public const ENV_PROD = 'require';
    public const ENV_DEV  = 'require-dev';

    public static function compare(string $sourceFile, string $targetFile): array
    {
        $sourceData = self::load($sourceFile);
        $targetData = self::load($targetFile);

        return [
            self::ENV_PROD => self::diff(self::ENV_PROD, $sourceData, $targetData),
            self::ENV_DEV  => self::diff(self::ENV_DEV, $sourceData, $targetData),
        ];
    }

    /**
     * @return Diff[]
     */
    private static function diff(string $type, ComposerLock $sourceData, ComposerLock $targetData): array
    {
        if ($type === self::ENV_PROD) {
            $sourcePackages = $sourceData->getRequired();
            $targetPackages = $targetData->getRequired();
        } else {
            $sourcePackages = $sourceData->getRequiredDev();
            $targetPackages = $targetData->getRequiredDev();
        }

        /** @var Diff[] $resultDiff */
        $resultDiff = [];

        foreach ($sourcePackages as $package) {
            $resultDiff[$package->getName()] = (new Diff($package))->setMode(Diff::MODE_REMOVED);
        }

        foreach ($targetPackages as $targetName => $targetPackage) {
            if (!\array_key_exists($targetName, $resultDiff)) {
                $resultDiff[$targetName] = (new Diff())->setMode(Diff::MODE_NEW);
            }

            $resultDiff[$targetName]->compareWithPackage($targetPackage);
        }

        $resultDiff = \array_filter(
            $resultDiff,
            static fn (Diff $diff) => $diff->getMode() !== Diff::MODE_SAME,
            \ARRAY_FILTER_USE_BOTH,
        );

        \ksort($resultDiff, \SORT_NATURAL);

        return $resultDiff;
    }

    private static function load(string $composerFile): ComposerLock
    {
        if (
            Url::isUrl($composerFile)
            && !\in_array(\parse_url($composerFile, \PHP_URL_SCHEME), \stream_get_wrappers(), true)
        ) {
            throw new Exception("There is no stream wrapper to open \"{$composerFile}\"");
        }

        if (\file_exists($composerFile)) {
            $json = json(\file_get_contents($composerFile));

            return new ComposerLock($json->getArrayCopy());
        }

        if (\str_contains($composerFile, ':')) {
            $json = json(self::exec('git show ' . \escapeshellarg($composerFile)));

            return new ComposerLock($json->getArrayCopy());
        }

        throw new Exception("Composer lock file \"{$composerFile}\" not found");
    }

    private static function exec(string $command): string
    {
        $process = Process::fromShellCommandline($command);
        $process->run();

        if ($process->isSuccessful()) {
            return $process->getOutput();
        }

        throw new ProcessFailedException($process);
    }
}
