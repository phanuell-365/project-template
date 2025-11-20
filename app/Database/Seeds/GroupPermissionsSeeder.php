<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GroupPermissionsSeeder extends Seeder
{
    public function run()
    {
        // The super admin group is given all permissions
        // Other groups can have limited permissions

        $data = [
            [
                'group_slug'      => 'super-admin',
                'permission_slugs' => [],
                // All permissions
            ],
            [
                'group_slug'      => 'admin',
                'permission_slugs' => [
                    'dashboard.view',
                    'user.management',
                    'users.list',
                    'users.create',
                ],
            ]
        ];

        // Empty the group_permissions table before inserting
        $this->db->table('group_permissions')
            ->emptyTable();

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
}
