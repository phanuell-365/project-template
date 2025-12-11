<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PackagesService;
use App\Services\PermissionsService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class PackageSettingsController extends BaseController
{
    private PackagesService $packages_service;

    public function __construct()
    {
        $this->packages_service = Services::packages_service();
    }

    public function index(): string
    {
        $packages = $this->packages_service->listPackages();

        // Do a flash message test
//        flash_message('Welcome', 'You have successfully accessed the package settings page.', 'success', 'modal', 10);

        return view('pages/system/package_settings', compact('packages'));
    }

    public function create(): ResponseInterface
    {
        try {
            // Logic to create package settings goes here

            $rules = [
                'name'          => 'required|string|max_length[255]',
                'description'   => 'permit_empty|string',
                'price'         => 'required|decimal',
                'duration_days' => 'required|integer',
                'features'      => 'permit_empty|string',
                'max_users'     => 'required|integer',
                'status'        => 'required|in_list[active,inactive]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to create package settings', false, [
                    'errors' => $this->validator->getErrors()
                ]);

                flash_message('Creation Failed', 'Please correct the errors and try again.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $data = $this->validator->getValidated();

            $result = $this->packages_service->createPackage($data);

            if (!$result['success']) {
                log_message('error', '[ERROR] Failed to create package settings: {message}', [
                    'message' => $result['message'],
                ]);

                $this->log_audit('Failed to create package settings', false, [
                    'error_message' => $result['message']
                ]);

                flash_message('Creation Failed', $result['message'], 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $packageId = $result['package_id'];

            // Assume package settings are created successfully
            $this->log_audit('Created package settings successfully', true, [
                'package_id' => $packageId
            ]);

            flash_message('Package Created', 'The package has been created successfully.', 'success');

            return redirect()
                ->to(route_to('package-settings', $this->org_slug))
                ->with('success', json_encode([
                    'status'     => 'success',
                    'package_id' => $packageId,
                ]));
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while creating package settings: {message}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);

            $this->log_audit('Exception occurred while creating package settings', false, [
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Creation Failed', 'An unexpected error occurred. Please try again later.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function edit(): ResponseInterface
    {
        try {
            $rules = [
                'package_id'    => 'required|integer|is_not_unique[packages.id]',
                'name'          => 'required|string|max_length[255]',
                'description'   => 'permit_empty|string',
                'price'         => 'required|decimal',
                'duration_days' => 'required|integer',
                'features'      => 'permit_empty|string',
                'max_users'     => 'required|integer',
                'status'        => 'required|in_list[active,inactive]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to edit package settings', false, [
                    'errors' => $this->validator->getErrors()
                ]);

                flash_message('Update Failed', 'Please correct the errors and try again.', 'error');

//                return redirect()
//                    ->back()
//                    ->withInput();
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Validation failed. Please correct the errors and try again.',
                        'errors'  => $this->validator->getErrors(),
                    ]);
            }

            $data = $this->validator->getValidated();

            $packageId = $data['package_id'];
            unset($data['package_id']);

            $result = $this->packages_service->editPackage($packageId, $data);

            if (!$result['success']) {
                log_message('error', '[ERROR] Failed to edit package settings: {message}', [
                    'message' => $result['message'],
                ]);

                $this->log_audit('Failed to edit package settings', false, [
                    'error_message' => $result['message']
                ]);

                flash_message('Update Failed', $result['message'], 'error');

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $result['message'],
                    ]);
            }

            // Assume package settings are edited successfully
            $this->log_audit('Edited package settings successfully', true, [
                'package_id' => $packageId
            ]);

            flash_message('Package Updated', 'The package has been updated successfully.', 'success');

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'     => 'success',
                    'package_id' => $packageId,
                ]);

        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while editing package settings: {message}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);

            $this->log_audit('Exception occurred while editing package settings', false, [
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Update Failed', 'An unexpected error occurred. Please try again later.', 'error');

//            return redirect()
//                ->back()
//                ->withInput();
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                ]);
        }
    }

    public function getPermissions(): string | ResponseInterface
    {
        try {
            // Validate the package id
            $rules = [
                'package_id' => 'required|integer|is_not_unique[packages.id]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to get package permissions', false, [
                    'errors' => $this->validator->getErrors()
                ]);

                flash_message('Retrieval Failed', 'Please provide a valid package ID.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $data = $this->validator->getValidated();

            $packageId = $data['package_id'];

            // Fetch permissions using the PackagesService
            // The view sent back should consist of all permissions against the permissions available for that package

            $package_permissions = $this->permissionsService->getPackagePermissions($packageId);
            $all_permissions = $this->permissionsService->getPermissionsHierarchy();

            return view('pages/system/package_permissions', compact('package_permissions', 'all_permissions', 'packageId'));
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while getting permissions: {message}', [
                'message' => $e->getMessage(),
                'stack'   => json_encode($e->getTrace(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

//            return $this->response
//                ->setStatusCode(500)
//                ->setJSON([
//                    'status'  => 'error',
//                    'message' => 'An unexpected error occurred. Please try again later.',
//                ]);

            flash_message('Retrieval Failed', 'An unexpected error occurred. Please try again later.', 'error');

            $this->log_audit('Exception occurred while getting package permissions', false, [
                'exception_message' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function updatePermissions(): ResponseInterface
    {
        try {
            // Get the package id from the query parameters

            $rules = [
                'package_id'   => 'required|integer|is_not_unique[packages.id]',
                'permissions.*'=> 'integer|is_not_unique[permissions.id]',
            ];

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to update package permissions', false, [
                    'errors' => $this->validator->getErrors()
                ]);

//                return $this->response
//                    ->setStatusCode(422)
//                    ->setJSON([
//                        'status'  => 'error',
//                        'message' => 'Validation failed. Please correct the errors and try again.',
//                        'errors'  => $this->validator->getErrors(),
//                    ]);

                flash_message('Update Failed', 'Please correct the errors and try again.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $validatedData = $this->validator->getValidated();

            $packageId = $validatedData['package_id'];
            $permissions = $this->request->getPost('permissions') ?? [];

            $this->permissionsService->updatePackagePermissions($packageId, $permissions);

            $this->log_audit('Updated package permissions successfully', true, [
                'package_id' => $packageId,
                'permissions_updated_count' => count($permissions),
            ]);

            flash_message('Permissions Updated', 'The package permissions have been updated successfully.', 'success');

            return redirect()
                ->to(route_to('package-settings', $this->org_slug))
                ->with('success', json_encode([
                    'status'     => 'success',
                    'package_id' => $packageId,
                ]));
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while updating permissions: {message}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);

            $this->log_audit('Exception occurred while updating package permissions', false, [
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Update Failed', 'An unexpected error occurred. Please try again later.', 'error');

//            return $this->response
//                ->setStatusCode(500)
//                ->setJSON([
//                    'status'  => 'error',
//                    'message' => 'An unexpected error occurred. Please try again later.',
//                ]);
            return redirect()
                ->back()
                ->withInput();
        }
    }
}
