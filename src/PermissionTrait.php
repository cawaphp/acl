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

trait PermissionTrait
{
    /**
     * @var array|Permission[]
     */
    protected $permissions = [];

    /**
     * @return array|Permission[]
     */
    protected function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param array|Permission[] $permissions
     *
     * @return $this
     */
    protected function setPermissions($permissions) : self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @param array|Permission[] $permissions
     *
     * @return $this
     */
    protected function addPermissions($permissions) : self
    {
        $this->permissions = Permission::listMerge($this->permissions, $permissions);

        return $this;
    }

    /**
     * @param string $path
     * @param array $filters
     *
     * @return bool
     */
    public function isAllowed(string $path, array $filters = []) : bool
    {
        $keys = explode('/', $path);

        $findFilters = [];
        $permissions = $this->permissions;
        foreach ($keys as $key) {
            if ($key == "*") {
                break;
            }

            /** @var Permission $permission */
            $permission = Permission::find($key, Permission::class, $permissions);
            if (!$permission) {
                return false;
            }

            $findFilters = array_merge($findFilters, $permission->getFilters());
            $permissions = $permission->getPermissions();
        }

        if (sizeof($filters)) {
            foreach ($filters as $key => $value) {
                /** @var Filter $filter */
                $filter = Permission::find($key, Filter::class, $findFilters);
                if ($filter) {
                    if (array_search($value, $filter->getFilters()) === false) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}

