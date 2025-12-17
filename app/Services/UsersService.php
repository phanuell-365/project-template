<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;

class UsersService extends BaseService
{

    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function generateUserPassword(): string
    {
        // We can use a more secure and user-friendly password generation method here
        // The user password will be sent via email, so it should be easy to remember
        // We'll also inform the user to change it after their first login
        // Also, to reduce confusion, we'll avoid characters that look similar
        $length = 12;

        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*()';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

    private function phoneExists(string $phone, string $org_slug): bool
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM users u
            JOIN organizations o ON u.organization_id = o.id
            WHERE u.identifier3 = :phone:
              AND o.slug = :org_slug:
              AND u.deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'phone'    => $phone,
            'org_slug' => $org_slug,
        ]);

        $result = $query->getRowArray();

        return $result['count'] > 0;
    }

    private function emailExists(string $email, string $org_slug): bool
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM users u
            JOIN organizations o ON u.organization_id = o.id
            WHERE u.identifier2 = :email:
              AND o.slug = :org_slug:
              AND u.deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'email'    => $email,
            'org_slug' => $org_slug,
        ]);

        $result = $query->getRowArray();

        return $result['count'] > 0;
    }

    public function createUser(array $data, string $org_slug)
    {
        // Check for existing email
        if ($this->emailExists($data['email'], $org_slug)) {
            return [
                'success' => false,
                'errors'  => ['email' => 'The provided email is already in use.'],
            ];
        }

        // Check for existing phone number if provided
        if (isset($data['phone']) && $data['phone'] !== '' && $this->phoneExists($data['phone'], $org_slug)) {
            return [
                'success' => false,
                'errors'  => ['phone' => 'The provided phone number is already in use.'],
            ];
        }

        $user_password = $this->generateUserPassword();

        $user_data = [
            'identifier' => $data['full_name'],
            'identifier2'     => $data['email'],
            'identifier3'     => $data['phone'] ?? null,
            'status'    => $data['status'],
            'password'  => password_hash($user_password, PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->transStart();

        // Get organization ID from slug
        $org_query = $this->db->table('organizations')
            ->where('slug', $org_slug)
            ->get();

        $organization = $org_query->getRowArray();

        if (!$organization) {
            return [
                'success' => false,
                'errors'  => ['organization' => 'Organization not found.'],
            ];
        }

        $user_id = $this->db->table('users')
            ->insert([
                ...$user_data,
                'organization_id' => $organization['id'],
            ]);

        
    }
}