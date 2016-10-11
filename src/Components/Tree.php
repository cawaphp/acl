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
use Cawa\Intl\TranslatorFactory;

class Tree extends \Cawa\Bootstrap\Forms\ExtendedFields\Tree
{
    use TranslatorFactory;

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
     * @param $key
     *
     * @return string
     */
    private function translate($key) : string
    {
        $explode = explode('/', $key);
        $trans = array_pop($explode);

        return self::trans('rights.' . $trans);
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
            $key = implode('/', array_merge($rightKey, [$right->getKey()]));
            $item = (new TreeItem('_' . $key, $this->translate($key)));

            if ($right->getFilters()) {
                foreach ($right->getFilters() as $filter) {
                    $key = implode('/', array_merge($rightKey, [$filter->getKey()]));
                    $child = (new TreeItem('_' . $key, $this->translate($key)));

                    foreach ($filter->getFilters() as $value) {
                        $key = implode('/', array_merge(
                            $rightKey,
                            [$right->getKey(), $filter->getKey(), $value]
                        ));
                        $child->addChildren(new TreeItem($key, $this->translate($key)));
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
                $key = implode('/', array_merge($rightKey, [$right->getKey()]));
                $return[] = new TreeItem($key, $this->translate($key));
            }
        }

        return $return;
    }
}
