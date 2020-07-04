<?php

/**
 * JBZoo Toolbox - Composer-Diff
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Composer-Diff
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Composer-Diff
 */

namespace JBZoo\ComposerDiff;

use JBZoo\Utils\Cli;

use function JBZoo\Data\json;

/**
 * Class Comporator
 * @package JBZoo\ComposerDiff
 */
class Comparator
{
    public const ENV_BOTH = 'both';
    public const ENV_PROD = 'require';
    public const ENV_DEV  = 'require-dev';

    /**
     * @param string $sourceFile
     * @param string $targetFile
     * @return array
     */
    public static function compare($sourceFile, $targetFile): array
    {
        $sourceData = self::load($sourceFile);
        $targetData = self::load($targetFile);

        return [
            self::ENV_PROD => self::diff(self::ENV_PROD, $sourceData, $targetData),
            self::ENV_DEV  => self::diff(self::ENV_DEV, $sourceData, $targetData),
        ];
    }

    /**
     * @param string         $type
     * @param CommploserLock $sourceData
     * @param CommploserLock $targetData
     * @return Diff[]
     */
    private static function diff(string $type, CommploserLock $sourceData, CommploserLock $targetData): array
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
            if (!array_key_exists($targetName, $resultDiff)) {
                $resultDiff[$targetName] = (new Diff())->setMode(Diff::MODE_NEW);
            }

            $resultDiff[$targetName]->compareWithPackage($targetPackage);
        }

        $resultDiff = array_filter($resultDiff, function (Diff $diff) {
            return $diff->getMode() !== Diff::MODE_SAME;
        }, ARRAY_FILTER_USE_BOTH);

        ksort($resultDiff, SORT_NATURAL);

        return $resultDiff;
    }

    /**
     * @param string $composerFile
     * @return CommploserLock
     */
    private static function load(string $composerFile): CommploserLock
    {
        if (
            Url::isUrl($composerFile) &&
            !in_array(parse_url($composerFile, PHP_URL_SCHEME), stream_get_wrappers(), true)
        ) {
            throw new Exception("There is no stream wrapper to open \"{$composerFile}\"");
        }
        if (file_exists($composerFile)) {
            $json = json(file_get_contents($composerFile));
            return new CommploserLock($json->getArrayCopy());
        }

        if (strpos($composerFile, ':') !== false) {
            $json = json(Cli::exec('git show ' . escapeshellarg($composerFile)));
            return new CommploserLock($json->getArrayCopy());
        }

        throw new Exception("Composer lock file \"{$composerFile}\" not found");
    }
}
