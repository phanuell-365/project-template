<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin User
        $data = [
            'identifier'    => 'Super Admin',
            'identifier2'   => 'phanuell@mzawadi.com',
            'identifier3'   => '0726943678',
            'secret'       => password_hash('SuperAdmin@123', PASSWORD_BCRYPT),
            'status'        => 1,
            'organization_slug' => 'default-organization',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Clear existing super admin to avoid duplicates
        $this->db->table('users')
            ->where('identifier2', 'phanuell@mzawadi.com')
            ->delete();

        // Get organization ID
        $organization = $this->db->table('organizations')
            ->where('slug', 'default-organization')
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
