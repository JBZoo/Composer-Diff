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

use Symfony\Component\Console\Output\OutputInterface;

use function JBZoo\Data\json;

/**
 * Class JsonOutput
 * @package JBZoo\ComposerDiff\Renders
 */
final class JsonOutput extends AbstractRender
{
    /**
     * @inheritDoc
     */
    protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void
    {
        $dataForJson = [$env => []];

        foreach ($changeLog as $diff) {
            $row = $diff->toArray();
            $dataForJson[$env][$row['name']] = $row;
        }

        $output->writeln((string)json($dataForJson));
    }
}
