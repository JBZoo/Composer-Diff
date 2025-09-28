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

if (!\defined('JBZOO_AUTOLOAD_FILE')) {
    \fwrite(
        \STDERR,
        'You need to set up the project dependencies using Composer:' . \PHP_EOL . \PHP_EOL
        . '    composer install' . \PHP_EOL . \PHP_EOL
        . 'You can learn all about Composer on https://getcomposer.org/.' . \PHP_EOL,
    );

    exit(1);
}

require_once JBZOO_AUTOLOAD_FILE;

$application = new CliApplication('JBZoo/Composer-Diff', '@git-version@');
$application->registerCommandsByPath(__DIR__ . '/src/Commands', __NAMESPACE__);
$application->setDefaultCommand('diff');

$application->run();
