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

namespace JBZoo\ComposerDiff\Commands;

use JBZoo\Cli\CliCommand;
use JBZoo\ComposerDiff\Comparator;
use JBZoo\ComposerDiff\Renders\AbstractRender;
use Symfony\Component\Console\Input\InputOption;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DiffAction extends CliCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $envs          = \implode(', ', [Comparator::ENV_BOTH, Comparator::ENV_PROD, Comparator::ENV_DEV]);
        $outputFormats = \implode(', ', [AbstractRender::CONSOLE, AbstractRender::MARKDOWN, AbstractRender::JSON]);

        $this
            ->setName('diff')
            ->setDescription('Show difference between two versions of composer.lock files')
            // File paths
            ->addOption(
                'source',
                null,
                InputOption::VALUE_REQUIRED,
                'The file, git ref, or git ref with filename to compare FROM',
                'HEAD:composer.lock',
            )
            ->addOption(
                'target',
                null,
                InputOption::VALUE_REQUIRED,
                'The file, git ref, or git ref with filename to compare TO',
                './composer.lock',
            )
            // Options
            ->addOption(
                'env',
                null,
                InputOption::VALUE_REQUIRED,
                "Show only selected environment. Available options: <info>{$envs}</info>",
                Comparator::ENV_BOTH,
            )
            ->addOption(
                'output',
                null,
                InputOption::VALUE_REQUIRED,
                "Output format. Available options: <info>{$outputFormats}</info>",
                AbstractRender::CONSOLE,
            )
            ->addOption('no-links', null, InputOption::VALUE_NONE, 'Hide all links in tables')
            ->addOption('strict', null, InputOption::VALUE_NONE, 'Return exit code if you have any difference');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function executeAction(): int
    {
        $sourcePath   = $this->getOptString('source');
        $targetPath   = $this->getOptString('target');
        $outputFormat = \strtolower($this->getOptString('output'));
        $env          = \strtolower($this->getOptString('env'));
        $isStrictMode = $this->getOptBool('strict');

        $params = [
            'show-links'  => !$this->getOptBool('no-links'),
            'strict-mode' => $isStrictMode,
        ];

        $fullChangeLog = Comparator::compare($sourcePath, $targetPath);

        $errorCode = 0;

        if (\in_array($env, [Comparator::ENV_BOTH, Comparator::ENV_PROD], true)) {
            $changeLog = $fullChangeLog[Comparator::ENV_PROD];
            $this->renderOutput($outputFormat, $changeLog, Comparator::ENV_PROD, $params);
            if ($isStrictMode && \count($changeLog) > 0) {
                $errorCode++;
            }
        }

        if (\in_array($env, [Comparator::ENV_BOTH, Comparator::ENV_DEV], true)) {
            $changeLog = $fullChangeLog[Comparator::ENV_DEV];
            $this->renderOutput($outputFormat, $changeLog, Comparator::ENV_DEV, $params);
            if ($isStrictMode && \count($changeLog) > 0) {
                $errorCode++;
            }
        }

        return $errorCode;
    }

    private function renderOutput(string $outputFormat, array $fullChangeLog, string $env, array $params): void
    {
        AbstractRender::factory($outputFormat, $params)
            ->setFullChangeLog($fullChangeLog)
            ->setEnv($env)
            ->render($this->outputMode->getOutput());
    }
}
