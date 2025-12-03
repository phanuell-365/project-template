<?php

namespace Config;

use App\Services\GroupsService;
use App\Services\PackagesService;
use App\Services\PermissionsService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function permissions_service($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('permissions_service');
        }

        return new PermissionsService();
    }

    public static function groups_service($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('groups_service');
        }

        return new GroupsService();
    }

    public static function packages_service($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('packages_service');
        }

        return new PackagesService();
    }
}
