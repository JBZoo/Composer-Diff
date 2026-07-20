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
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class ComposerDiffTest extends AbstractComposerDiffTestCase
{
    /**
     * @throws \Exception
     */
    protected function task(array $params = []): string
    {
        $application = new CliApplication();
        // Symfony Console 8.0 removed Application::add(); addCommands() exists on 7.3+/8.x.
        $application->addCommands([new DiffAction()]);
        $application->setDefaultCommand('diff');
        $command = $application->find('diff');

        $buffer = new BufferedOutput();
        $args   = new StringInput(Cli::build('', $params));
        $code   = $command->run($args, $buffer);

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
                // -d error_reporting mutes E_DEPRECATED in the child: the --prefer-lowest CI leg pulls
                // old transitive dev-tool deps (amphp/*, sabre/event via php-coveralls) whose files-
                // autoload emits "implicitly nullable" deprecations on PHP 8.5+ that Cli::exec captures
                // into the report/help output. Production (the phar or a no-dev install) never loads them.
                Sys::getBinary() . ' -d error_reporting=' . (\E_ALL & ~\E_DEPRECATED),
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
