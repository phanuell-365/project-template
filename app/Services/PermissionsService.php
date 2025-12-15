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

    public function getPackagePermissions($package_id)
    {
        $sql = "
            SELECT p.id permission_id, p.name, p.slug, p.description
            FROM permissions p
            INNER JOIN package_permissions pp ON p.id = pp.permission_id
            WHERE pp.package_id = :package_id:
              AND p.deleted_at IS NULL
              AND pp.deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'package_id' => $package_id,
        ]);

        // Log the last query for debugging
//        log_message('debug', 'Last Query: {query}', [
//            'query' => $this->db->getLastQuery()
//        ]);

        return $query->getResultArray();
    }

    public function listAllPermissions()
    {
        $sql = "
            SELECT p.id AS permission_id, p.name, p.description, p.uri, p.is_parent, p.parent_id, p.icon, p.order, p.slug, p.context
            FROM permissions p
            WHERE p.deleted_at IS NULL
            ORDER BY p.order ASC
        ";

        $query = $this->db->query($sql);

        return $query->getResultArray();
    }

    public function getPermissionsHierarchy(): array
    {
        // Fetch the parents
        $sqlParents = "
            SELECT id AS permission_id, name, description, uri, is_parent, parent_id, icon, `order`, slug, context
            FROM permissions
            WHERE is_parent = 1
              AND deleted_at IS NULL
            ORDER BY `order` ASC
        ";

        $queryParents = $this->db->query($sqlParents);

        $parents = $queryParents->getResultArray();

        // For each parent, fetch its children
        foreach ($parents as &$parent) {
            $sqlChildren = "
                SELECT id AS permission_id, name, description, uri, is_parent, parent_id, icon, `order`, slug, context
                FROM permissions
                WHERE parent_id = :parent_id:
                  AND deleted_at IS NULL
                ORDER BY `order` ASC
            ";
            $queryChildren = $this->db->query($sqlChildren, [
                'parent_id' => $parent['permission_id'],
            ]);
            $children = $queryChildren->getResultArray();
            $parent['children'] = $children;
        }

        return $parents;
    }

    public function getPermissionsHierarchyForPackage(int $packageId): array
    {
        // We'll build the hierarchy but only include permissions that belong to the package
        $packagePermissions = $this->getPackagePermissions($packageId);
        $packagePermissionIds = array_column($packagePermissions, 'permission_id');
        $hierarchy = [];
        $allPermissionsHierarchy = $this->getPermissionsHierarchy();

        foreach ($allPermissionsHierarchy as $parent) {
            // Check if parent is in package permissions
            if (in_array($parent['permission_id'], $packagePermissionIds)) {
                $filteredParent = $parent;
                $filteredParent['children'] = [];
                // Now filter children
                foreach ($parent['children'] as $child) {
                    if (in_array($child['permission_id'], $packagePermissionIds)) {
                        $filteredParent['children'][] = $child;
                    }
                }
                $hierarchy[] = $filteredParent;
            }
        }

        return $hierarchy;
    }

    public function updatePackagePermissions(int $packageId, array $permissionIds): void
    {
        // Strategy: Use a transaction to ensure data integrity
        // Secondly, delete existing permissions for the package
        // We also need to cascade this to the organizations that have this package
        $this->db->transException(true)
            ->transStart();

        // Delete existing permissions
        $deleteSql = "
            DELETE FROM package_permissions
            WHERE package_id = :package_id:
        ";

        $this->db->query($deleteSql, [
            'package_id' => $packageId,
        ]);

        // Insert new permissions
        $insertSql = "
            INSERT INTO package_permissions (package_id, permission_id, created_at, updated_at)
            VALUES (:package_id:, :permission_id:, NOW(), NOW())
        ";

        foreach ($permissionIds as $permissionId) {
            $this->db->query($insertSql, [
                'package_id'    => $packageId,
                'permission_id' => $permissionId,
            ]);
        }

        // Clean up user permissions cache for all users in organizations with this package
        $orgSql = "
            SELECT id AS organization_id
            FROM organizations
            WHERE package_id = :package_id:
        ";

        $orgQuery = $this->db->query($orgSql, [
            'package_id' => $packageId,
        ]);

        $organizations = $orgQuery->getResultArray();

        foreach ($organizations as $org) {
            $userSql = "
                SELECT DISTINCT ug.user_id AS user_id
                FROM user_groups ug
                INNER JOIN group_permissions gp ON ug.group_id = gp.group_id
                INNER JOIN package_permissions pp ON gp.permission_id = pp.permission_id
                WHERE pp.package_id = :package_id:
                  AND ug.deleted_at IS NULL
                  AND gp.deleted_at IS NULL
                  AND pp.deleted_at IS NULL
            ";

            $userQuery = $this->db->query($userSql, [
                'package_id' => $packageId,
            ]);

            $users = $userQuery->getResultArray();

            foreach ($users as $user) {
                $this->clearUserPermissionsCache($user['user_id'], $org['organization_id']);
            }
        }

        $this->db->transComplete();
    }

    public function clearUserPermissionsCache(int $userId, int $orgId): void
    {
        $cacheKey = "user_permissions_{$userId}_{$orgId}";
        $this->cache->delete($cacheKey);
    }
}