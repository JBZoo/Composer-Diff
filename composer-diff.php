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

$vendorPaths = [
    __DIR__ . '/../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

foreach ($vendorPaths as $file) {
    if (\file_exists($file)) {
        \define('JBZOO_AUTOLOAD_FILE', $file);
        break;
    }
}

if (!defined('JBZOO_AUTOLOAD_FILE')) {
    fwrite(
        STDERR,
        'You need to set up the project dependencies using Composer:' . PHP_EOL . PHP_EOL .
        '    composer install' . PHP_EOL . PHP_EOL .
        'You can learn all about Composer on https://getcomposer.org/.' . PHP_EOL
    );

    die(1);
}

require_once JBZOO_AUTOLOAD_FILE;

$application = new CliApplication('JBZoo/Composer-Diff', '@git-version@');
$application->registerCommandsByPath(__DIR__ . '/src/Commands', __NAMESPACE__);
$application->setDefaultCommand('diff');
$application->run();
