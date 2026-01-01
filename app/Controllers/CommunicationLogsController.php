<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\CommunicationService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CommunicationLogsController extends BaseController
{
    private CommunicationService $communication_service;

    public function __construct()
    {
        $this->communication_service = Services::communication_service();
    }
    public function communicationLogsView()
    {
        try {

            $attributes = [
                'subject',
                'recipient_email',
                'recipient_phone',
                'channel',
                'status',
                'sent_at',
            ];

            $filters = $this->request->getGet();

            // For security, only allow filtering by valid attributes
            $rules = [
                'organization_id'   => 'permit_empty|integer',
                'channel'           => 'permit_empty|in_list[email,sms]',
                'status'            => 'permit_empty|in_list[pending,sent,failed]',
                'date_from'         => 'permit_empty|valid_date[d/m/Y]',
                'date_to'           => 'permit_empty|valid_date[d/m/Y]',
            ];

            if (!$this->validateData($filters, $rules)) {
                log_message('warning', '[WARNING] Invalid filters provided for communication logs: {filters}', [
                    'filters' => json_encode($filters)
                ]);

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Invalid filters provided.'
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                }
            }

            // If the date_from or date_to filters are not provided, set default value to be last 2 days
            if (empty($filters['date_from'])) {
                $filters['date_from'] = date('d/m/Y', strtotime('-2 days'));
            }
            if (empty($filters['date_to'])) {
                $filters['date_to'] = date('d/m/Y');
            }

            if ($this->request->isAJAX()) {
                $logs = $this->communication_service->getCommunicationLogs($filters);

                return $this->response->setJSON([
                    'status'     => 'success',
                    'message'    => 'Communication logs retrieved successfully.',
                    'data'       => $logs,
                ]);
            }

            $date_filters = [
                'date_from' => $filters['date_from'],
                'date_to'   => $filters['date_to'],
            ];

            return view('pages/system/communication_logs', compact('attributes', 'date_filters'));
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {message} {trace}', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'An error occurred while retrieving communication logs.'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            flash_message('Error', 'An error occurred while loading the communication logs page.', 'error', 'banner', true, 10);

            return redirect()->back();
        }
    }

    public function communicationLogDetailsView()
    {
        try {

//            if (empty($log_id) || !is_numeric($log_id)) {
//                flash_message('Error', 'Invalid communication log ID provided.', 'error', 'banner', true, 10);
//                return redirect()->back();
//            }

            $rules = [
                'log_id' => 'required|integer|is_not_unique[notifications.id]',
            ];

            $data = $this->request->getGet(array_keys($rules));

            if (!$this->validateData($data, $rules)) {
                log_message('warning', '[WARNING] Invalid communication log ID provided: {data}', [
                    'data' => json_encode($data)
                ]);

                flash_message('Error', 'Invalid communication log ID provided.', 'error', 'banner', true, 10);

                return redirect()->back();
            }

            $log_details = $this->communication_service->getCommunicationLogDetails((int)$data['log_id']);

            if (!$log_details) {
                log_message('warning', '[WARNING] Communication log not found for ID: {log_id}', [
                    'log_id' => $data['log_id']
                ]);

                flash_message('Error', 'Communication log not found.', 'error', 'banner', true, 10);

                return redirect()->back();
            }

            return view('pages/system/communication_log_details', compact('log_details'));
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {message} {trace}', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            flash_message('Error', 'An error occurred while loading the communication log details page.', 'error', 'banner', true, 10);

            return redirect()->back();
        }
    }
}
