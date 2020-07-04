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

use JBZoo\ComposerDiff\Diff;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Console
 * @package JBZoo\ComposerDiff
 */
class Console extends AbstractRender
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void
    {
        $showLinks = $this->showLinks();

        $output->writeln("  <blue>{$this->getTitle($env)}</blue> ({$env})");

        $table = (new Table($output))
            ->setColumnStyle(2, (new TableStyle())->setPadType(STR_PAD_LEFT))
            ->setColumnStyle(3, (new TableStyle())->setPadType(STR_PAD_LEFT));

        if ($showLinks) {
            $table->setHeaders(['Package', 'Action', 'Old Version', 'New Version', 'Details']);
        } else {
            $table->setHeaders(['Package', 'Action', 'Old Version', 'New Version']);
        }

        foreach ($changeLog as $diff) {
            $row = $diff->toArray();

            $mode = $row['mode'];

            if ($mode === Diff::MODE_NEW) {
                $mode = "<green>{$mode}</green>";
            } elseif ($mode === Diff::MODE_REMOVED) {
                $mode = "<red>{$mode}</red>";
            } elseif ($mode === Diff::MODE_UPGRADED) {
                $mode = "<yellow>{$mode}</yellow>";
            } elseif ($mode === Diff::MODE_DOWNGRADED) {
                $mode = "<cyan>{$mode}</cyan>";
            } elseif ($mode === Diff::MODE_CHANGED) {
                $mode = "<cyan>{$mode}</cyan>";
            }

            $fromVersion = $row['version_from'] ?: '-';
            $toVersion = $row['version_to'] ?: '-';

            if ($showLinks) {
                $table->addRow([$row['name'], $mode, $fromVersion, $toVersion, $row['compare']]);
            } else {
                $table->addRow([$row['name'], $mode, $fromVersion, $toVersion]);
            }
        }

        $table->render();
        $output->writeln(' ');
    }
}
