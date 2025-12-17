<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\GroupsService;
use App\Services\UsersService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class UserController extends BaseController
{
    private GroupsService $groups_service;
    private UsersService $users_service;

    public function __construct()
    {
        $this->groups_service = Services::groups_service();
        $this->users_service = Services::users_service();
    }

    public function index()
    {
        //
    }

    public function createUserView(): string
    {
        $groups = $this->groups_service->getGroupsUnderOrganization($this->org_slug);

        return view('pages/users/create_user', compact('groups'));
    }

    public function createUser()
    {
        try {
            $rules = [
                'full_name' => 'required|string|max_length[255]',
                'email'     => 'required|valid_email',
                'phone'     => 'permit_empty|numeric',
                'status'    => 'required|in_list[1,0]',
                'groups.*' => 'required|integer|is_not_unique[groups.id]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to create user', false, [
                    'errors' => $this->validator->getErrors()
                ]);

                flash_message('Creation Failed', 'Please correct the errors and try again.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $data = $this->validator->getValidated();

            $result = $this->users_service->createUser($data, $this->org_slug);

            if (!$result['success']) {
                log_message('error', '[ERROR] User Creation Failed: {errors}', [
                    'errors' => json_encode($result['errors'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to create user', false, [
                    'errors' => $result['errors']
                ]);

                flash_message('Creation Failed', 'An error occurred while creating the user. Please try again.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            // We'll notify the user via email here (TODO)

            $this->log_audit('User created successfully', true, [
                'user_id' => $result['user_id']
            ]);

            flash_message('User Created', 'The user has been created successfully.', 'success');

            return redirect()
                ->to(route_to('users-list', $this->org_slug));

        } catch (\Exception $e) {
            log_message('error', '[EXCEPTION] {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);

            $this->log_audit('Failed to create user due to exception', false, [
                'exception_message' => $e->getMessage(),
                'stack_trace'       => $e->getTraceAsString(),
            ]);

            flash_message('Creation Failed', 'An unexpected error occurred. Please try again later.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }
}
