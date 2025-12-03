<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function loginView(): string
    {
        $org_data = $this->orgData();

        $org_name = $org_data['name'] ?? 'Organization';

        log_message('debug', '[DEBUG] {org_slug}', [
            'org_slug' => json_encode([
                'data'    => $this->org_slug,
                'message' => 'this is it'
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        ]);

        // testing the flash_message helper
//        flash_message('Welcome to the login page!', 'You can login using your credentials.', 'success', 'banner', true, 10);

        $this->log_audit('Viewed login page for organization: ' . $this->org_slug, true, ['org_slug' => $this->org_slug]);

        return view('pages/auth/login', compact('org_name'));
    }

    public function login(): ResponseInterface
    {
        // Handle login logic here (authentication, validation, etc.)

        $rules = [
            'auth_type' => 'required|in_list[email,phone]',
            'email'     => 'permit_empty|required_if_value[auth_type,email]|valid_email',
            'phone'     => 'permit_empty|required_if_value[auth_type,phone]|numeric|valid_ke_phone',
            'password'  => 'required|min_length[6]',
        ];

//        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validate($rules)) {

            log_message('debug', '[DEBUG] Validation Errors: {errors} User Data {data}', [
                'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
//                'data'   => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
            ]);

            $this->log_audit('Failed login attempt for organization: ' . $this->org_slug, false, [
                'org_slug' => $this->org_slug,
                'errors'   => $this->validator->getErrors()
            ]);

            flash_message('Login Failed', 'Please correct the errors and try again.', 'error');

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->validator->getValidated();

//        log_message('debug', '[DEBUG] Validated User Data: {data}', [
//            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
//        ]);

        // Get user data from database and verify credentials here
        if ($data['auth_type'] == 'phone') {
//            $user = $this->db->table('users')
//                ->where('identifier3', $data['phone'])
//                ->join('organizations', 'users.organization_id = organizations.id')
//                ->where('organizations.slug', $org_slug)
//                ->get()
//                ->getRowArray();
            $query = "
                SELECT users.id AS id,
                       users.identifier  AS identifier,
                       users.identifier2 AS identifier2,
                       users.identifier3 AS identifier3,
                       users.secret      AS secret,
                        organizations.id  AS org_id
                FROM users
                JOIN organizations ON users.organization_id = organizations.id
                WHERE users.identifier3 = ?
                  AND organizations.slug = ?
            ";
            $user = $this->db->query($query, [$data['phone'], $this->org_slug])->getRowArray();
        } else {
//            $user = $this->db->table('users')
//                ->where('identifier2', $data['email'])
//                ->join('organizations', 'users.organization_id = organizations.id')
//                ->where('organizations.slug', $org_slug)
//                ->get()
//                ->getRowArray();
            $query = "
                SELECT users.id AS id,
                       users.identifier  AS identifier,
                       users.identifier2 AS identifier2,
                       users.identifier3 AS identifier3,
                       users.secret      AS secret,
                        organizations.id  AS org_id
                FROM users
                JOIN organizations ON users.organization_id = organizations.id
                WHERE users.identifier2 = ?
                  AND organizations.slug = ?
            ";
            $user = $this->db->query($query, [$data['email'], $this->org_slug])->getRowArray();
        }

        // log the last query for debugging
//        log_message('debug', '[DEBUG] User Lookup Query: {query}', [
//            'query' => $this->db->getLastQuery()
//        ]);

        if (!$user || !password_verify($data['password'], $user['secret'])) {
            $this->log_audit('Failed login attempt for organization: ' . $this->org_slug, false, [
                'org_slug' => $this->org_slug,
                'reason'   => 'Invalid credentials'
            ]);

            flash_message('Login Failed', 'Invalid email/phone or password.', 'error');

            return redirect()
//                ->to(route_to('login-view', $org_slug))
                ->back()
                ->withInput();
        }

        $org_data = $this->orgData();

        // Set session data, etc. here as needed
        session()->set('isLoggedIn', true);
        session()->set('org_slug', $this->org_slug);
        session()->set('org_name', $org_data['name'] ?? 'Organization');
        session()->set('user_id', $user['id']);
        session()->set('org_id', $org_data['id']);
        session()->set('email', $user['identifier2']);
        session()->set('phone', $user['identifier3']);
        session()->set('full_name', $user['identifier']);

        $my_permissions = $this->permissionsService->getUserPermissions($user['id'], $org_data['id']);

        session()->set('permissions', $my_permissions);

        $sidebar_tree = $this->permissionsService->buildSidebarTree($my_permissions);

        session()->set('sidebar_tree', $sidebar_tree);

        // log who is user
        log_message('info', 'User {full_name} (ID: {user_id}) logged in to organization {org_name} (ID: {org_id})', [
            'full_name' => $user['identifier'],
            'user_id'   => $user['id'],
            'org_name'  => $org_data['name'] ?? 'Organization',
            'org_id'    => $org_data['id'],
        ]);

        $this->log_audit('Successful login for organization: ' . $this->org_slug, true, ['org_slug' => $this->org_slug]);

        // Set a success flash message
        flash_message('Login Successful', 'Welcome back!', 'success', 'banner', true);

        return redirect()->to(route_to('dashboard', $this->org_slug));
    }

    public function forgotPasswordView()
    {
        $org_data = $this->orgData();

        $org_name = $org_data['name'] ?? 'Organization';

        $this->log_audit('Viewed forgot password page for organization: ' . $this->org_slug, true, ['org_slug' => $this->org_slug]);

        return view('pages/auth/forgot_password', compact( 'org_name'));
    }

    public function logout(): ResponseInterface
    {
        session()->set('user_id', null);

        $this->log_audit('User logged out from organization: ' . $this->org_slug, true, ['org_slug' => $this->org_slug]);

        session()->destroy();

        flash_message('Logged Out', 'You have been successfully logged out.', 'success', 'banner', true);

        return redirect()->to(route_to('login-view', $this->org_slug));
    }
}
