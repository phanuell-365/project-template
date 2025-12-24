<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\TemplateService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

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

            return view('pages/organisation/templates', compact( 'attributes'));
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

            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setBody('An error occurred while fetching communication templates.');
        }
    }
}
