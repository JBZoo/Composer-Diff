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

namespace JBZoo\ComposerDiff\Commands;

use JBZoo\ComposerDiff\Comparator;
use JBZoo\ComposerDiff\Renders\AbstractRender;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Build
 * @package JBZoo\ComposerDiff\Commands
 */
class DiffAction extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $envs = implode(', ', [Comparator::ENV_BOTH, Comparator::ENV_PROD, Comparator::ENV_DEV]);
        $outputFormats = implode(', ', [AbstractRender::CONSOLE, AbstractRender::MARKDOWN, AbstractRender::JSON]);

        $this
            ->setName('diff')
            ->setDescription('Show difference between two versions of composer.lock files')
            // File paths
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'The file, git ref, or git ref with filename ' .
                'to compare FROM', 'HEAD:composer.lock')
            ->addOption('target', null, InputOption::VALUE_REQUIRED, 'The file, git ref, or git ref with filename ' .
                'to compare TO', './composer.lock')
            // Options
            ->addOption('env', null, InputOption::VALUE_REQUIRED, "Show only selected environment. " .
                "Available options: <info>{$envs}</info>", Comparator::ENV_BOTH)
            ->addOption('output', null, InputOption::VALUE_REQUIRED, "Output format. " .
                "Available options: <info>{$outputFormats}</info>", AbstractRender::CONSOLE)
            ->addOption('strict', null, InputOption::VALUE_NONE, 'Return exit code if you have some difference');
    }

    /**
     * @inheritDoc
     * @phan-suppress PhanUnusedProtectedMethodParameter
     */
    protected function runCommand(InputInterface $input, OutputInterface $output): int
    {
        $sourcePath = (string)$this->opt('source');
        $targetPath = (string)$this->opt('target');
        $outputFormat = strtolower((string)$this->opt('output'));
        $env = strtolower((string)$this->opt('env'));
        $isStrictMode = (bool)$this->opt('strict');

        $fullChangeLog = Comparator::compare($sourcePath, $targetPath);

        $errorCode = 0;

        if (in_array($env, [Comparator::ENV_BOTH, Comparator::ENV_PROD], true)) {
            $changeLog = $fullChangeLog[Comparator::ENV_PROD];
            $this->renderOutput($outputFormat, $changeLog, Comparator::ENV_PROD);
            if ($isStrictMode && count($changeLog) > 0) {
                $errorCode++;
            }
        }

        if (in_array($env, [Comparator::ENV_BOTH, Comparator::ENV_DEV], true)) {
            $changeLog = $fullChangeLog[Comparator::ENV_DEV];
            $this->renderOutput($outputFormat, $changeLog, Comparator::ENV_DEV);
            if ($isStrictMode && count($changeLog) > 0) {
                $errorCode++;
            }
        }

        return $errorCode;
    }

    /**
     * @param string $outputFormat
     * @param array  $fullChangeLog
     * @param string $env
     * @return bool
     */
    private function renderOutput(string $outputFormat, array $fullChangeLog, string $env): bool
    {
        return AbstractRender::factory($outputFormat)
            ->setFullChangeLog($fullChangeLog)
            ->setEnv($env)
            ->render($this->output);
    }
}
