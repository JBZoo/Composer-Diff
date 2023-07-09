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

namespace JBZoo\ComposerDiff;

use JBZoo\Cli\CliApplication;

const PATH_ROOT = __DIR__;

$cwd = isset($_SERVER['PWD']) && \is_dir($_SERVER['PWD']) ? $_SERVER['PWD'] : \getcwd();

// See https://getcomposer.org/doc/articles/vendor-binaries.md#finding-the-composer-autoloader-from-a-binary
if ((isset($_composer_autoload_path) && \file_exists($autoloadFile = $_composer_autoload_path))
    || \file_exists($autoloadFile = __DIR__ . '/../../autoload.php')
    || \file_exists($autoloadFile = __DIR__ . '/../autoload.php')
    || \file_exists($autoloadFile = __DIR__ . '/vendor/autoload.php')
) {
    \define('JBZOO_AUTOLOAD_FILE', $autoloadFile);
} else {
    throw new \RuntimeException("Could not locate autoload.php. cwd is {$cwd}; __DIR__ is " . __DIR__);
}

require_once JBZOO_AUTOLOAD_FILE;

$application = new CliApplication('JBZoo/Composer-Diff', '@git-version@');
$application->registerCommandsByPath(__DIR__ . '/src/Commands', __NAMESPACE__);
$application->setDefaultCommand('diff');

$application->run();
