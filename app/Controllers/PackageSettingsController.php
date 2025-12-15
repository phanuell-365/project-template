<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\GroupsService;
use App\Services\PackagesService;
use App\Services\PermissionsService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;
use JsonException;

class PackageSettingsController extends BaseController
{
    private PackagesService $packages_service;
    private GroupsService $groups_service;

    public function __construct()
    {
        $this->packages_service = Services::packages_service();
        $this->groups_service = Services::groups_service();
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

            $this->response->setHeader('X-CSRF-Token', csrf_hash());

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

            $this->response->setHeader('X-CSRF-Token', csrf_hash());

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

    public function delete() : ResponseInterface
    {
        try {
            $rules = [
                'package_id' => 'required|integer|is_not_unique[packages.id]',
            ];

            $this->response->setHeader('X-CSRF-Token', csrf_hash());

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to delete package', false, [
                    'errors' => $this->validator->getErrors()
                ]);

//                flash_message('Deletion Failed', 'Please correct the errors and try again.', 'error');

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

            $result = $this->packages_service->deletePackage($packageId);

            if (!$result['success']) {
                log_message('error', '[ERROR] Failed to delete package: {message}', [
                    'message' => $result['message'],
                ]);

                $this->log_audit('Failed to delete package', false, [
                    'error_message' => $result['message']
                ]);

//                flash_message('Deletion Failed', $result['message'], 'error');

//                return redirect()
//                    ->back()
//                    ->withInput();
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $result['message'],
                    ]);
            }

            // Assume package is deleted successfully
            $this->log_audit('Deleted package successfully', true, [
                'package_id' => $packageId
            ]);

//            flash_message('Package Deleted', 'The package has been deleted successfully.', 'success');

//            return redirect()
//                ->to(route_to('package-settings', $this->org_slug));
            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'     => 'success',
                    'package_id' => $packageId,
                    'message'    => 'The package has been deleted successfully.',
                ]);
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while deleting package: {message}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);

            $this->log_audit('Exception occurred while deleting package', false, [
                'exception_message' => $e->getMessage()
            ]);

//            flash_message('Deletion Failed', 'An unexpected error occurred. Please try again later.', 'error');

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

