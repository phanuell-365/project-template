<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Services;

class PermissionsService extends BaseService
{
    protected BaseConnection $db;
    protected CacheInterface $cache;
    protected int $ttl = 3600; // Cache for 1 hour

    public function __construct()
    {
        $this->db = Database::connect();
        $this->cache = Services::cache();
    }

    public function getUserPermissions(int $userId, int $orgId): array
    {
        $cacheKey = "user_permissions_{$userId}_{$orgId}";
        $permissions = $this->cache->get($cacheKey);

        if (!is_array($permissions)) {
            $sql = "
            SELECT p.id AS permission_id, p.name, p.description, p.uri, p.is_parent, p.parent_id, p.icon, p.order, p.slug
                FROM permissions p
                -- Join matches Permissions to User Groups
                INNER JOIN group_permissions gp ON p.id = gp.permission_id
                -- Join matches User Groups to Users
                INNER JOIN user_groups ug ON gp.group_id = ug.group_id
                -- Join matches Permissions to Organization Packages
                INNER JOIN package_permissions pp ON p.id = pp.permission_id
                -- Join matches Organization Packages to Organizations
                INNER JOIN organizations o ON pp.package_id = o.package_id
            WHERE ug.user_id = :user_id:
              AND o.id = :org_id:
              AND p.deleted_at IS NULL
              AND ug.deleted_at IS NULL
              AND gp.deleted_at IS NULL
              AND pp.deleted_at IS NULL
            ";

            $query = $this->db->query($sql, [
                'user_id' => $userId,
                'org_id'  => $orgId,
            ]);

            $permissions = $query->getResultArray();

            // Store in cache
            $this->cache->save($cacheKey, $permissions, $this->ttl);
        }

        return $permissions;
    }

    public function clearUserPermissionsCache(int $userId, int $orgId): void
    {
        $cacheKey = "user_permissions_{$userId}_{$orgId}";
        $this->cache->delete($cacheKey);
    }

    public function canAccessRoute(int $userId, int $orgId, string $route): bool
    {
        $permissions = $this->getUserPermissions($userId, $orgId);

        // log permissions for debugging

//        log_message('debug', 'User Permissions {permissions}', [
//            'permissions' => print_r($permissions, true)
//        ]);

        // log user id
        log_message('debug', 'Checking route access for user ID: {userId}, org ID: {orgId}, route: {route}', [
            'userId' => $userId,
            'orgId'  => $orgId,
            'route'  => $route
        ]);

        foreach ($permissions as $permission) {
            if ($permission['uri'] === $route) {

                // set the current permission in session for later use
                session()->set('current_permission', $permission);

                return true;
            }
        }

        return false;
    }

    public function buildSidebarTree(array $permissions): array
    {
        $tree = [];
        $lookup = [];

        // First, create a lookup table
        foreach ($permissions as $permission) {
            $permission['children'] = [];
            $lookup[$permission['permission_id']] = $permission;
        }

        // Then, build the tree structure
//        foreach ($lookup as $id => $permission) {
//            if ($permission['is_parent']) {
//                $tree[] = &$lookup[$id];
//            } else {
//                $parentId = $permission['parent_id'];
//                if (isset($lookup[$parentId])) {
//                    $lookup[$parentId]['children'][] = &$lookup[$id];
//                }
//            }
//        }
        // Also exclude permissions which do not have an order defined, they are not meant to be in the sidebar
        foreach ($lookup as $id => $permission) {
            if ($permission['is_parent'] && $permission['order'] !== null) {
                $tree[] = &$lookup[$id];
            } else {
                $parentId = $permission['parent_id'];
                if (isset($lookup[$parentId]) && $permission['order'] !== null) {
                    $lookup[$parentId]['children'][] = &$lookup[$id];
                }
            }
        }

        $org_slug = session()->get('org_slug');

        // Add full URL to each permission that has a URI except for '#'
        $addFullUrl = function (&$nodes) use ($org_slug, &$addFullUrl) {
            foreach ($nodes as &$node) {
                if ($node['uri'] && $node['uri'] !== '#') {
                    $node['full_url'] = "/{$org_slug}/" . ltrim($node['uri'], '/');
                } else {
                    $node['full_url'] = '#';
                }
                if (!empty($node['children'])) {
                    $addFullUrl($node['children']);
                }
            }
        };

        $addFullUrl($tree);

        // Finally, sort the tree based on the 'order' field
        $sortTree = function (&$nodes) use (&$sortTree) {
            usort($nodes, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
            foreach ($nodes as &$node) {
                if (!empty($node['children'])) {
                    $sortTree($node['children']);
                }
            }
        };

        $sortTree($tree);

        // log the tree for debugging
        log_message('debug', 'Sidebar Tree: {tree}', [
            'tree' => print_r($tree, true)
        ]);

        return $tree;
    }
}