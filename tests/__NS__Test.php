<?php

/**
 * JBZoo Toolbox - __PACKAGE__
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    __PACKAGE__
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/__PACKAGE__
 */

namespace JBZoo\PHPUnit;

use JBZoo\__NS__\__NS__;

/**
 * Class __NS__Test
 * @package JBZoo\PHPUnit
 */
class __NS__Test extends PHPUnit
{
    public function testShouldDoSomeStreetMagic()
    {
        $obj = new __NS__();
        isSame('street magic', $obj->doSomeStreetMagic());
    }
}
