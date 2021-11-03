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

namespace JBZoo\PHPUnit;

/**
 * Class Composer-DiffReadmeTest
 *
 * @package JBZoo\PHPUnit
 */
final class ComposerDiffReadmeTest extends AbstractReadmeTest
{
    /**
     * @var string
     */
    protected $packageName = 'Composer-Diff';

    protected function setUp(): void
    {
        parent::setUp();

        $this->params['strict_types'] = true;
        $this->params['travis'] = false;
    }
}
