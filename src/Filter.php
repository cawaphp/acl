<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Cawa\Acl;

class Filter extends AbstractPermission
{
    /**
     * @var array|AbstractPermission[]
     */
    private $filters = [];

    /**
     * @return array|AbstractPermission[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array|AbstractPermission[] $filters
     *
     * @return $this|self
     */
    public function setFilters($filters) : self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param string $key
     * @param array $filters
     */
    public function __construct($key, array $filters = [])
    {
        parent::__construct($key);

        $this->filters = $filters;
    }
}
