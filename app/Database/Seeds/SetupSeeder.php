<?php

namespace App\Database\Seeds;

use App\Libraries\SetupSchema;
use CodeIgniter\Database\Seeder;

class SetupSeeder extends Seeder
{
    public function run()
    {
        // 1. Set up permissions
        $this->setUpPermissions();

        // 2. Create packages
        $this->createPackages();

        // 3. Create organizations
        $this->createOrganizations();

        // 4. Create groups
        $this->createGroups();

        // 5. Assign package permissions
        $this->assignPackagesPermissions();

        // 6. Assign group permissions
        $this->assignGroupPermissions();

        // 7. Create Super Admin User
        $this->createSuperAdminUser();

    }

    public function setUpPermissions()
    {
        $data = SetupSchema::$permissionsStructure;

//        $this->db->table('permissions')
//            ->where('deleted_at IS NULL', null, false)
//            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

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

            $this->db->table('permissions')
                ->insert($root);
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

            $this->db->table('permissions')
                ->insert($child);
        }
    }

    public function assignPackagesPermissions()
    {
        $data = SetupSchema::$packagesPermissionsStructure;

        foreach ($data as $entry) {
            // Get package ID
            $package = $this->db->table('packages')
                ->where('slug', $entry['package_slug'])
                ->get()
                ->getRowArray();

            if ($package) {
                if (empty($entry['permission_slugs'])) {
                    // Assign all permissions
                    $permissions = $this->db->table('permissions')
                        ->get()
                        ->getResultArray();
                } else {
                    // Assign specific permissions
                    $permissions = $this->db->table('permissions')
                        ->whereIn('slug', $entry['permission_slugs'])
                        ->get()
                        ->getResultArray();
                }

                $insertData = [];
                foreach ($permissions as $permission) {
                    $insertData[] = [
                        'package_id'    => $package['id'],
                        'permission_id' => $permission['id'],
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];
                }

                if (!empty($insertData)) {
                    // Insert package permissions
                    $this->db->table('package_permissions')
                        ->insertBatch($insertData);
                }
            }
        }
    }

    public function assignGroupPermissions()
    {
        $data = SetupSchema::$groupsPermissionsStructure;

        foreach ($data as $entry) {
            // Get group ID
            $group = $this->db->table('groups')
                ->where('slug', $entry['group_slug'])
                ->get()
                ->getRowArray();

            if ($group) {
                if (empty($entry['permission_slugs'])) {
                    // Assign all permissions
                    $permissions = $this->db->table('permissions')
                        ->get()
                        ->getResultArray();
                } else {
                    // Assign specific permissions
                    $permissions = $this->db->table('permissions')
                        ->whereIn('slug', $entry['permission_slugs'])
                        ->get()
                        ->getResultArray();
                }

                $insertData = [];

                foreach ($permissions as $permission) {
                    $insertData[] = [
                        'group_id'      => $group['id'],
                        'permission_id' => $permission['id'],
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];
                }

                if (!empty($insertData)) {
                    $this->db->table('group_permissions')
                        ->insertBatch($insertData);
                }
            }
        }
    }

    public function createOrganizations()
    {
        $data = SetupSchema::$organizationsStructure;

        // Before inserting, lookup the package ID for 'super-admin' package
        $package = $this->db->table('packages')
            ->where('slug', 'default-package')
            ->get()
            ->getRowArray();
        if ($package) {
            foreach ($data as &$org) {
                $org['package_id'] = $package['id'];
                $org['created_at'] = date('Y-m-d H:i:s');
                $org['updated_at'] = date('Y-m-d H:i:s');
            }

            // Insert the organizations
            $this->db->table('organizations')
                ->insertBatch($data);
        }
    }

    public function createPackages()
    {
        $data = SetupSchema::$packagesStructure;

        foreach ($data as &$package) {
            $package['features'] = json_encode($package['features']);
            $package['created_at'] = date('Y-m-d H:i:s');
            $package['updated_at'] = date('Y-m-d H:i:s');
        }

        // Insert the package data
        $this->db->table('packages')
            ->insertBatch($data);
    }

    public function createGroups()
    {
        $data = SetupSchema::$groupsStructure;

        // Before inserting, lookup the organization ID for 'default-organization'
        $organization = $this->db->table('organizations')
            ->where('slug', 'admin')
            ->get()
            ->getRowArray();

           if ($organization) {
            foreach ($data as &$group) {
                $group['organization_id'] = $organization['id'];
                unset($group['organization_slug']);
            }

            // Set timestamps
            $currentTime = date('Y-m-d H:i:s');

            foreach ($data as &$group) {
                $group['created_at'] = $currentTime;
                $group['updated_at'] = $currentTime;
            }

            // Insert the groups
            $this->db->table('groups')
                ->insertBatch($data);
        }
    }

    public function createSuperAdminUser()
    {
        // Create Super Admin User
        $data = [
            'identifier'    => 'Super Admin',
            'identifier2'   => 'phanuell@mzawadi.com',
            'identifier3'   => '0726943678',
            'secret'       => password_hash('SuperAdmin@123', PASSWORD_BCRYPT),
            'status'        => 1,
            'organization_slug' => 'admin',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Get organization ID
        $organization = $this->db->table('organizations')
            ->where('slug', 'admin')
            ->get()
            ->getRowArray();

        if ($organization) {
            $data['organization_id'] = $organization['id'];

            // Remove organization_slug before insert
            unset($data['organization_slug']);

            // Insert the super admin user
            $this->db->table('users')
                ->insert($data);

            // Assign Super Admin to Super Admin Group
            $user = $this->db->table('users')
                ->where('identifier2', 'phanuell@mzawadi.com')
                ->get()
                ->getRowArray();

            $group = $this->db->table('groups')
                ->where('slug', 'super-admin')
                ->get()
                ->getRowArray();

            if ($user && $group) {
                $this->db->table('user_groups')
                    ->insert([
                        'user_id'    => $user['id'],
                        'group_id'   => $group['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }
    }
}
