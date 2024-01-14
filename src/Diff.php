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

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

final class Diff
{
    public const MODE_NEW        = 'New';
    public const MODE_REMOVED    = 'Removed';
    public const MODE_CHANGED    = 'Changed';
    public const MODE_UPGRADED   = 'Upgraded';
    public const MODE_DOWNGRADED = 'Downgraded';
    public const MODE_SAME       = 'Same';

    private string $mode          = self::MODE_SAME;
    private ?string $comparingUrl = null;
    private ?Package $target      = null;
    private ?Package $source;

    public function __construct(?Package $sourcePackage = null)
    {
        $this->source = $sourcePackage;
    }

    public function setMode(string $newMode): self
    {
        $this->mode = $newMode;

        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function toArray(): array
    {
        if ($this->source !== null) {
            return [
                'name'         => $this->source->getName(),
                'url'          => $this->source->getPackageUrl(),
                'version_from' => $this->source->getVersion(true),
                'version_to'   => $this->target?->getVersion(true),
                'mode'         => $this->mode,
                'compare'      => $this->comparingUrl,
            ];
        }

        if ($this->target !== null) {
            return [
                'name'         => $this->target->getName(),
                'url'          => $this->target->getPackageUrl(),
                'version_from' => null,
                'version_to'   => $this->target->getVersion(true),
                'mode'         => $this->mode,
                'compare'      => $this->comparingUrl,
            ];
        }

        throw new Exception('Source and target packages are not defined');
    }

    public function compareWithPackage(Package $targetPackage): self
    {
        $this->target = $targetPackage;

        if ($this->source === null) {
            return $this->setMode(self::MODE_NEW);
        }

        if ($this->source->getName() !== $this->target->getName()) {
            throw new Exception(
                "Can't compare versions of different packages. " .
                "Source:{$this->source->getName()}; Target:{$this->target->getName()};",
            );
        }

        $sourceVersion      = $this->source->getVersion();
        $targetVersion      = $this->target->getVersion();
        $this->comparingUrl = $this->getComparingUrl($sourceVersion, $targetVersion);

        if ($sourceVersion === $targetVersion) {
            return $this->setMode(self::MODE_SAME);
        }

        if (self::isHashVersion($sourceVersion) || self::isHashVersion($targetVersion)) {
            return $this->setMode(self::MODE_CHANGED);
        }

        $parser = new VersionParser();

        $normalizedSource = $parser->normalize($sourceVersion);
        $normalizedTarget = $parser->normalize($targetVersion);

        if (Comparator::greaterThan($normalizedSource, $normalizedTarget)) {
            return $this->setMode(self::MODE_DOWNGRADED);
        }

        if (Comparator::lessThan($normalizedSource, $normalizedTarget)) {
            return $this->setMode(self::MODE_UPGRADED);
        }

        return $this->setMode(self::MODE_CHANGED);
    }

    public function getComparingUrl(?string $fromVersion, ?string $toVersion): ?string
    {
        if (\in_array($fromVersion, [self::MODE_REMOVED, self::MODE_NEW], true)) {
            return '';
        }

        if (\in_array($toVersion, [self::MODE_REMOVED, self::MODE_NEW], true)) {
            return '';
        }

        if ($this->source !== null) {
            return Url::getCompareUrl($this->source->getSourceUrl(), $fromVersion, $toVersion);
        }

        if ($this->target !== null) {
            return Url::getCompareUrl($this->target->getSourceUrl(), $fromVersion, $toVersion);
        }

        throw new Exception('Source and target packages are not defined');
    }

    private static function isHashVersion(string $version): bool
    {
        return \strlen($version) === Package::HASH_LENGTH && !\str_contains($version, '.');
    }
}
