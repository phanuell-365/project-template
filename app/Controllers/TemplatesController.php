<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\TemplateService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;
use PhpParser\Node\Stmt\If_;

class TemplatesController extends BaseController
{
    private TemplateService $templates_service;

    public function __construct()
    {
        $this->templates_service = Services::templates_service();
    }

    public function index()
    {
        //
    }

    public function getTemplates()
    {
        try {

            $templates = $this->templates_service->listTemplates($this->org_slug);

            $attributes = [
                'name',
                'description',
                'context',
                'channel',
                'customized',
                'last_modified',
            ];

            $this->log_audit('Fetched communication templates', true, [
                'template_count' => count($templates)
            ]);

            // Return the templates if the request is AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'data' => $templates
                ]);
            }

            return view('pages/organisation/templates', compact('attributes'));
        } catch (Exception $e) {
            log_message('error', 'Error fetching communication templates: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error fetching communication templates', false, [
                'exception_message' => $e->getMessage(),
                'exception_stack'   => $e->getTraceAsString()
            ]);

            flash_message('Error', 'An unexpected error occurred while fetching communication templates. Please try again later.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function editTemplateView()
    {
        try {
            $data = $this->request->getGet();

            $templateData = $this->templates_service->getTemplateBySlug($this->org_slug, $data['slug'] ?? null, $data['channel'] ?? null);

            if (!$templateData['success']) {
                log_message('error', 'Template not found: {slug}', ['slug' => $data['slug'] ?? 'null']);

                $this->log_audit('Failed to load communication template', false, [
                    'slug' => $data['slug'] ?? 'null'
                ]);

                flash_message('Not Found', 'The requested communication template was not found.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $template = $templateData['data'];

            return view('pages/organisation/edit_template', compact('template'));
        } catch (Exception $e) {
            log_message('error', 'Error loading communication template view: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error loading communication template view', false, [
                'slug'              => $data['slug'] ?? 'unknown',
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Error', 'An error occurred while loading the communication template.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function previewTemplate()
    {
        try {
            $data = $this->request->getGet();

            $templateData = $this->templates_service->getTemplateBySlug($this->org_slug, $data['slug'], $data['channel']);

            if (!$templateData['success']) {
                log_message('error', 'Template preview failed: {message}', ['message' => $previewData['message'] ?? 'Unknown error']);

                $this->log_audit('Failed to preview communication template', false, [
                    'slug'    => $data['slug'] ?? 'null',
                    'channel' => $data['channel'] ?? 'null',
                    'message' => $previewData['message'] ?? $templateData['message'] ?? 'Unknown error'
                ]);

                flash_message('Preview Error', $previewData['message'] ?? 'An error occurred during template preview.', 'error');

//                return redirect()
//                    ->back()
//                    ->withInput();
                return $this->response->setJSON([
                    'error' => $previewData['message'] ?? 'An error occurred during template preview.'
                ]);
            }
//            $previewData = $this->templates_service->previewTemplate($this->org_slug, $data['slug'] ?? null, $data['channel'] ?? null, $data['placeholders'] ?? []);

            // This request comes with POST data containing the body to preview
            $postData = $this->request->getPost();

            $previewData = $this->templates_service->previewTemplate($this->org_slug, $data['slug'] ?? null, $data['channel'] ?? null, $postData['body'] ?? '');

            if (!$previewData['success']) {
                log_message('error', 'Template preview failed: {message}', ['message' => $previewData['message'] ?? 'Unknown error']);

                $this->log_audit('Failed to preview communication template', false, [
                    'slug'    => $data['slug'] ?? 'null',
                    'channel' => $data['channel'] ?? 'null',
                    'message' => $previewData['message'] ?? 'Unknown error'
                ]);

                flash_message('Preview Error', $previewData['message'] ?? 'An error occurred during template preview.', 'error');

//                return redirect()
//                    ->back()
//                    ->withInput();
                return $this->response->setJSON([
                    'error' => $previewData['message'] ?? 'An error occurred during template preview.'
                ]);
            }

            $this->log_audit('Previewed communication template', true, [
                'slug'    => $data['slug'] ?? 'null',
                'channel' => $data['channel'] ?? 'null',
            ]);

//            return view('pages/organisation/preview_template', [
//                'preview' => $previewData['data']
//            ]);

            // Since it's an AJAX request, return JSON
            return $this->response->setJSON([
                ... $previewData
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error previewing communication template: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error previewing communication template', false, [
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Error', 'An error occurred while previewing the communication template.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function saveTemplates()
    {
        try {
            $data = $this->request->getGet();
            log_message('debug', 'Saving template with data: {data}', ['data' => json_encode($data)]);

            $templateData = $this->templates_service->getTemplateBySlug($this->org_slug, $data['slug'] ?? null, $data['channel'] ?? null);

            if (!$templateData['success']) {
                log_message('error', 'Template not found for saving: {slug}', ['slug' => $data['slug'] ?? 'null']);

                $this->log_audit('Failed to load communication template for saving', false, [
                    'slug' => $data['slug'] ?? 'null'
                ]);

                flash_message('Not Found', 'The requested communication template was not found for saving.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

//            $postData = $this->request->getPost();

            if ($data['channel'] === 'email') {
                $rules = [
                    'subject' => 'required|string|max_length[255]',
                    'body'    => 'required|string',
                ];
            } else {
                $rules = [
                    'body' => 'required|string',
                ];
            }

            if (!$this->validate($rules)) {

                $this->log_audit('Validation failed while saving communication template', false, [
                    'slug'    => $data['slug'] ?? 'null',
                    'channel' => $data['channel'] ?? 'null',
                    'errors'  => $this->validator->getErrors()
                ]);

                flash_message('Validation Error', 'Please correct the errors in the form.', 'error');

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('validation', $this->validator->getErrors());
            }

            $postData = $this->validator->getValidated();

            $saveData = $this->templates_service->saveTemplate($this->org_slug, $data['slug'], $data['channel'], $postData['subject'] ?? null, $postData['body']);

            if (!$saveData['success']) {
                log_message('error', 'Template save failed: {message}', ['message' => $saveData['message'] ?? 'Unknown error']);

                $this->log_audit('Failed to save communication template', false, [
                    'slug'    => $postData['slug'] ?? 'null',
                    'channel' => $postData['channel'] ?? 'null',
                    'message' => $saveData['message'] ?? 'Unknown error'
                ]);

                flash_message('Save Error', $saveData['message'] ?? 'An error occurred while saving the template.', 'error');

                return redirect()
                    ->back()
                    ->withInput();
            }

            $this->log_audit('Saved communication template', true, [
                'slug'    => $postData['slug'] ?? 'null',
                'channel' => $postData['channel'] ?? 'null',
            ]);

            flash_message('Success', 'Template saved successfully.', 'success');

            return redirect()
                ->to(route_to('get-communication-templates', $this->org_slug));
        } catch (Exception $e) {
            log_message('error', 'Error saving communication template: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            $this->log_audit('Error saving communication template', false, [
                'exception_message' => $e->getMessage()
            ]);

            flash_message('Error', 'An error occurred while saving the communication template.', 'error');

            return redirect()
                ->back()
                ->withInput();
        }
    }
}
