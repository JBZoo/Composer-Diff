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

use JBZoo\Markdown\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class Markdown extends AbstractRender
{
    /**
     * {@inheritDoc}
     */
    protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void
    {
        $table = new Table();
        $table->setMinCellLength(3);

        if ($this->showLinks()) {
            $table
                ->setHeaders(['Package', 'Action', 'Old Version', 'New Version', ''])
                ->setAlignments([
                    Table::ALIGN_LEFT,
                    Table::ALIGN_LEFT,
                    Table::ALIGN_RIGHT,
                    Table::ALIGN_RIGHT,
                    Table::ALIGN_LEFT,
                ]);
        } else {
            $table
                ->setHeaders(['Package', 'Action', 'Old Version', 'New Version'])
                ->setAlignments([
                    Table::ALIGN_LEFT,
                    Table::ALIGN_LEFT,
                    Table::ALIGN_RIGHT,
                    Table::ALIGN_RIGHT,
                ]);
        }

        foreach ($changeLog as $diff) {
            $row = $diff->toArray();

            $fromVersion = $row['version_from'] ?: '-';
            $toVersion   = $row['version_to'] ?: '-';

            if ($this->showLinks()) {
                $table->appendRow([
                    self::getLink($row['name'], $row['url']),
                    $row['mode'],
                    $fromVersion,
                    $toVersion,
                    self::getLink('See details', $row['compare']),
                ]);
            } else {
                $table->appendRow([$row['name'], $row['mode'], $fromVersion, $toVersion]);
            }
        }

        $output->writeln("## {$this->getTitle($env)} ({$env})");
        $output->writeln('');
        $output->write($table->render());
        $output->writeln('');
    }

    protected static function getLink(string $title, ?string $url = null): string
    {
        return $url ? "[{$title}]({$url})" : '';
    }
}
