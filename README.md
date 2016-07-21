# Сáша Acl
Simple ACL with permission & filters
-----

## Warning
Be aware that this package is still in heavy developpement.
Some breaking change will occure. Thank's for your comprehension.

## Features
* Recursive permission with simple format : key1/key2/key3
* Can have filter on permission ion order to limit some permissions depending on context 
* Merge permissions for multiple role with `addPermissions`
* Simple php array that can be serializes to database for persistent storage 
 
## Basic Usage

```php
class Role 
{
    use PermissionTrait;
    
    public function __construct()
    {
        $this->addPermissions([
            new Permission('app', [
                new Filter('app', [1]),
                new Permission('user', [
                    new Permission('group', [
                        new Permission('read'),
                        new Permission('create'),
                        new Permission('update'),
                        new Permission('delete'),
                    ]),
                ])
            ])
        ]);
        
        $this->addPermissions([
            new Permission('app', [
                new Filter('app', [2]),
                new Permission('user', [
                    new Permission('role', [
                        new Permission('read'),
                    ])
                ])
            ])
        ]);
    }
}

$role = new Role();

// Permissions
var_dump($role->isAllowed('app/user/group/read'); // true
var_dump($role->isAllowed('app/user/role/read'); // true
var_dump($role->isAllowed('app/user/role/create'); // false
var_dump($role->isAllowed('app/user/*'); // true
var_dump($role->isAllowed('app/user/permision/*'); // false

// Filters
var_dump($role->isAllowed('app', ['app' => 1])); // true
var_dump($role->isAllowed('app/user/role/read', ['app' => 1])); // true (filter are inherited)
var_dump($role->isAllowed('app/user/role/read', ['app' => 2])); // false
var_dump($role->isAllowed('app/user/role/read', ['unknown' => 1])); // true (no filter found mean all available)
```

## About

### License

Cawa is licensed under the GPL v3 License - see the `LICENSE` file for details
