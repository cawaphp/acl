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

namespace Cawa\Acl\Components;

use Cawa\Acl\Permission;
use Cawa\Bootstrap\Forms\ExtendedFields\TreeItem;

class Tree extends \Cawa\Bootstrap\Forms\ExtendedFields\Tree
{
    /**
     * @var Permission[]
     */
    public $rights;

    /**
     * @param array|Permission[] $rights
     * @param string $name
     * @param null $label
     */
    public function __construct(array $rights, $name, $label = null)
    {
        $this->rights = $rights;
        $this->setData($this->generateData($rights));

        parent::__construct($name, $label);
    }

    /**
     * @param array|Permission[] $rights
     * @param array $rightKey
     *
     * @return array
     */
    private function generateData(array $rights, array $rightKey = []) : array
    {
        $return = [];
        foreach ($rights as $right) {
            $item = (new TreeItem("_" . $right->getKey()));

            if ($right->getFilters()) {
                foreach ($right->getFilters() as $filter) {
                    $child = (new TreeItem("_" . $filter->getKey()));

                    foreach ($filter->getFilters() as $value) {
                        $child->addChildren(new TreeItem(implode('/', array_merge(
                            $rightKey,
                            [$right->getKey(), $filter->getKey(), $value]
                         ))));
                    }

                    $item->addChildren($child);
                }
            }

            if ($right->getPermissions()) {
                $childs = $this->generateData($right->getPermissions(), array_merge($rightKey, [$right->getKey()]));
                foreach ($childs as $child) {
                    $item->addChildren($child);
                }

                $return[] = $item;
            } else {
                $return[] = new TreeItem(implode('/', array_merge($rightKey, [$right->getKey()])));
            }


        }

        return $return;
    }
}

