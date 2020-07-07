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

namespace JBZoo\ComposerDiff\Renders;

use JBZoo\ComposerDiff\Comparator;
use JBZoo\ComposerDiff\Diff;
use JBZoo\ComposerDiff\Exception;
use JBZoo\Data\Data;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractRender
 * @package JBZoo\ComposerDiff
 */
abstract class AbstractRender
{
    public const CONSOLE  = 'console';
    public const MARKDOWN = 'markdown';
    public const JSON     = 'json';

    /**
     * @var Diff[]
     */
    protected $fullChangeLog = [];

    /**
     * @var string
     */
    protected $env = Comparator::ENV_BOTH;

    /**
     * @var Data
     */
    protected $params;

    /**
     * AbstractRender constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = new Data(array_merge([
            'show-links'  => true,
            'strict-mode' => false,
        ], $params));
    }

    /**
     * @param string $outputFormat
     * @param array  $params
     * @return AbstractRender
     */
    public static function factory(string $outputFormat, array $params): self
    {
        $outputFormat = strtolower(trim($outputFormat));

        if (self::CONSOLE === $outputFormat) {
            return new Console($params);
        }

        if (self::MARKDOWN === $outputFormat) {
            return new Markdown($params);
        }

        if (self::JSON === $outputFormat) {
            return new JsonOutput($params);
        }

        throw new Exception("Output format \"{$outputFormat}\" not found");
    }

    /**
     * @param Diff[] $fullChangeLog
     * @return $this
     */
    public function setFullChangeLog(array $fullChangeLog): self
    {
        $this->fullChangeLog = $fullChangeLog;
        return $this;
    }

    /**
     * @param string $env
     * @return $this
     */
    public function setEnv(string $env): self
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return bool
     */
    protected function showLinks(): bool
    {
        return (bool)$this->params->get('show-links');
    }

    /**
     * @param string $env
     * @return string
     * @phan-suppress PhanPluginPossiblyStaticProtectedMethod
     */
    protected function getTitle(string $env): string
    {
        return $env === Comparator::ENV_PROD
            ? 'PHP Production Dependencies'
            : 'PHP Dev Dependencies';
    }

    /**
     * @param OutputInterface $output
     * @return bool
     */
    public function render(OutputInterface $output): bool
    {
        if (count($this->fullChangeLog) === 0) {
            $output->writeln("There is no difference ({$this->env})");
            return false;
        }

        if (in_array($this->env, [Comparator::ENV_BOTH, Comparator::ENV_PROD], true)) {
            $this->renderOneEnv($output, $this->fullChangeLog, Comparator::ENV_PROD);
        }

        if (in_array($this->env, [Comparator::ENV_BOTH, Comparator::ENV_DEV], true)) {
            $this->renderOneEnv($output, $this->fullChangeLog, Comparator::ENV_DEV);
        }

        return true;
    }

    /**
     * @param OutputInterface $output
     * @param Diff[]          $changeLog
     * @param string          $env
     */
    abstract protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void;
}
