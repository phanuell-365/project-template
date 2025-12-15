<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrganizationsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Create a default organization that the Super Admin can use
            [
                'name'          => 'Default Organization',
                'address'       => '123 Main St, Anytown, USA',
                'contact_email' => 'phanuell@mzawadi.com',
                'contact_phone' => '+1234567890',
                'slug'          => 'admin',
                'package_id'    => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // You can add more organizations here if needed
        ];

        // Empty the organizations table before inserting
//        $this->db->table('organizations')
//            ->truncate();

        // Before inserting, lookup the package ID for 'super-admin' package
        $package = $this->db->table('packages')
            ->where('slug', 'default-package')
            ->get()
            ->getRowArray();
        if ($package) {
            foreach ($data as &$org) {
                $org['package_id'] = $package['id'];
            }

            // Insert the organizations
            $this->db->table('organizations')
                ->insertBatch($data);
        }
    }
}
