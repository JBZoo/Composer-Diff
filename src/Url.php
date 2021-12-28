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

declare(strict_types=1);

namespace JBZoo\ComposerDiff;

/**
 * Class Url
 * @package JBZoo\ComposerDiff
 */
final class Url
{
    public const BITBUCKET = 'bitbucket';
    public const GITHUB    = 'github';
    public const GITLAB    = 'gitlab';
    public const UNKNOWN   = 'unknown';

    /**
     * @param string|null $sourceUrl
     * @param string|null $fromVersion
     * @param string|null $toVersion
     * @return string|null
     */
    public static function getCompareUrl(?string $sourceUrl, ?string $fromVersion, ?string $toVersion): ?string
    {
        if (!$sourceUrl || !$fromVersion || !$toVersion) {
            return null;
        }

        $service = self::detectService($sourceUrl);
        $url = self::getPackageUrl($sourceUrl);

        $fromVersion = \urlencode($fromVersion);
        $toVersion = \urlencode($toVersion);

        if (\in_array($service, [self::GITHUB, self::GITLAB], true)) {
            return "{$url}/compare/{$fromVersion}...{$toVersion}";
        }

        if (self::BITBUCKET === $service) {
            return "{$url}/branches/compare/{$fromVersion}%0D{$toVersion}";
        }

        return null;
    }

    /**
     * @param string $sourceUrl
     * @return string|null
     */
    public static function getPackageUrl(string $sourceUrl): ?string
    {
        $service = self::detectService($sourceUrl);

        if (self::GITHUB === $service) {
            if (\strpos($sourceUrl, 'http') === false) {
                $sourceUrl = (string)\preg_replace('/^git@(github\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        if (self::GITLAB === $service) {
            if (\strpos($sourceUrl, 'http') === false) {
                $sourceUrl = (string)\preg_replace('/^git@(gitlab\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        if (self::BITBUCKET === $service) {
            if (\strpos($sourceUrl, 'http') === false) {
                $sourceUrl = (string)\preg_replace('/^git@(bitbucket\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        return null;
    }

    /**
     * @param string $url
     * @return string
     */
    private static function detectService(string $url): string
    {
        if (\preg_match('/^git@github\..+:.+\.git$/', $url)) {
            return self::GITHUB;
        }

        if (\preg_match('/^git@gitlab\..+:.+\.git$/', $url)) {
            return self::GITLAB;
        }

        if (\preg_match('/^git@bitbucket\..+:.+\.git$/', $url)) {
            return self::BITBUCKET;
        }

        if (0 !== \stripos($url, "http")) {
            return self::UNKNOWN;
        }

        $host = \strtolower((string)\parse_url($url, \PHP_URL_HOST));

        if (\strpos($host, 'github') !== false) {
            return self::GITHUB;
        }

        if (\strpos($host, 'bitbucket') !== false) {
            return self::BITBUCKET;
        }

        if (\strpos($host, 'gitlab') !== false) {
            return self::GITLAB;
        }

        return self::UNKNOWN;
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isUrl(string $string): bool
    {
        return (bool)\filter_var($string, \FILTER_VALIDATE_URL, \FILTER_FLAG_PATH_REQUIRED);
    }
}
