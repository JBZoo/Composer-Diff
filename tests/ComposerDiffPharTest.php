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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Cli;
use JBZoo\Utils\Sys;

final class ComposerDiffPharTest extends AbstractComposerDiffTest
{
    /**
     * @throws \Exception
     */
    protected function task(array $params = []): string
    {
        return $this->taskReal($params);
    }

    protected function taskReal(array $params = []): string
    {
        $rootDir = PROJECT_ROOT;

        return Cli::exec(
            \implode(' ', [
                'COLUMNS=120',
                Sys::getBinary(),
                "{$rootDir}/build/composer-diff.phar",
                '--no-interaction',
                '--no-ansi',
            ]),
            $params,
            $rootDir,
            false,
        );
    }
}
