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

use JBZoo\Cli\CliApplication;
use JBZoo\ComposerDiff\Commands\DiffAction;
use JBZoo\Utils\Cli;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class ComposerDiffTest extends AbstractComposerDiffTest
{
    /**
     * @throws \Exception
     */
    protected function task(array $params = []): string
    {
        $application = new CliApplication();
        $application->add(new DiffAction());
        $application->setDefaultCommand('diff');
        $command = $application->find('diff');

        $buffer = new BufferedOutput();
        $args = new StringInput(Cli::build('', $params));
        $code = $command->run($args, $buffer);

        if ($code > 0) {
            throw new \RuntimeException($buffer->fetch());
        }

        return $buffer->fetch();
    }

    protected function taskReal(array $params = []): string
    {
        $rootDir = PROJECT_ROOT;

        return Cli::exec(
            \implode(' ', [
                'COLUMNS=120',
                Sys::getBinary(),
                "{$rootDir}/composer-diff.php",
                '--no-interaction',
                '--no-ansi',
            ]),
            $params,
            $rootDir,
            false,
        );
    }
}
