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

namespace JBZoo\PHPUnit;

use JBZoo\ComposerDiff\Comparator;
use JBZoo\ComposerDiff\Diff;
use JBZoo\ComposerDiff\Url;
use JBZoo\Utils\Cli;
use JBZoo\Utils\Sys;

use function JBZoo\Data\phpArray;

/**
 * Class ComposerDiffTest
 *
 * @package JBZoo\PHPUnit
 */
class ComposerDiffTest extends PHPUnit
{
    private const URL_GITHUB_1 = 'git@github.com:JBZoo/Utils.git';
    private const URL_GITHUB_2 = 'https://github.com/JBZoo/Utils.git';
    private const URL_GITHUB_3 = 'https://github.com/JBZoo/Utils';

    private const URL_GITLAB_1 = 'https://gitlab.com/gitlab-org/gitlab-runner';
    private const URL_GITLAB_2 = 'git@gitlab.com:gitlab-org/gitlab-runner.git';

    private const URL_BITBUCKET_1 = 'https://bitbucket.org/vendor/project.git';
    private const URL_BITBUCKET_2 = 'git@bitbucket.org:vendor/project.git';

    private const URL_INVALID = 'https://google.com';

    public function testPackageUrl()
    {
        isSame('https://github.com/JBZoo/Utils', Url::getPackageUrl(self::URL_GITHUB_1));
        isSame('https://github.com/JBZoo/Utils', Url::getPackageUrl(self::URL_GITHUB_2));
        isSame('https://github.com/JBZoo/Utils', Url::getPackageUrl(self::URL_GITHUB_3));

        isSame('https://gitlab.com/gitlab-org/gitlab-runner', Url::getPackageUrl(self::URL_GITLAB_1));
        isSame('https://gitlab.com/gitlab-org/gitlab-runner', Url::getPackageUrl(self::URL_GITLAB_2));

        isSame('https://bitbucket.org/vendor/project', Url::getPackageUrl(self::URL_BITBUCKET_1));
        isSame('https://bitbucket.org/vendor/project', Url::getPackageUrl(self::URL_BITBUCKET_2));

        isSame(null, Url::getPackageUrl(self::URL_INVALID));
    }

    public function testCompareGitHub()
    {
        isSame(
            'https://github.com/JBZoo/Utils/compare/2.0.0...3.0.0',
            Url::getCompareUrl(self::URL_GITHUB_1, '2.0.0', '3.0.0')
        );
        isSame(
            'https://github.com/JBZoo/Utils/compare/2.0.0...3.0.0',
            Url::getCompareUrl(self::URL_GITHUB_2, '2.0.0', '3.0.0')
        );
        isSame(
            'https://github.com/JBZoo/Utils/compare/2.0.0...3.0.0',
            Url::getCompareUrl(self::URL_GITHUB_3, '2.0.0', '3.0.0')
        );

        isSame(
            'https://gitlab.com/gitlab-org/gitlab-runner/compare/1.0...2.0',
            Url::getCompareUrl(self::URL_GITLAB_1, '1.0', '2.0')
        );
        isSame(
            'https://gitlab.com/gitlab-org/gitlab-runner/compare/1.0...2.0',
            Url::getCompareUrl(self::URL_GITLAB_2, '1.0', '2.0')
        );

        isSame(
            'https://bitbucket.org/vendor/project/branches/compare/1.0%0D2.0',
            Url::getCompareUrl(self::URL_BITBUCKET_1, '1.0', '2.0')
        );
        isSame(
            'https://bitbucket.org/vendor/project/branches/compare/1.0%0D2.0',
            Url::getCompareUrl(self::URL_BITBUCKET_2, '1.0', '2.0')
        );

        isSame(null, Url::getCompareUrl(self::URL_INVALID, '1.0', '2.0'));
    }

