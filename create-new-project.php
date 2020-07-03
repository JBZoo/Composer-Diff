<?php

/**
 * JBZoo Toolbox - Composer-Graph
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Composer-Graph
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Composer-Graph
 */

$packageName = isset($GLOBALS["argv"][1]) ? $GLOBALS["argv"][1] : 'Testing';

if (!$packageName || $packageName === '__CHANGE_ME__') {
    echo 'Undefined package name! Plz, check config' . PHP_EOL;
    exit(1);
}

$packageName = ucfirst(trim($packageName));
$namespace = str_replace('-', '', $packageName);

global $config;

$config = [
    'root'    => realpath('.'),
    'exclude' => [
        '.',
        '..',
        '.git',
        '.idea',
        'vendor',
        'build',
        pathinfo(__FILE__, PATHINFO_BASENAME),
    ],
    'defines' => [
        '__PACKAGE__'        => $packageName,
        '__NS__'             => $namespace,
        'jbzoo/skeleton-php' => 'jbzoo/' . strtolower($packageName),
    ],
];


/**********************************************************************************************************************/

/**
 * @param $path
 * @return null|string
 */
function openFile($path)
{
    $contents = null;

    if ($realPath = realpath($path)) {
        $handle = fopen($path, "rb");
        $contents = fread($handle, filesize($path));
        fclose($handle);
    }

    return $contents;
}

/**
 * @param       $dir
 * @param null  $filter
 * @param array $results
 * @return array
 */
function getFileList($dir, $filter = null, &$results = [])
{
    $files = scandir($dir);

    global $config;

    foreach ($files as $key => $value) {
        $path = $dir . DIRECTORY_SEPARATOR . $value;

        if (!is_dir($path) && !in_array($value, $config['exclude'], true)) {
            if ($filter) {
                if (preg_match('#' . $filter . '#iu', $path)) {
                    $results[] = realpath($path);
                }
            } else {
                $results[] = realpath($path);
            }

        } elseif (is_dir($path) && !in_array($value, $config['exclude'], true)) {
            getFileList($path, $filter, $results);
        }
    }

    return $results;
}


/********** Replace all files *****************************************************************************************/
$list = getFileList($config['root']);
foreach ($list as $file) {
    $content = openFile($file);

    foreach ($config['defines'] as $const => $value) {
        $content = str_replace($const, $value, $content);
    }

    if (strpos($file, '.gitignore')) {
        $regexp = '/\n# Cutline.*/ius';
        $content = preg_replace($regexp, '', $content);
    }

    if (strpos($file, '.travis.yml')) {
        $content = str_replace("  - php `pwd`/create-new-project.php Skeleton-Php\n", '', $content);
    }

    file_put_contents($file, $content);
}


/********** Change Readme file ****************************************************************************************/

$map = [
    "src/__NS__.php"                => "src/{$namespace}.php",
    "tests/__NS__Test.php"          => "tests/{$namespace}Test.php",
    "tests/__NS__CodestyleTest.php" => "tests/{$namespace}CodestyleTest.php",
    "tests/__NS__CopyrightTest.php" => "tests/{$namespace}CopyrightTest.php",
    '/README.dist.md'               => 'README.md',
];

foreach ($map as $oldName => $newName) {
    rename("{$config['root']}/{$oldName}", "{$config['root']}/{$newName}");
}


/********** Self-destruction ******************************************************************************************/
@unlink(__FILE__);


/********** Success ***************************************************************************************************/
echo $packageName . ' is ready!' . PHP_EOL;
exit(0);
