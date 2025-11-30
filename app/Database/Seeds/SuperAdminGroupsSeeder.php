<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminGroupsSeeder extends Seeder
{
    public function run()
    {
        // Create the super admin group that belongs to the super admin package

        $data = [
            [
                'name'            => 'Super Admins',
                'description'     => 'Group for super admin users with all permissions.',
                'max_users'       => 0,
                'slug'            => 'super-admin',
                'organization_slug' => 'default-organization',
            ],
            // Add an admin group for the default organization
            [
                'name'            => 'Admins',
                'description'     => 'Group for organization admin users.',
                'max_users'       => 0,
                'slug'            => 'admin',
                'organization_slug' => 'default-organization',
            ],
        ];

        // Empty the groups table before inserting
        $this->db->table('groups')
            ->emptyTable();

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
}
