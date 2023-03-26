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

use Symfony\Component\Console\Output\OutputInterface;

use function JBZoo\Data\json;

final class JsonOutput extends AbstractRender
{
    protected function renderOneEnv(OutputInterface $output, array $changeLog, string $env): void
    {
        $dataForJson = [$env => []];

        foreach ($changeLog as $diff) {
            $row                             = $diff->toArray();
            $dataForJson[$env][$row['name']] = $row;
        }

        $output->writeln((string)json($dataForJson));
    }
}
