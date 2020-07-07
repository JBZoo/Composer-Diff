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

use JBZoo\Data\Data;

use function JBZoo\Data\data;

/**
 * Class Package
 * @package JBZoo\ComposerDiff
 */
class Package
{
    public const HASH_LENGTH = 7;

    /**
     * @var Data
     */
    protected $data;

    /**
     * Package constructor.
     * @param array $packageDate
     */
    public function __construct(array $packageDate)
    {
        $this->data = data($packageDate);

        if ($this->data->count() === 0) {
            throw new Exception("Can't parse package data");
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->data->get('name');
    }

    /**
     * @param bool $prettyPrint
     * @return string
     */
    public function getVersion(bool $prettyPrint = false): string
    {
        $version = (string)$this->data->get('version');
        if ($prettyPrint) {
            $version = (string)preg_replace('#^v\.#i', '', $version);
            $version = (string)preg_replace('#^v#i', '', $version);
        }

        $reference = (string)$this->data->find('source.reference');

        if (strlen($reference) >= self::HASH_LENGTH && 0 === strpos($version, 'dev-')) {
            $version = substr($reference, 0, self::HASH_LENGTH) ?: '';
            if ($prettyPrint) {
                $version = "{$this->data->get('version')}@{$version}";
            }
        }

        return $version;
    }

    /**
     * @return string
     */
    public function getSourceUrl(): string
    {
        return (string)$this->data->find('source.url');
    }

    /**
     * @return string|null
     */
    public function getPackageUrl(): ?string
    {
        if ($url = $this->getSourceUrl()) {
            return Url::getPackageUrl($url);
        }

        return null;
    }
}
