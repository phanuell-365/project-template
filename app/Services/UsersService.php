<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Services;

class UsersService extends BaseService
{

    protected BaseConnection $db;
    protected $communication_service;

    public function __construct()
    {
        $this->db = Database::connect();

        $this->communication_service = Services::communication_service();
    }

    public function createUser(array $data, string $org_slug)
    {
        log_message('debug', '[DEBUG] Creating user with data: {data} in organization: {org_slug}', [
            'data'     => json_encode($data, JSON_PRETTY_PRINT),
            'org_slug' => $org_slug,
        ]);

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
            'identifier'  => $data['full_name'],
            'identifier2' => $data['email'],
            'identifier3' => $data['phone'] ?? null,
            'status'      => $data['status'],
            'secret'      => password_hash($user_password, PASSWORD_BCRYPT),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
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

        $this->db->table('users')
            ->insert([
                ...$user_data,
                'organization_id' => $organization['id'],
            ]);

        $user_id = $this->db->insertID();
        log_message('debug', '[DEBUG] Inserted user ID: {user_id}', [
            'user_id' => $user_id,
        ]);

        // Assign user to groups
        if (isset($data['groups']) && is_array($data['groups'])) {
            foreach ($data['groups'] as $group_id) {
                $this->db->table('user_groups')
                    ->insert([
                        'user_id'    => $user_id,
                        'group_id'   => $group_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', '[ERROR] Database transaction failed while creating user.');

            return [
                'success' => false,
                'errors'  => ['database' => 'Failed to create user due to a database error.'],
            ];
        }

        // Send a welcome email to the user with their credentials

        // Send notification email

        $this->communication_service->dispatchNotification(
            $data['email'],
            'email',
            'auth.new_user',
            [
                'user_name'         => $data['full_name'],
                'user_email'        => $data['email'],
                'user_phone'        => $data['phone'] ?? 'N/A',
                'user_password'     => $user_password,
                'company_name'      => $organization['name'],
                'login_link'        => base_url(route_to('login-view', $organization['slug'])),
                'registration_date' => date('Y-m-d H:i:s'),
            ],
            $organization['id'],
            'high'
        );

        // Send SMS notification (if phone number is provided)
        if (isset($data['phone']) && $data['phone'] !== '') {
            $this->communication_service->dispatchNotification(
                $data['phone'],
                'sms',
                'auth.new_user',
                [
                    'user_name'     => $data['full_name'],
                    'user_email'    => $data['email'],
                    'user_password' => $user_password,
                    'login_link'    => base_url(route_to('login-view', $organization['slug'])),
                ],
                $organization['id'],
                'high'
            );
        }

        return [
            'success' => true,
            'user_id' => $this->db->insertID(),
        ];
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
}