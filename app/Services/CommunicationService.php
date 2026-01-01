<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Email\Email;
use Config\Database;
use Config\Services;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CommunicationService
{
    const PENDING = 'pending';
    const SENT = 'completed';
    const FAILED = 'failed';
    const PROCESSING = 'processing';

    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';


    protected BaseConnection $db;
    protected TemplateService $template_service;
    protected SettingsService $settings_service;
    protected Client $client;
    protected Email $email_service;

    public function __construct()
    {
        $this->db = Database::connect();

        $this->template_service = Services::templates_service();

        $this->settings_service = Services::settings_service();

        $this->client = new Client();

        $this->email_service = Services::email();
    }

    /**
     * @param string $recipient - Email or phone number of the recipient
     * @param string $channel - Channel to send the notification (email or sms)
     * @param string $template_slug - Slug of the template to use
     * @param array $data - An array defining values to replace in the template
     * @param int $organization_id - The Id of the organization
     * @param string $priority - Priority of the notification (low, medium, high)
     * @return array
     */
    public function dispatchNotification(string $recipient, string $channel, string $template_slug, array $data, int $organization_id, string $priority): array
    {
        $org_data = $this->getOrganizationData($organization_id);

        // Fetch and process template
        $template = $this->template_service->getTemplateBySlug($org_data['slug'], $template_slug, $channel);

        if (!$template['success']) {
            return $template;
        }

        log_message('debug', '[DEBUG] Fetched template: {template}', [
            'template' => json_encode($template['data'], JSON_PRETTY_PRINT),
        ]);

        $template = $template['data'];

        $renderable_notification = $this->template_service->renderTemplate($template['slug'], $template['channel'], $organization_id, $data);

        if (!$renderable_notification['success']) {
            return $renderable_notification;
        }

        log_message('debug', '[DEBUG] Rendered notification: {notification}', [
            'notification' => json_encode($renderable_notification, JSON_PRETTY_PRINT),
        ]);

        // Prepare notification data
        $notificationData = [
            'organization_id' => $organization_id,
            'subject'         => $renderable_notification['subject'],
            'body'            => $renderable_notification['body'],
            'recipient_email' => $channel === self::CHANNEL_EMAIL ? $recipient : null,
            'recipient_phone' => $channel === self::CHANNEL_SMS ? $recipient : null,
            'channel'         => $channel,
            'priority'        => $priority,
            'status'          => self::PENDING,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        log_message('debug', '[DEBUG] Prepared notification data: {data}', [
            'data' => json_encode($notificationData, JSON_PRETTY_PRINT),
        ]);

        if ($priority === self::PRIORITY_HIGH) {
            return $this->sendNow($notificationData);
        } else {
            return $this->addToQueue($notificationData)
                ? [
                    'success' => true,
                    'message' => 'Notification queued successfully.'
                ]
                : [
                    'success' => false,
                    'message' => 'Failed to queue notification.'
                ];
        }
    }

    private function getOrganizationData(int $organization_id): array
    {
        $organization = $this->db->table('organizations')
            ->where('id', $organization_id)
            ->get()
            ->getRowArray();

        return $organization ? : [];
    }

    private function sendNow(array $data): array
    {
        try {
            // Insert the data into notifications table

            $this->db->table('notifications')
                ->insert($data);

            $inserted_id = $this->db->insertID();

            if ($data['channel'] === self::CHANNEL_EMAIL) {
                $email_payload = [
                    'org_slug' => $this->getOrganizationData($data['organization_id'])['slug'],
                    'to'       => $data['recipient_email'],
                    'subject'  => $data['subject'],
                    'body'     => $data['body'],
                ];

                // Update status to processing
                $this->db->table('notifications')
                    ->where('id', $inserted_id)
                    ->update([
                        'status'     => self::PROCESSING,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $result = $this->sendEmail($email_payload);
            } elseif ($data['channel'] === self::CHANNEL_SMS) {
                $sms_payload = [
                    'org_slug' => $this->getOrganizationData($data['organization_id'])['slug'],
                    'phone'    => $data['recipient_phone'],
                    'message'  => $data['body'],
                ];

                // Update status to processing
                $this->db->table('notifications')
                    ->where('id', $inserted_id)
                    ->update([
                        'status'     => self::PROCESSING,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $result = $this->sendSms($sms_payload);
            } else {
                $result = [
                    'success' => false,
                    'message' => 'Invalid notification channel specified.',
                ];
            }

            // Update notification status based on result
            $new_status = $result['success'] ? self::SENT : self::FAILED;
            $this->db->table('notifications')
                ->where('id', $inserted_id)
                ->update([
                    'status'        => $new_status,
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'sent_at'       => date('Y-m-d H:i:s'),
                    'response_json' => isset($result['response']) ? json_encode($result['response']) : null,
                ]);

            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while sending notification: ' . $e->getMessage(),
            ];
        }
    }

    private function sendEmail($payload): array
    {
        try {
            $to = $payload['to'];
            $subject = $payload['subject'];
            $body = $payload['body'];

            // Initialize email service
            $email_settings = $this->settings_service->getSettingsBySection($payload['org_slug'], 'email');

            log_message('debug', '[DEBUG] Email settings: {settings}', [
                'settings' => json_encode($email_settings, JSON_PRETTY_PRINT),
            ]);

            $email_config = [
                'SMTPHost'   => $email_settings['smtp_host'],
                'SMTPUser'   => $email_settings['smtp_username'],
                'SMTPPass'   => $email_settings['smtp_pass'],
                'SMTPPort'   => (int)$email_settings['smtp_port'],
                'SMTPCrypto' => $this->settings_service->getSetting($payload['org_slug'], 'email', 'smtp_crypto') ?? '',
                'mailType'   => $this->settings_service->getSetting($payload['org_slug'], 'email', 'mail_type') ?? 'html',
                'charset'    => $this->settings_service->getSetting($payload['org_slug'], 'email', 'charset') ?? 'UTF-8',
                'protocol'   => $this->settings_service->getSetting($payload['org_slug'], 'email', 'protocol') ?? 'smtp',
                'CRLF'       => "\r\n",
            ];

            $this->email_service->initialize($email_config);
            $this->email_service->setFrom($email_settings['from_address'], $email_settings['from_name']);

            $this->email_service->setTo($to);
            $this->email_service->setSubject($subject);
            $this->email_service->setMessage($body);

            if ($this->email_service->send()) {
                return [
                    'success' => true,
                    'message' => 'Email sent successfully.',
                ];
            } else {

                log_message('error', '[ERROR] Email sending failed: {debug}', [
                    'debug' => $this->email_service->printDebugger([
                        'headers',
                        'subject'
                    ])
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send email.',
                ];
            }
        } catch (Exception $e) {

            log_message('error', '[ERROR] Error sending email: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending email: ' . $e->getMessage(),
            ];
        }
    }

    private function sendSms($payload): array
    {
        try {
            $phone = $payload['phone'];
            $message = $payload['message'];

            $sms_settings = $this->settings_service->getSettingsBySection($payload['org_slug'], 'sms');

            $data = [
                'apikey'    => $sms_settings['api_key'],
                'partnerID' => $sms_settings['partner_id'],
                'shortcode' => $sms_settings['short_code'],
                'pass_type' => $sms_settings['pass_type'],
                'mobile'    => $phone,
                'message'   => $message,
            ];

            log_message('debug', '[DEBUG] SMS settings: {settings}', [
                'settings' => json_encode($sms_settings, JSON_PRETTY_PRINT),
            ]);

            log_message('debug', '[DEBUG] SMS payload: {payload}', [
                'payload' => json_encode($data, JSON_PRETTY_PRINT),
            ]);

            $response = $this->client->request('POST', $sms_settings['gateway_url'], [
                'form_params' => $data,
                'timeout'     => $this->settings_service->getSetting($payload['org_slug'], 'sms', 'timeout') ?? 30,
            ]);

            $response_body = $response->getBody();
            $response_code = $response->getStatusCode();

            if ($response_code === 200) {
                return [
                    'success'  => true,
                    'message'  => 'SMS sent successfully.',
                    'response' => $response_body,
                ];
            } else {
                return [
                    'success'  => false,
                    'message'  => 'Failed to send SMS. Response code: ' . $response_code,
                    'response' => $response_body,
                ];
            }
        } catch (Exception $e) {

            log_message('error', '[ERROR] Error sending SMS: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending SMS: ' . $e->getMessage(),
            ];
        } catch (GuzzleException $e) {
            log_message('error', '[ERROR] Guzzle error sending SMS: {message} {stack}', [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'A Guzzle error occurred while sending SMS: ' . $e->getMessage(),
            ];
        }
    }

    public function addToQueue(array $data): array
    {
        try {

            $notificationData = [
                'organization_id' => $data['organization_id'],
                'subject'         => $data['subject'],
                'body'            => $data['body'],
                'recipient_email' => $data['recipient_email'] ?? null,
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'channel'         => $data['channel'] ?? self::CHANNEL_EMAIL,
                'priority'        => $data['priority'] ?? self::PRIORITY_MEDIUM,
                'status'          => self::PENDING,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            $db_response = $this->db->table('notifications')
                ->insert($notificationData);

            if ($db_response) {
                return [
                    'success' => true,
                    'message' => 'Notification added to queue successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add notification to queue.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while adding to queue: ' . $e->getMessage(),
            ];
        }
    }

    public function getCommunicationLogs(array $filters = []): array
    {
        $builder = $this->db->table('notifications')
//        ->join('organizations', 'notifications.organization_id = organizations.id', 'left');
        ->join('organizations AS o', 'notifications.organization_id = o.id', 'left')
        ->select('notifications.*, o.name AS organization_name, o.slug AS organization_slug');

        if (isset($filters['organization_id'])) {
            $builder->where('organization_id', $filters['organization_id']);
        }

        if (isset($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (isset($filters['channel'])) {
            $builder->where('channel', $filters['channel']);
        }

        if (isset($filters['date_from'])) {
            // convert date from d/m/Y to Y-m-d
            $date_from = DateTime::createFromFormat('d/m/Y', $filters['date_from'])->format('Y-m-d');
//            $builder->where('DATE(notifications.created_at) >=', $filters['date_from']);
            $builder->where('DATE(notifications.created_at) >=', $date_from);
        }

        if (isset($filters['date_to'])) {
//            $builder->where('DATE(notifications.created_at) <=', $filters['date_to']);
            // convert date from d/m/Y to Y-m-d
            $date_to = DateTime::createFromFormat('d/m/Y', $filters['date_to'])->format('Y-m-d');
            $builder->where('DATE(notifications.created_at) <=', $date_to);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getCommunicationLogDetails(int $log_id): array
    {
//        $log = $this->db->table('notifications')
//            ->join('organizations AS o', 'notifications.organization_id = o.id', 'left')
//            ->select('notifications.*, o.name AS organization_name, o.slug AS organization_slug')
//            ->where('notifications.id', $log_id)
//            ->get()
//            ->getRowArray();

//        return $log ? : [];

        // Include the organization details, user's details if any, etc.
        $sql = "
            SELECT n.*, 
                   o.name AS organization_name, 
                   o.slug AS organization_slug,
                   o.contact_email AS organization_email,
                   o.contact_phone AS organization_phone,
                   o.address AS organization_address
            FROM notifications n
            LEFT JOIN organizations o ON n.organization_id = o.id
            WHERE n.id = :log_id:
        ";

        $log = $this->db->query($sql, ['log_id' => $log_id])->getRowArray();

        return $log ? : [];
    }
}