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

namespace JBZoo\ComposerDiff\Renders;

use JBZoo\ComposerDiff\Comparator;
use JBZoo\ComposerDiff\Diff;
use JBZoo\ComposerDiff\Exception;
use JBZoo\Data\Data;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRender
{
    public const CONSOLE  = 'console';
    public const MARKDOWN = 'markdown';
    public const JSON     = 'json';

    /** @var Diff[] */
    protected array $fullChangeLog = [];

    protected string $env = Comparator::ENV_BOTH;

    protected Data $params;

    /**
     * @param Diff[] $changeLog
     */
    abstract protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void;

    public function __construct(array $params)
    {
        $this->params = new Data(\array_merge([
            'show-links'  => true,
            'strict-mode' => false,
        ], $params));
    }

    /**
     * @param  Diff[] $fullChangeLog
     * @return $this
     */
    public function setFullChangeLog(array $fullChangeLog): self
    {
        $this->fullChangeLog = $fullChangeLog;

        return $this;
    }

    /**
     * @return $this
     */
    public function setEnv(string $env): self
    {
        $this->env = $env;

        return $this;
    }

    public function render(OutputInterface $output): bool
    {
        if (\count($this->fullChangeLog) === 0) {
            $output->writeln("There is no difference ({$this->env})");

            return false;
        }

        if (\in_array($this->env, [Comparator::ENV_BOTH, Comparator::ENV_PROD], true)) {
            $this->renderOneEnv($output, $this->fullChangeLog, Comparator::ENV_PROD);
        }

        if (\in_array($this->env, [Comparator::ENV_BOTH, Comparator::ENV_DEV], true)) {
            $this->renderOneEnv($output, $this->fullChangeLog, Comparator::ENV_DEV);
        }

        return true;
    }

    public static function factory(string $outputFormat, array $params): self
    {
        $outputFormat = \strtolower(\trim($outputFormat));

        if ($outputFormat === self::CONSOLE) {
            return new Console($params);
        }

        if ($outputFormat === self::MARKDOWN) {
            return new Markdown($params);
        }

        if ($outputFormat === self::JSON) {
            return new JsonOutput($params);
        }

        throw new Exception("Output format \"{$outputFormat}\" not found");
    }

    protected function showLinks(): bool
    {
        return (bool)$this->params->get('show-links');
    }

    /**
     * @phan-suppress PhanPluginPossiblyStaticProtectedMethod
     */
    protected function getTitle(string $env): string
    {
        return $env === Comparator::ENV_PROD
            ? 'PHP Production Dependencies'
            : 'PHP Dev Dependencies';
    }
}
