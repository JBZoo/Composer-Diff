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

namespace JBZoo\PHPUnit;

use JBZoo\ComposerDiff\ComposerDiff;

/**
 * Class ComposerDiffTest
 * @package JBZoo\PHPUnit
 */
class ComposerDiffTest extends PHPUnit
{
    public function testShouldDoSomeStreetMagic()
    {
        $obj = new ComposerDiff();
        isSame('street magic', $obj->doSomeStreetMagic());
    }
}
