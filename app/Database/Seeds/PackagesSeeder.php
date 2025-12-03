<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PackagesSeeder extends Seeder
{
    public function run()
    {
        // Create the Super Admin Package
        $packageData = [
            [
                'name'          => 'Default Package',
                'description'   => 'Package with all permissions and unrestricted access.',
                'price'         => 0.00,
                'duration_days' => 0,
                // 0 for unlimited
                'slug'          => 'default-package',
                'features'      => json_encode([
                    'unlimited_users'    => true,
                    'priority_support'   => true,
                    'custom_branding'    => true,
                    'advanced_analytics' => true,
                    'api_access'         => true,
                ]),
                'status'        => 'active',
                'max_users'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Basic',
                'description'   => 'Basic package with limited features.',
                'price'         => 9.99,
                'duration_days' => 30,
                'slug'          => 'basic',
                'features'      => json_encode([
                    'unlimited_users'    => false,
                    'priority_support'   => false,
                    'custom_branding'    => false,
                    'advanced_analytics' => false,
                    'api_access'         => false,
                ]),
                'status'        => 'active',
                'max_users'     => 10,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        // Clear existing packages to avoid duplicates

        $this->db->table('packages')
            ->truncate();

        // Insert the package data
        $this->db->table('packages')
            ->insertBatch($packageData);
    }
}