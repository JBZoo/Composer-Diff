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

use JBZoo\Data\Data;

use function JBZoo\Data\data;

final class Package
{
    public const HASH_LENGTH = 7;

    private Data $data;

    public function __construct(array $packageDate)
    {
        $this->data = data($packageDate);

        if ($this->data->count() === 0) {
            throw new Exception("Can't parse package data");
        }
    }

    public function getName(): string
    {
        return $this->data->getString('name');
    }

    public function getVersion(bool $prettyPrint = false): string
    {
        $version = $this->data->getString('version');
        if ($prettyPrint) {
            $version = (string)\preg_replace('#^v\.#i', '', $version);
            $version = (string)\preg_replace('#^v#i', '', $version);
        }

        $reference = $this->data->findString('source.reference');

        if (
            \strlen($reference) >= self::HASH_LENGTH
            && (\str_starts_with($version, 'dev-') || \str_ends_with($version, '-dev'))
        ) {
            $version = \substr($reference, 0, self::HASH_LENGTH);
            if ($prettyPrint) {
                $version = "{$this->data->getString('version')}@{$version}";
            }
        }

        return $version;
    }

    public function getSourceUrl(): string
    {
        return $this->data->findString('source.url');
    }

    public function getPackageUrl(): ?string
    {
        $url = $this->getSourceUrl();
        if ($url !== '') {
            return Url::getPackageUrl($url);
        }

        return null;
    }
}