    public function testComparingSamePackage()
    {
        isSame(["require" => [], "require-dev" => []],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingSamePackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingSamePackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingNewPackage()
    {
        isSame([
            "require"     => [
                [
                    'name'         => 'vendor-1/package-1',
                    'url'          => 'https://gitlab.com/vendor-1/package-1',
                    'version_from' => null,
                    'version_to'   => '1.0.0',
                    'mode'         => 'New',
                    'compare'      => null,
                ]
            ],
            "require-dev" => []
        ],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingNewPackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingNewPackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingRemovedPackage()
    {
        isSame([
            "require"     => [
                [
                    'name'         => 'vendor-1/package-1',
                    'url'          => 'https://gitlab.com/vendor-1/package-1',
                    'version_from' => '1.0.0',
                    'version_to'   => null,
                    'mode'         => 'Removed',
                    'compare'      => null,
                ]
            ],
            "require-dev" => []
        ],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingRemovedPackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingRemovedPackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingUpgradingPackage()
    {
        isSame([
            "require"     => [
                [
                    'name'         => 'vendor-1/package-1',
                    'url'          => 'https://gitlab.com/vendor-1/package-1',
                    'version_from' => '1.0.0',
                    'version_to'   => '2.0.0',
                    'mode'         => 'Upgraded',
                    'compare'      => 'https://gitlab.com/vendor-1/package-1/compare/1.0.0...2.0.0',
                ]
            ],
            "require-dev" => []
        ],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingUpgradingPackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingUpgradingPackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingDowngradedPackage()
    {
        isSame([
            "require"     => [
                [
                    'name'         => 'vendor-1/package-1',
                    'url'          => 'https://gitlab.com/vendor-1/package-1',
                    'version_from' => '2.0.0',
                    'version_to'   => '1.0.0',
                    'mode'         => 'Downgraded',
                    'compare'      => 'https://gitlab.com/vendor-1/package-1/compare/2.0.0...1.0.0',
                ]
            ],
            "require-dev" => []
        ],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingDowngradedPackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingDowngradedPackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingChangedPackage()
    {
        isSame([
            "require"     => [
                [
                    'name'         => 'vendor-1/package-1',
                    'url'          => 'https://gitlab.com/vendor-1/package-1',
                    'version_from' => 'bbc0fba',
                    'version_to'   => '4121ea4',
                    'mode'         => 'Changed',
                    'compare'      => 'https://gitlab.com/vendor-1/package-1/compare/bbc0fba...4121ea4',
                ]
            ],
            "require-dev" => []
        ],
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingChangedPackage/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingChangedPackage/composer-lock-to.json'
            ))
        );
    }

    public function testComparingComplex()
    {
        isSame(
            phpArray(__DIR__ . '/fixtures/testComparingComplex/expected-diff.php')->getArrayCopy(),
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingComplex/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingComplex/composer-lock-to.json'
            ))
        );
    }

    public function testComparingComplexSimple()
    {
        isSame(
            phpArray(__DIR__ . '/fixtures/testComparingComplexSimple/expected-diff.php')->getArrayCopy(),
            $this->toArray(Comparator::compare(
                __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
                __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json'
            ))
        );
    }

    public function testMarkdownFormatOutput()
    {
        $expectedProd = implode("\n", [
            '  Required by Production (require)',
            '+-------------------+------------+-------------+-------------+------------------------------------------------------------+',
            '| Package           | Action     | Old Version | New Version | Details                                                    |',
            '+-------------------+------------+-------------+-------------+------------------------------------------------------------+',
            '| vendor/downgraded | Downgraded |       2.0.0 |       1.0.0 | https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0 |',
            '| vendor/new        | New        |           - |       1.0.0 |                                                            |',
            '| vendor/removed    | Removed    |       1.0.0 |           - |                                                            |',
            '| vendor/upgraded   | Upgraded   |       1.0.0 |       2.0.0 | https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0   |',
            '+-------------------+------------+-------------+-------------+------------------------------------------------------------+',
        ]);

        $expectedDev = implode("\n", [
            '  Required by Development (require-dev)',
            '+-----------------------+------------+-------------+-------------+----------------------------------------------------------------+',
            '| Package               | Action     | Old Version | New Version | Details                                                        |',
            '+-----------------------+------------+-------------+-------------+----------------------------------------------------------------+',
            '| vendor/downgraded-dev | Downgraded |       2.0.0 |       1.0.0 | https://gitlab.com/vendor/downgraded-dev/compare/2.0.0...1.0.0 |',
            '| vendor/new-dev        | New        |           - |       1.0.0 |                                                                |',
            '| vendor/removed-dev    | Removed    |       1.0.0 |           - |                                                                |',
            '| vendor/upgraded-dev   | Upgraded   |       1.0.0 |       2.0.0 | https://gitlab.com/vendor/upgraded-dev/compare/1.0.0...2.0.0   |',
            '+-----------------------+------------+-------------+-------------+----------------------------------------------------------------+',
        ]);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
        ]);

        isContain($expectedProd, $cliOutput);
        isContain($expectedDev, $cliOutput);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require'
        ]);

        isContain($expectedProd, $cliOutput);
        isNotContain($expectedDev, $cliOutput);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require-dev'
        ]);

        isNotContain($expectedProd, $cliOutput);
        isContain($expectedDev, $cliOutput);
    }

