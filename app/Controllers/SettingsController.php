<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\SettingsSchema;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class SettingsController extends BaseController
{
    public function index()
    {
        //
    }

    public function generalSettingsView()
    {
        try {
            // Try to get the 'section' query parameter
            $data = $this->request->getGet();

            // If none is provided, default to 'site'
            if (empty($data['section'])) {
                $data['section'] = 'site';
            }

            // Load organisation settings from the model
            list(
                'sections' => $sections,
                'html' => $sectionsHtml
                ) = $this->settings_service->getRenderableSettingsSections($data['section'], $this->org_slug);
//            $organisationModel = model('App\Models\OrganisationModel');
//            $organisationSettings = $organisationModel->findAll();

            $sectionData = $this->settings_service->getRenderableFormBySection($data['section']);

            if (!$sectionData['success']) {
                log_message('error', 'Settings section not found: {section}', ['section' => $data['section']]);

                $this->log_audit('Failed to load settings section', false, [
                    'section' => $data['section']
                ]);

                flash_message('Not Found', 'The requested settings section was not found.', 'error');

//                return service('response')
//                    ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
//                    ->setBody('Settings section not found.');
                return redirect()
                    ->back()
                    ->withInput();
            }

//            dd($sectionData);

            $section = $sectionData['data'];


            // Pass the settings to the view
            return view('pages/organisation/settings', compact('sections', 'sectionsHtml', 'section'));
        } catch (Exception $e) {
            log_message('error', 'Error loading general settings view: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error loading settings section', false, [
                'section'           => $data['section'] ?? 'unknown',
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Error', 'An error occurred while loading the settings section.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function saveGeneralSettings()
    {
        try {
            $data = $this->request->getGet();

            // Log the incoming request data
            log_message('debug', '[DEBUG] Incoming General Settings Save Request: {data}', ['data' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)]);

            $section = $data['section'] ?? null;

            // If none is provided, default to 'site'
            if (empty($section)) {
                $section = 'site';
            }

            log_message('debug', '[DEBUG] Saving General Settings: {data}', ['data' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)]);

            if (empty($data['section'])) {
                log_message('error', 'Settings section is missing in the request data.');

                flash_message('Error', 'The requested settings section was not found.', 'error');

                return redirect()
                    ->back()
                    ->with('error', 'Settings section is required.');
            }

            $schema = SettingsSchema::$structure;

            $dynamicRules = [];

            // Flatten schema to extract rules
            foreach ($schema as $schema_section => $group) {
                foreach ($group['value'] as $key => $setting) {
                    if (isset($setting['validation']) && $schema_section === $data['section']) {
                        $dynamicRules[$key] = [
                            'label' => $setting['label'],
                            'rules' => $setting['validation']
                        ];
                    }
                }
            }

            // Log the validations
            log_message('debug', '[DEBUG] Dynamic Validation Rules: {rules}', ['rules' => json_encode($dynamicRules, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)]);

            // Validate incoming data against dynamic rules
            if (!$this->validate($dynamicRules)) {
                log_message('error', 'Validation failed while saving settings: {errors}', [
                    'errors' => json_encode($this->validator->getErrors(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                ]);

//                flash_message('Validation Error', 'Please correct the errors in the form.', 'error');
                flash_message('Validation Error', 'Validation Errors: ' . implode(' ', $this->validator->getErrors()), 'error');

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = $this->validator->getValidated();

            // Handle file uploads if any
            foreach ($this->request->getFiles() as $key => $file) {
                if ($file->isValid() && ! $file->hasMoved()) {
                    $uploadPath = 'settings/' . $this->user['organization_id'] . '/';

                    $fileValues = file_upload_values($file, $uploadPath);

                    if ($fileValues) {
                        $data[$key] = $fileValues['path'];
                    } else {
                        log_message('error', 'File upload failed for key: {key}', ['key' => $key]);

                        flash_message('Error', 'An error occurred while uploading the file.', 'error');

                        return redirect()
                            ->back()
                            ->withInput();
                    }
                }
            }

            $result = $this->settings_service->saveSettings($section, $data, $this->user['organization_id']);

            if (!$result['success']) {
                log_message('error', 'Failed to save settings section: {section} {settings} ', [
                    'section'  => $section,
                    'settings' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                ]);

                $this->log_audit('Failed to save settings section', false, [
                    'section' => $section
                ]);

                flash_message('Error', 'An error occurred while saving the settings.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $this->log_audit('Saved settings section', true, [
                'section'  => $section,
                'settings' => $data
            ]);

            flash_message('Success', 'Settings saved successfully.', 'success');

            return redirect()
                ->back();
        } catch (Exception $e) {
            log_message('error', 'Error saving general settings: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error saving settings section', false, [
                'section'           => $data['section'] ?? 'unknown',
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Error', 'An error occurred while saving the settings.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }
}
