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

use function JBZoo\Data\data;

final class ComposerLock
{
    /** @var Package[] */
    private array $listRequired = [];

    /** @var Package[] */
    private array $listRequiredDev = [];

    public function __construct(array $composerLockData)
    {
        $data = data($composerLockData);

        foreach ((array)$data->get('packages') as $packageData) {
            $package                                 = new Package($packageData);
            $this->listRequired[$package->getName()] = $package;
        }

        foreach ((array)$data->get('packages-dev') as $packageData) {
            $package                                    = new Package($packageData);
            $this->listRequiredDev[$package->getName()] = $package;
        }
    }

    /**
     * @return Package[]
     */
    public function getRequired(): array
    {
        return $this->listRequired;
    }

    /**
     * @return Package[]
     */
    public function getRequiredDev(): array
    {
        return $this->listRequiredDev;
    }
}
