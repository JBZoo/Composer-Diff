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

declare(strict_types=1);

namespace JBZoo\ComposerDiff\Renders;

use JBZoo\ComposerDiff\Exception;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Markdown
 * @package JBZoo\ComposerDiff\Renders
 */
final class Markdown extends AbstractRender
{
    public const A_LEFT   = 'Left';
    public const A_CENTER = 'Center';
    public const A_RIGHT  = 'Right';

    public const CELL_MIN_LENGTH = 3;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var string[]
     */
    private $alignments = [];

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @inheritDoc
     */
    protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void
    {
        if ($this->showLinks()) {
            $this->headers = ['Package', 'Action', 'Old Version', 'New Version', ''];
            $this->alignments = [self::A_LEFT, self::A_LEFT, self::A_RIGHT, self::A_RIGHT, self::A_LEFT];
        } else {
            $this->headers = ['Package', 'Action', 'Old Version', 'New Version'];
            $this->alignments = [self::A_LEFT, self::A_LEFT, self::A_RIGHT, self::A_RIGHT];
        }


        $this->rows = [];
        foreach ($changeLog as $diff) {
            $row = $diff->toArray();

            $fromVersion = $row['version_from'] ?: '-';
            $toVersion = $row['version_to'] ?: '-';

            if ($this->showLinks()) {
                $this->rows[] = [
                    self::getLink($row['name'], $row['url']),
                    $row['mode'],
                    $fromVersion,
                    $toVersion,
                    self::getLink('See details', $row['compare']),
                ];
            } else {
                $this->rows[] = [$row['name'], $row['mode'], $fromVersion, $toVersion];
            }
        }

        $widths = $this->calculateWidths();

        $output->writeln("## {$this->getTitle($env)} ({$env})");
        $output->writeln('');
        $output->write($this->renderHeaders($widths));
        $output->write($this->renderRows($widths));
        $output->writeln('');
    }

    /**
     * @return int[]
     */
    protected function calculateWidths(): array
    {
        $widths = [];

        foreach (array_merge([$this->headers], $this->rows) as $row) {
            $max = count($row);

            for ($colIndex = 0; $colIndex < $max; $colIndex++) {
                $iWidth = strlen((string)$row[$colIndex]);

                if ((!array_key_exists($colIndex, $widths)) || $iWidth > $widths[$colIndex]) {
                    $widths[$colIndex] = $iWidth;
                }
            }
        }

        // all columns must be at least 3 wide for the markdown to work
        $widths = array_map(static function (int $width) {
            return $width >= self::CELL_MIN_LENGTH ? $width : self::CELL_MIN_LENGTH;
        }, $widths);

        return $widths;
    }

    /**
     * @param string      $title
     * @param string|null $url
     * @return string
     */
    protected static function getLink(string $title, ?string $url = null): string
    {
        return $url ? "[$title]($url)" : '';
    }

    /**
     * @param int[] $widths
     * @return string
     */
    protected function renderHeaders(array $widths): string
    {
        $result = '| ';

        foreach (array_keys($this->headers) as $colIndex) {
            $result .= self::renderCell(
                $this->headers[$colIndex],
                $this->getColumnAlign($colIndex),
                $widths[$colIndex]
            );

            $result .= ' | ';
        }

        $result = rtrim($result, ' ') . PHP_EOL . $this->renderAlignments($widths) . PHP_EOL;

        return $result;
    }

    /**
     * @param int[] $widths
     * @return string
     */
    protected function renderRows(array $widths): string
    {
        $result = '';

        foreach ($this->rows as $row) {
            $result .= '| ';

            /** @var string $colIndex */
            foreach (array_keys($row) as $colIndex) {
                $result .= self::renderCell($row[$colIndex], $this->getColumnAlign($colIndex), $widths[$colIndex]);
                $result .= ' | ';
            }

            $result = rtrim($result, ' ') . PHP_EOL;
        }

        return $result;
    }

    /**
     * @param string $contents
     * @param string $alignment
     * @param int    $width
     * @return string
     */
    protected static function renderCell(string $contents, string $alignment, int $width): string
    {
        $map = [
            self::A_LEFT   => STR_PAD_RIGHT,
            self::A_CENTER => STR_PAD_BOTH,
            self::A_RIGHT  => STR_PAD_LEFT,
        ];

        $padType = $map[$alignment] ?? STR_PAD_LEFT;

        return str_pad($contents, $width, ' ', $padType);
    }

    /**
     * @param int[] $widths
     * @return string
     */
    protected function renderAlignments(array $widths): string
    {
        $row = '|';

        foreach ($widths as $colIndex => $colIndexValue) {
            $cell = str_repeat('-', $colIndexValue + 2);
            $align = $this->getColumnAlign($colIndex);

            if ($align === self::A_CENTER) {
                $cell = ':' . substr($cell, 2) . ':';
            }

            if ($align === self::A_RIGHT) {
                $cell = substr($cell, 1) . ':';
            }

            if ($align === self::A_LEFT) {
                $cell = ':' . substr($cell, 1);
            }

            $row .= $cell . '|';
        }

        return $row;
    }

    /**
     * @param string|int $colIndex
     * @return string
     */
    protected function getColumnAlign($colIndex): string
    {
        $validAligns = [self::A_LEFT, self::A_CENTER, self::A_RIGHT];
        $result = $this->alignments[$colIndex] ?? self::A_LEFT;

        if (!in_array($result, $validAligns, true)) {
            throw new Exception("Invalid alignment for column index {$colIndex}: {$result}");
        }

        return $result;
    }
}
