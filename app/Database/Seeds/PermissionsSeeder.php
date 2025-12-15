<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // use Google Material Symbols for icons

        $data = [
            // ============================================================
            // 1. DASHBOARD (Root Level)
            // ============================================================
            [
                'name'        => 'Dashboard',
                'description' => 'Access to the main dashboard',
                'uri'         => '/dashboard',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'dashboard',
                'slug'        => 'dashboard.view',
                'order'       => 1,
            ],
            // ============================================================
            // 2. USER MANAGEMENT (Module)
            // ============================================================
            // Parent Item (Sidebar dropdown trigger)
            [
                'name'        => 'User Management',
                'description' => 'Manage users and roles',
                'uri'         => '#',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'people',
                'slug'        => 'user.management',
                'order'       => 2,
            ],
            // Child: List Users (Sidebar Link)
            [
                'name'        => 'List Users',
                'description' => 'View and manage users',
                'uri'         => '/users',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'user.management',
                'icon'        => 'list',
                'slug'        => 'users.list',
                'order'       => 1,
            ],
            // Action: Create User (Sidebar Link/Button)
            [
                'name'        => 'Create User',
                'description' => 'Add new users to the system',
                'uri'         => '/users/create',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'user.management',
                'icon'        => 'person_add',
                'slug'        => 'users.create',
                'order'       => 2,
            ],
            // Action: Edit User (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit User',
                'description' => 'Modify existing user details',
                'uri'         => '/users/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'user.management',
                'icon'        => null,
                'slug'        => 'users.edit',
                'order'       => null,
            ],
            // Action: Delete User (Not in Sidebar/Hidden)
            [
                'name'        => 'Delete User',
                'description' => 'Remove users from the system',
                'uri'         => '/users/delete',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'user.management',
                'icon'        => null,
                'slug'        => 'users.delete',
                'order'       => null,
            ],
            // ============================================================
            // 3. ACCESS CONTROL / ROLES (Module)
            // ============================================================
            // Parent Item
            [
                'name'        => 'Access Control',
                'description' => 'Manage roles and permissions',
                'uri'         => '#',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'security',
                'slug'        => 'access.control',
                'order'       => 3,
            ],
            // Child: Groups (Sidebar Link)
            [
                'name'        => 'Groups',
                'description' => 'View and manage user groups',
                'uri'         => '/groups',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => 'group',
                'slug'        => 'groups.list',
                'order'       => 1,
            ],
            // Action: Create Group (Not in Sidebar/Hidden)
            [
                'name'        => 'Create Group',
                'description' => 'Add new user groups',
                'uri'         => '/groups/create',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => null,
                'slug'        => 'groups.create',
                'order'       => null,
            ],
            // Action: Edit Group (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Group',
                'description' => 'Modify existing user groups',
                'uri'         => '/groups/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => null,
                'slug'        => 'groups.edit',
                'order'       => null,
            ],
            // Action: Delete Group (Not in Sidebar/Hidden)
            [
                'name'        => 'Delete Group',
                'description' => 'Remove user groups from the system',
                'uri'         => '/groups/delete',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => null,
                'slug'        => 'groups.delete',
                'order'       => null,
            ],
            // Action: View Group Permissions (Not in Sidebar/Hidden)
            [
                'name'        => 'View Group Permissions',
                'description' => 'View permissions assigned to groups',
                'uri'         => '/groups/permissions',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => null,
                'slug'        => 'groups.permissions.view',
                'order'       => null,
            ],
            // Action: Edit Group Permissions (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Group Permissions',
                'description' => 'Modify permissions assigned to groups',
                'uri'         => '/groups/permissions/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'access.control',
                'icon'        => null,
                'slug'        => 'groups.permissions.edit',
                'order'       => null,
            ],
            // ============================================================
            // 4. ORGANISATION SETTINGS (Module)
            // ============================================================
            // Parent Item
            [
                'name'        => 'Organisation Settings',
                'description' => 'Manage organisation settings',
                'uri'         => '#',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'settings',
                'slug'        => 'organisation.settings',
                'order'       => 4,
            ],
            // Child: General Settings (Sidebar Link)
            [
                'name'        => 'General Settings',
                'description' => 'View and edit organisation settings',
                'uri'         => '/organisation/settings',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'organisation.settings',
                'icon'        => 'tune',
                'slug'        => 'organisation.settings.general',
                'order'       => 1,
            ],
            // Action: Edit Settings (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Settings',
                'description' => 'Modify organisation settings',
                'uri'         => '/organisation/settings/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'organisation.settings',
                'icon'        => null,
                'slug'        => 'organisation.settings.edit',
                'order'       => null,
            ],
            // Child: Billing Information (Sidebar Link)
            [
                'name'        => 'Billing Information',
                'description' => 'View and manage billing information',
                'uri'         => '/organisation/billing',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'organisation.settings',
                'icon'        => 'credit_card',
                'slug'        => 'organisation.billing.view',
                'order'       => 2,
            ],
            // Action: Billing History (Not in Sidebar/Hidden)
            [
                'name'        => 'Billing History',
                'description' => 'View organisation billing history',
                'uri'         => '/organisation/billing/history',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'organisation.settings',
                'icon'        => null,
                'slug'        => 'organisation.billing.history',
                'order'       => null,
            ],
            // ============================================================
            // 5. SYSTEM SETTINGS (Module)
            // ============================================================
            // Parent Item
            [
                'name'        => 'System Settings',
                'description' => 'Manage system-wide settings',
                'uri'         => '#',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'build',
                'slug'        => 'system.settings',
                'order'       => 5,
            ],
            // Child: Package Settings (Sidebar Link)
            [
                'name'        => 'Package Settings',
                'description' => 'View and manage package settings',
                'uri'         => '/system/package-settings',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => 'package',
                'slug'        => 'system.package.settings',
                'order'       => 1,
            ],
            // Action: Create Package (Not in Sidebar/Hidden)
            [
                'name'        => 'Create Package',
                'description' => 'Add new system packages',
                'uri'         => '/system/package-settings/create',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.create',
                'order'       => null,
            ],
            // Action: Edit Package (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Package',
                'description' => 'Modify existing system packages',
                'uri'         => '/system/package-settings/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.edit',
                'order'       => null,
            ],
            // Action: Delete Package (Not in Sidebar/Hidden)
            [
                'name'        => 'Delete Package',
                'description' => 'Remove system packages',
                'uri'         => '/system/package-settings/delete',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.delete',
                'order'       => null,
            ],
            // Action: Assign Permissions to Package (Not in Sidebar/Hidden)
            [
                'name'        => 'Assign Permissions to Package',
                'description' => 'Manage permissions assigned to packages',
                'uri'         => '/system/package-settings/permissions',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.permissions.assign',
                'order'       => null,
            ],
            // Action: Create Package Group Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Create Package Group Template',
                'description' => 'Add new package group permission templates',
                'uri'         => '/system/package-settings/group-templates/create',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.group.create',
                'order'       => null,
            ],
            // Action: Edit Package Group Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Package Group Template',
                'description' => 'Modify existing package group permission templates',
                'uri'         => '/system/package-settings/group-templates/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.group.edit',
                'order'       => null,
            ],
            // Action: Delete Package Group Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Delete Package Group Template',
                'description' => 'Remove package group permission templates',
                'uri'         => '/system/package-settings/group-templates/delete',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.group.delete',
                'order'       => null,
            ],
            // Action: Assign Permissions to Package Group Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Assign Permissions to Package Group Template',
                'description' => 'Manage permissions assigned to package group templates',
                'uri'         => '/system/package-settings/group-templates/permissions',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.package.group.permissions.assign',
                'order'       => null,
            ],
            // Child: Group Settings (Sidebar Link)
            [
                'name'        => 'Group Settings',
                'description' => 'View and manage group settings',
                'uri'         => '/system/group-settings',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => 'group_work',
                'slug'        => 'system.group.settings',
                'order'       => 2,
            ],
            // Action: Create Group Permission Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Create Group Permission Template',
                'description' => 'Add new group permission templates',
                'uri'         => '/system/group-settings/create',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.group.create',
                'order'       => null,
            ],
            // Action: Edit Group Permission Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Edit Group Permission Template',
                'description' => 'Modify existing group permission templates',
                'uri'         => '/system/group-settings/edit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.group.edit',
                'order'       => null,
            ],
            // Action: Delete Group Permission Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Delete Group Permission Template',
                'description' => 'Remove group permission templates',
                'uri'         => '/system/group-settings/delete',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.group.delete',
                'order'       => null,
            ],
            // Action: Assign Permissions to Group Template (Not in Sidebar/Hidden)
            [
                'name'        => 'Assign Permissions to Group Template',
                'description' => 'Manage permissions assigned to group templates',
                'uri'         => '/system/group-settings/permissions',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.settings',
                'icon'        => null,
                'slug'        => 'system.group.permissions.assign',
                'order'       => null,
            ],
            // ============================================================
            // 6. SYSTEM LOGS (Module)
            // ============================================================
            // Parent Item
            [
                'name'        => 'System Logs',
                'description' => 'View system logs and activities',
                'uri'         => '#',
                'is_parent'   => 1,
                'context'     => 'admin',
                'parent_slug' => null,
                'icon'        => 'receipt_long',
                'slug'        => 'system.logs',
                'order'       => 6,
            ],
            // Child: Audit Logs (Sidebar Link)
            [
                'name'        => 'Audit Logs',
                'description' => 'View audit logs of system activities',
                'uri'         => '/system/logs/audit',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.logs',
                'icon'        => 'history',
                'slug'        => 'system.logs.audit',
                'order'       => 1,
            ],
            // Child: Communication Logs (Sidebar Link)
            [
                'name'        => 'Communication Logs',
                'description' => 'View communication logs',
                'uri'         => '/system/logs/communication',
                'is_parent'   => 0,
                'context'     => 'admin',
                'parent_slug' => 'system.logs',
                'icon'        => 'message',
                'slug'        => 'system.logs.communication',
                'order'       => 2,
            ],
        ];

        // Empty the permissions table before seeding
        $this->db->table('permissions')->emptyTable();

        // Insert data into the permissions table

        $roots = [];
        $children = [];

        foreach ($data as $permission) {
            if ($permission['parent_slug'] == null) {
                // Parent permission
                $roots[] = $permission;
            } else {
                // Child permission
                $children[] = $permission;
            }
        }

        $slugMap = [];

        // First, insert root permissions to get their IDs
        foreach ($roots as $root) {
            // unset parent_slug before insert since it's not a DB field
            unset($root['parent_slug']);

            // add update_at and created_at timestamps
            $root['created_at'] = date('Y-m-d H:i:s');
            $root['updated_at'] = date('Y-m-d H:i:s');

            $this->db->table('permissions')->insert($root);
            $insertedId = $this->db->insertID();
            $slugMap[$root['slug']] = $insertedId;
        }

        // Next, insert child permissions with correct parent_id
        foreach ($children as $child) {
            $parentId = $slugMap[$child['parent_slug']] ?? null;
            if ($parentId) {
                $child['parent_id'] = $parentId;
            } else {
                $child['parent_id'] = null; // or handle error if parent not found
            }
            // unset parent_slug before insert since it's not a DB field
            unset($child['parent_slug']);

            // add update_at and created_at timestamps
            $child['created_at'] = date('Y-m-d H:i:s');
            $child['updated_at'] = date('Y-m-d H:i:s');

            $this->db->table('permissions')->insert($child);
        }
    }
}