    public function testConsoleFormatOutput()
    {
        $expectedProd = implode("\n", [
            '## Required by Production (require)',
            '',
            '| Package                                                   | Action     | Old Version | New Version | Details                                                               |',
            '|-----------------------------------------------------------|------------|------------:|------------:|-----------------------------------------------------------------------|',
            '| [vendor/downgraded](https://gitlab.com/vendor/downgraded) | Downgraded |       2.0.0 |       1.0.0 | [Details](https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0) |',
            '| [vendor/new](https://gitlab.com/vendor/new)               | New        |           - |       1.0.0 |                                                                       |',
            '| [vendor/removed](https://gitlab.com/vendor/removed)       | Removed    |       1.0.0 |           - |                                                                       |',
            '| [vendor/upgraded](https://gitlab.com/vendor/upgraded)     | Upgraded   |       1.0.0 |       2.0.0 | [Details](https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0)   |',
        ]);

        $expectedDev = implode("\n", [
            '## Required by Development (require-dev)',
            '',
            '| Package                                                           | Action     | Old Version | New Version | Details                                                                   |',
            '|-------------------------------------------------------------------|------------|------------:|------------:|---------------------------------------------------------------------------|',
            '| [vendor/downgraded-dev](https://gitlab.com/vendor/downgraded-dev) | Downgraded |       2.0.0 |       1.0.0 | [Details](https://gitlab.com/vendor/downgraded-dev/compare/2.0.0...1.0.0) |',
            '| [vendor/new-dev](https://gitlab.com/vendor/new-dev)               | New        |           - |       1.0.0 |                                                                           |',
            '| [vendor/removed-dev](https://gitlab.com/vendor/removed-dev)       | Removed    |       1.0.0 |           - |                                                                           |',
            '| [vendor/upgraded-dev](https://gitlab.com/vendor/upgraded-dev)     | Upgraded   |       1.0.0 |       2.0.0 | [Details](https://gitlab.com/vendor/upgraded-dev/compare/1.0.0...2.0.0)   |',
        ]);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'output' => 'markdown',
        ]);

        isContain($expectedProd, $cliOutput);
        isContain($expectedDev, $cliOutput);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require',
            'output' => 'markdown',
        ]);

        isContain($expectedProd, $cliOutput);
        isNotContain($expectedDev, $cliOutput);

        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require-dev',
            'output' => 'markdown',
        ]);

        isNotContain($expectedProd, $cliOutput);
        isContain($expectedDev, $cliOutput);
    }

    public function testStrictMode()
    {
        $this->expectException(\Exception::class);

        $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'strict' => null
        ]);
    }

    public function testStrictModeRequireOnly()
    {
        $this->expectException(\Exception::class);

        $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require',
            'strict' => null
        ]);
    }

    public function testStrictModeRequireDevOnly()
    {
        $this->expectException(\Exception::class);

        $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-to.json',
            'env'    => 'require-dev',
            'strict' => null
        ]);
    }

    public function testStrictModeNoErrors()
    {
        $cliOutput = $this->task([
            'source' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'target' => __DIR__ . '/fixtures/testComparingComplexSimple/composer-lock-from.json',
            'strict' => null
        ]);

        isContain('There is no difference (require)', $cliOutput);
        isContain('There is no difference (require-dev)', $cliOutput);
    }

    #### Testing Tools /////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param array $fullDiff
     * @return array
     */
    private function toArray(array $fullDiff)
    {
        $result = [];

        /** @var Diff[] $rows */
        foreach ($fullDiff as $env => $rows) {
            $result[$env] = [];
            foreach ($rows as $diff) {
                $result[$env][] = $diff->toArray();
            }
        }

        return $result;
    }

    /**
     * @param string $taskName
     * @param array  $params
     * @return string
     */
    private function task(array $params = [])
    {
        $rootDir = PROJECT_ROOT;

        return Cli::exec(
            implode(' ', [
                'COLUMNS=120',
                Sys::getBinary(),
                "{$rootDir}/tests/cli-wrapper.php",
                '--no-interaction',
                '--no-ansi'
            ]),
            $params,
            $rootDir,
            0
        );
    }
}
