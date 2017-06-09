<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Acl;

abstract class AbstractPermission
{
    /**
     * @var string
     */
    private $key;

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this|self
     */
    public function setKey(string $key) : self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }
}
