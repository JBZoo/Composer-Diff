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

final class Url
{
    public const BITBUCKET = 'bitbucket';
    public const GITHUB    = 'github';
    public const GITLAB    = 'gitlab';
    public const UNKNOWN   = 'unknown';

    public static function getCompareUrl(?string $sourceUrl, ?string $fromVersion, ?string $toVersion): ?string
    {
        if (
            $sourceUrl === null
            || $sourceUrl === ''
            || $fromVersion === null
            || $fromVersion === ''
            || $toVersion === null
            || $toVersion === ''
        ) {
            return null;
        }

        $service = self::detectService($sourceUrl);
        $url     = self::getPackageUrl($sourceUrl);

        $fromVersion = \urlencode($fromVersion);
        $toVersion   = \urlencode($toVersion);

        if (\in_array($service, [self::GITHUB, self::GITLAB], true)) {
            return "{$url}/compare/{$fromVersion}...{$toVersion}";
        }

        if ($service === self::BITBUCKET) {
            return "{$url}/branches/compare/{$fromVersion}%0D{$toVersion}";
        }

        return null;
    }

    public static function getPackageUrl(string $sourceUrl): ?string
    {
        $service = self::detectService($sourceUrl);

        if ($service === self::GITHUB) {
            if (!\str_contains($sourceUrl, 'http')) {
                $sourceUrl = (string)\preg_replace('/^git@(github\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        if ($service === self::GITLAB) {
            if (!\str_contains($sourceUrl, 'http')) {
                $sourceUrl = (string)\preg_replace('/^git@(gitlab\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        if ($service === self::BITBUCKET) {
            if (!\str_contains($sourceUrl, 'http')) {
                $sourceUrl = (string)\preg_replace('/^git@(bitbucket\.[^:]+):/', 'https://$1/', $sourceUrl);
            }

            return (string)\preg_replace('/\.git$/', '', $sourceUrl);
        }

        return null;
    }

    public static function isUrl(string $string): bool
    {
        return (bool)\filter_var($string, \FILTER_VALIDATE_URL, \FILTER_FLAG_PATH_REQUIRED);
    }

    private static function detectService(string $url): string
    {
        if (\preg_match('/^git@github\..+:.+\.git$/', $url) > 0) {
            return self::GITHUB;
        }

        if (\preg_match('/^git@gitlab\..+:.+\.git$/', $url) > 0) {
            return self::GITLAB;
        }

        if (\preg_match('/^git@bitbucket\..+:.+\.git$/', $url) > 0) {
            return self::BITBUCKET;
        }

        if (\stripos($url, 'http') !== 0) {
            return self::UNKNOWN;
        }

        $host = \strtolower((string)\parse_url($url, \PHP_URL_HOST));

        if (\str_contains($host, 'github')) {
            return self::GITHUB;
        }

        if (\str_contains($host, 'bitbucket')) {
            return self::BITBUCKET;
        }

        if (\str_contains($host, 'gitlab')) {
            return self::GITLAB;
        }

        return self::UNKNOWN;
    }
}
