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

class Permission extends AbstractPermission
{
    /**
     * @var array|AbstractPermission[]
     */
    private $childs = [];

    /**
     * @return array|AbstractPermission[]
     */
    public function getChilds() : array
    {
        return $this->childs;
    }

    /**
     * @return $this|self
     */
    public function resetChilds() : self
    {
        $this->childs = [];

        return $this;
    }

    /**
     * @return array|Permission[]
     */
    public function getPermissions() : array
    {
        $return = [];
        foreach ($this->childs as $child) {
            if ($child instanceof self) {
                $return[] = $child;
            }
        }

        return $return;
    }

    /**
     * @return array|Filter[]
     */
    public function getFilters() : array
    {
        $return = [];
        foreach ($this->childs as $child) {
            if ($child instanceof Filter) {
                $return[] = $child;
            }
        }

        return $return;
    }

    /**
     * @param AbstractPermission $child
     *
     * @return $this|self
     */
    public function addChild(AbstractPermission $child) : self
    {
        $this->childs[] = $child;

        return $this;
    }

    /**
     * @param string $key
     * @param array $childs
     */
    public function __construct($key, array $childs = [])
    {
        parent::__construct($key);

        $this->childs = $childs;
    }

    /**
     * @param array $rights
     * @param array $flat
     *
     * @return array|Permission[]
     */
    public static function fromArray(array $rights, array $flat) : array
    {
        $return = [];

        foreach ($flat as $current) {
            $keys = explode('/', $current);
            if ($permission = self::findRecursive($keys, $rights)) {
                $return = self::listMerge($return, [$permission]);
            }
        }

        return $return;
    }

    /**
     * @param array $keys
     * @param array|AbstractPermission[] $permissions
     *
     * @return $this|self|null
     */
    private static function findRecursive(array $keys, array $permissions) : ?self
    {
        $return = $current = null;
        foreach ($keys as $key) {
            foreach ($permissions as $permission) {
                if ($permission->getKey() != $key) {
                    continue;
                }

                if ($permission instanceof self) {
                    if (!$return) {
                        $return = clone $permission;
                        $return->resetChilds();

                        $current = $return;
                        $permissions = $permission->getChilds();
                    } else {
                        $child = (clone $permission)->resetChilds();
                        $current->addChild($child);

                        $current = $child;
                        $permissions = $permission->getChilds();
                    }
                } else {
                    $current->addChild((clone $permission)->setFilters([end($keys)]));
                }
            }
        }

        return $return;
    }

    /**
     * @param Permission $permission
     *
     * @return $this|self
     */
    public function merge(Permission $permission) : self
    {
        // merge filters
        foreach ($permission->getFilters() as $filter) {
            $found = false;
            foreach ($this->getFilters() as $current) {
                if ($current->getKey() == $filter->getKey()) {
                    $found = true;

                    $current->setFilters(array_unique(array_merge(
                        $filter->getFilters(),
                        $current->getFilters()
                    )));
                }
            }

            if (!$found) {
                $this->childs[] = $filter;
            }
        }

        $this->childs = self::listMerge($this->childs, $permission->getPermissions());

        return $this;
    }

    /**
     * @param array|self[] $sources
     * @param array|self[] $merge
     *
     * @return array
     */
    public static function listMerge(array $sources, array $merge) : array
    {
        foreach ($merge as $permission) {
            /** @var Permission $find */
            $find = self::find($permission->getKey(), self::class, $sources);

            if (!$find) {
                $sources[] = $permission;
            } else {
                $find->merge($permission);
            }
        }

        return $sources;
    }

    /**
     * @param string $key
     * @param string $class
     * @param array|AbstractPermission[] $permissions
     *
     * @return null|AbstractPermission
     */
    public static function find(string $key, string $class, array $permissions) : ?AbstractPermission
    {
        /** @var AbstractPermission $permission */
        foreach ($permissions as $permission) {
            if ($permission->getKey() == $key && get_class($permission) == $class) {
                return $permission;
            }
        }

        return null;
    }
}
