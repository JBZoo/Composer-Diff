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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 * @package JBZoo\ComposerDiff\Commands
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var InputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $input;

    /**
     * @var OutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $output;

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $output->getFormatter();

        $colors = ['black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white', 'default'];
        foreach ($colors as $color) {
            $formatter->setStyle($color, new OutputFormatterStyle($color));
        }

        $this->input = $input;
        $this->output = $output;

        return $this->runCommand($input, $output);
    }

    /**
     * @param string $optionName
     * @return string|null
     * @phan-suppress PhanPartialTypeMismatchReturn
     * @phan-suppress PhanCoalescingNeverUndefined
     */
    protected function opt(string $optionName): ?string
    {
        /** @var string|null $result */
        $result = $this->input->getOption($optionName);

        return $result ?? null;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    abstract protected function runCommand(InputInterface $input, OutputInterface $output): int;
}