//    public function getPermissions(): string | ResponseInterface
//    {
//
//
//        return $this->configurePackage('package_permissions');
//    }

    public function getPermissions(): string | ResponseInterface
    {
        try {
            // Validate package id using the dedicated method
            $data = $this->request->getPostGet();

            if (!$this->validatePackageId($data)) {
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
            log_message('error', '[ERROR] Exception while getting permissions: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => json_encode($e->getTrace(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

            flash_message('Retrieval Failed', 'An unexpected error occurred. Please try again later.', 'error');

            $this->log_audit('Exception occurred while getting package permissions', false, [
                'exception_message' => $e->getMessage()
            ]);

            return redirect()
                ->to(route_to('get-package-permissions', $this->org_slug))
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    private function validatePackageId(array $data): bool
    {
        $rules = [
            'package_id' => 'required|integer|is_not_unique[packages.id]',
        ];

        if (!$this->validateData($data, $rules)) {
            log_message('error', '[ERROR] Validation Errors: {errors}', [
                'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

            $this->log_audit('Failed to validate package ID', false, [
                'errors' => $this->validator->getErrors()
            ]);

            return false;
        }

        return true;
    }

    public function getPackageGroupTemplates(): string | ResponseInterface
    {
        try {
            $data = $this->request->getGet();

            log_message('debug', 'Received data for getting package group templates: {data}', [
                'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

            if (!$this->validatePackageId($data)) {
                return redirect()
                    ->back()
                    ->withInput();
            }

            $packageId = $data['package_id'];

            $data = $this->request->getGet();

            $groupId = $data['group_id'];

            // Here, the group id can only be 'new' or an existing group template id
            // There's a need to validate this input as well so that we are safe
            if (isset($data['group_id']) && $data['group_id'] !== 'new') {
                $rules = [
                    'group_id' => 'integer|is_not_unique[package_group_permissions_templates.id]',
                ];

                if (!$this->validateData($data, $rules)) {
                    log_message('error', '[ERROR] Validation Errors: {errors}', [
                        'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                    ]);

                    $this->log_audit('Failed to validate group template ID', false, [
                        'errors' => $this->validator->getErrors()
                    ]);

                    flash_message('Retrieval Failed', 'Please correct the errors and try again.', 'error');

                    return redirect()
                        ->back()
                        ->withInput();
                }
            }

            $data = $this->validator->getValidated();

            log_message('debug', 'Validated data for getting package group templates: {data}', [
                'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);


            $group_templates = $this->groups_service->listPackageGroupPermissionsTemplates($packageId);

            // create a map of group template ids to names for easy lookup in the view
            $group_template_map = [];
            foreach ($group_templates as $template) {
                $group_template_map[$template['id']] = $template['name'];
            }

            // Now prefix a 'new' item to the group_template_map list for creating new group templates
            $new_item = ['new' => 'New Template'];
            $group_template_map = $new_item + $group_template_map;

//            $all_permissions = $this->permissionsService->getPermissionsHierarchy();
            $all_permissions = $this->permissionsService->getPermissionsHierarchyForPackage($packageId);
            $group_template = $groupId === 'new' ? [] : $this->groups_service->getPackageGroupPermissionsTemplateById($groupId) ?? [];
            $assigned_permission_ids = empty($group_template) ? [] : $group_template['permission_json'] ?? [];

            return view('pages/system/package_group_templates', compact(
                'group_templates',
                'packageId',
                'group_template_map',
                'all_permissions',
                'groupId',
                'assigned_permission_ids',
                'group_template'
            ));
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while getting package group templates: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => json_encode($e->getTrace(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

            flash_message('Retrieval Failed', 'An unexpected error occurred. Please try again later.', 'error');

            $this->log_audit('Exception occurred while getting package group templates', false, [
                'exception_message' => $e->getMessage()
            ]);

            return redirect()
                ->to(route_to('get-package-group-templates', $this->org_slug))
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }

    }

    /**
     * Will work both for creating and updating package group templates
     * @return ResponseInterface
     * @throws JsonException
     */
    public function savePackageGroupTemplate(): ResponseInterface
    {
        try {

            // Rules for validating the request

            $rules = [
                'package_id'    => 'required|integer|is_not_unique[packages.id]',
                'template_id'   => 'required',
                'name'          => 'required|string|max_length[255]',
//                'name'          => "required|string|max_length[255]|is_unique[package_group_permissions_templates.name,package_id,{package_id},id,{template_id}]",
                'description'   => 'permit_empty|string',
                'permissions.*' => 'integer|is_not_unique[permissions.id]',
            ];

            // By default, we'll add a CSRF token header to the response for AJAX requests
            $this->response->setHeader('X-CSRF-Token', csrf_hash());

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to save package group template', false, [
                    'errors' => $this->validator->getErrors()
                ]);

//                flash_message('Save Failed', 'Please correct the errors and try again.', 'error');
//
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

            // For simplicity and easier understanding, we'll check for uniqueness of name here ourselves
            $validatedData = $this->validator->getValidated();

            // Generate a slug to check for uniqueness
            $slug = url_title($validatedData['name'], '-', true);

            $existingTemplate = $this->groups_service->getPackageGroupPermissionsTemplateBySlug(
                $validatedData['package_id'],
                $slug
//                $validatedData['name']
            );

            if ($existingTemplate && $existingTemplate['id'] != $validatedData['template_id']) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode(['name' => 'The template name must be unique within the package.'], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to save package group template due to non-unique name', false, [
                    'errors' => ['name' => 'The template name must be unique within the package.']
                ]);

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Validation failed. Please correct the errors and try again.',
                        'errors'  => ['name' => 'The template name must be unique within the package.'],
                    ]);
            }

            // Now, before we forget, let's check if the template id is for new items or that we're trying to update an existing one
//            $validatedData = $this->validator->getValidated();

            $templateId = $validatedData['template_id'];

            if ($templateId !== 'new') {
                // Validate that the template id exists
                $new_rules = [
                    'template_id' => 'integer|is_not_unique[package_group_permissions_templates.id]',
                ];

                if (!$this->validateData(['template_id' => $templateId], $new_rules)) {
                    log_message('error', '[ERROR] Validation Errors: {errors}', [
                        'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                    ]);

                    $this->log_audit('Failed to validate existing template ID for saving package group template', false, [
                        'errors' => $this->validator->getErrors()
                    ]);

//                    flash_message('Save Failed', 'Please correct the errors and try again.', 'error');
//
//                    return redirect()
//                        ->back()
//                        ->withInput();

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Validation failed. Please correct the errors and try again.',
                            'errors'  => $this->validator->getErrors(),
                        ]);
                }
            }

            $data = [
                'name'            => $validatedData['name'],
                'description'     => $validatedData['description'] ?? '',
                'permission_json' => $this->request->getPost('permissions') ?? [],
            ];

            $result = $this->groups_service->savePackageGroupPermissionsTemplate($validatedData['package_id'], $templateId, $data);

            if (!$result['success']) {
                log_message('error', '[ERROR] Failed to save package group template: {message}', [
                    'message' => $result['message'],
                ]);

                $this->log_audit('Failed to save package group template', false, [
                    'error_message' => $result['message']
                ]);

//                flash_message('Save Failed', $result['message'], 'error');
//
//                return redirect()
//                    ->back()
//                    ->withInput();

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $result['message'],
                    ]);
            }

            $savedTemplateId = $result['template_id'];

            // Assume package group template is saved successfully
            $this->log_audit('Saved package group template successfully', true, [
                'template_id' => $savedTemplateId
            ]);

//            flash_message('Template Saved', 'The package group template has been saved successfully.', 'success');
//
//            return redirect()
//                ->to(route_to('get-package-group-templates', $this->org_slug))
//                ->with('success', json_encode([
//                    'status'      => 'success',
//                    'template_id' => $savedTemplateId,
//                ]));

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'      => 'success',
                    'template_id' => $savedTemplateId,
                    'message'     => 'The package group template has been saved successfully.',
                ]);
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while saving package group template: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => json_encode($e->getTrace(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

//            flash_message('Save Failed', 'An unexpected error occurred. Please try again later.', 'error');

            $this->log_audit('Exception occurred while saving package group template', false, [
                'exception_message' => $e->getMessage()
            ]);

            // By default, we'll add a CSRF token header to the response for AJAX requests
            $this->response->setHeader('X-CSRF-Token', csrf_hash());

//            return redirect()
//                ->to(route_to('get-package-group-templates', $this->org_slug))
//                ->withInput()
//                ->with('error', 'An unexpected error occurred. Please try again later.');
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                ]);
        }
    }

    public function deletePackageGroupTemplate() : ResponseInterface
    {
        try {
//            $data = $this->request->getPost();

//            log_message('debug', 'Received data for deleting package group template: {data}', [
//                'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
//            ]);

            $rules = [
                'template_id' => 'required|integer|is_not_unique[package_group_permissions_templates.id]',
            ];

            $this->response->setHeader('X-CSRF-Token', csrf_hash());

            if (!$this->validate($rules)) {
                log_message('error', '[ERROR] Validation Errors: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                ]);

                $this->log_audit('Failed to validate template ID for deletion', false, [
                    'errors' => $this->validator->getErrors()
                ]);

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Validation failed. Please correct the errors and try again.',
                        'errors'  => $this->validator->getErrors(),
                    ]);
            }

            $validatedData = $this->validator->getValidated();

            $templateId = $validatedData['template_id'];

            $result = $this->groups_service->deletePackageGroupPermissionsTemplate($templateId);

            if (!$result['success']) {
                log_message('error', '[ERROR] Failed to delete package group template: {message}', [
                    'message' => $result['message'],
                ]);

                $this->log_audit('Failed to delete package group template', false, [
                    'error_message' => $result['message']
                ]);

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $result['message'],
                    ]);
            }

            $this->log_audit('Deleted package group template successfully', true, [
                'template_id' => $templateId
            ]);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status'      => 'success',
                    'template_id' => $templateId,
                    'message'     => 'The package group template has been deleted successfully.',
                ]);
        } catch (Exception $e) {
            log_message('error', '[ERROR] Exception while deleting package group template: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => json_encode($e->getTrace(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ]);

            $this->log_audit('Exception occurred while deleting package group template', false, [
                'exception_message' => $e->getMessage()
            ]);

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                ]);
        }
    }

    public function updatePermissions() : ResponseInterface
    {
        try {
            // Get the package id from the query parameters

            $rules = [
                'package_id'    => 'required|integer|is_not_unique[packages.id]',
                'permissions.*' => 'integer|is_not_unique[permissions.id]',
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
                'package_id'                => $packageId,
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
