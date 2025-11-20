<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PackagePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Default Package is given all permissions

        // Our Basic Package will have limited permissions

        $data = [
            [
                'package_slug'     => 'default-package',
                'permission_slugs' => [],
                // All permissions
            ],
            [
                'package_slug'     => 'basic',
                'permission_slugs' => [
                    'dashboard.view',
                    'user.management',
                    'users.list',
                    'users.create',
                ],
            ]
        ];

        // Empty the package_permissions table before inserting
        $this->db->table('package_permissions')
            ->emptyTable();

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
}
