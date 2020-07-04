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

namespace JBZoo\ComposerDiff;

use function JBZoo\Data\data;

/**
 * Class PackageCollection
 * @package JBZoo\ComposerDiff
 */
class CommploserLock
{
    /**
     * @var Package[]
     */
    private $listRequired = [];

    /**
     * @var Package[]
     */
    private $listRequiredDev = [];

    /**
     * PackageCollection constructor.
     * @param array $composerLockData
     */
    public function __construct(array $composerLockData)
    {
        $data = data($composerLockData);

        foreach ((array)$data->get('packages') as $packageData) {
            $package = new Package($packageData);
            $this->listRequired[$package->getName()] = $package;
        }

        foreach ((array)$data->get('packages-dev') as $packageData) {
            $package = new Package($packageData);
            $this->listRequiredDev[$package->getName()] = $package;
        }
    }

    /**
     * @return Package[]
     */
    public function getRequired()
    {
        return $this->listRequired;
    }

    /**
     * @return Package[]
     */
    public function getRequiredDev()
    {
        return $this->listRequiredDev;
    }
}
