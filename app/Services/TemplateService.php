<?php

namespace App\Services;

use App\Libraries\TemplateRegistry;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class TemplateService extends BaseService
{
    const EMAIL_CHANNEL = 'email';
    const SMS_CHANNEL = 'sms';
    const RAW_EMAIL_CHANNEL = 1;
    const RAW_SMS_CHANNEL = 2;

    protected BaseConnection $db;
    protected array $placeholder_values = [];
    protected $cssConverter;
    protected $baseCss;

    public function __construct()
    {
        $this->db = Database::connect();

        $this->placeholder_values = [
            'auth.new_company'    => [
                'company_name'      => 'Acme Corp',
                'registration_date' => '2024-01-15',
                'app_name'          => 'MyApp',
                'support_email'     => 'support@acmecorp.com',
                'support_phone'     => '+1-800-123-4567',
                'description'       => 'Your company registration was completed successfully.',
            ],
            'auth.new_user'       => [
                'user_name'         => 'John Doe',
                'user_email'        => 'admin@acmecorp.com',
                'login_link'        => 'https://myapp.com/login',
                'company_name'      => 'Acme Corp',
                'user_password'     => 'securepassword123',
                'registration_date' => '2024-01-15',
                'description'       => 'Your user account has been created successfully.',
            ],
            'auth.password_reset' => [
                'user_name'       => 'John Doe',
                'reset_link'      => 'https://myapp.com/reset-password?token=abcdef',
                'expiration_time' => '30 minutes',
                'description'     => 'A password reset request was received for your account.',
            ],
        ];

        $this->cssConverter = new CssToInlineStyles();

        $cssPath = FCPATH . 'css/email-output.css';
        if (file_exists($cssPath)) {
            $this->baseCss = file_get_contents($cssPath);
        } else {
            $this->baseCss = '';

            log_message('warning', 'CSS file for email templates not found at path: {path}', [
                'path' => $cssPath
            ]);
        }
    }

    public function listTemplates(string $org_slug): array
    {
        $templateData = TemplateRegistry::$definitions;

        $sql = "
            SELECT id, slug, subject, body, organization_id, IF(channel = 1,'email', 'sms') as channel, updated_at as last_modified
            FROM notification_templates
            WHERE organization_id = (
                SELECT id FROM organizations WHERE slug = :org_slug:
            )
            ORDER BY organization_id DESC
        ";

        $savedTemplates = $this->db->query($sql, [
            'org_slug' => $org_slug
        ])
            ->getResultArray();

        $templates = [];

        foreach ($templateData as $slug => $definition) {
            $templates[$slug] = [
                'name'         => $definition['name'],
                'description'  => $definition['description'],
                'placeholders' => $definition['placeholders'],
                'channels'     => [],
            ];

            foreach ([
                         'email',
                         'sms'
                     ] as $channel) {
                $defaultSubject = '';

                if ($channel == 'email') {
                    $defaultSubject = $definition['email']['default_subject'] ?? '';
                    $defaultBody = view($definition['email']['default_body_view']);
                } else {
                    $defaultBody = $definition['sms']['default_message'] ?? '';
                }

                // Check for saved template
                $savedTemplate = null;
                foreach ($savedTemplates as $st) {
                    if ($st['slug'] === $slug && $st['channel'] === $channel) {
                        $savedTemplate = $st;
                        break;
                    }
                }

                $templates[$slug]['channels'][$channel] = [
                    'subject'       => $savedTemplate['subject'] ?? $defaultSubject,
                    'body'          => $savedTemplate['body'] ?? $defaultBody,
                    'is_customized' => (bool)$savedTemplate,
                    'id'            => $savedTemplate['id'] ?? null,
                    'last_modified' => $savedTemplate['last_modified'] ?? null,
                ];
            }
        }

//        return $templates;

        // We'll flatten the templates for easier rendering in the datatable
        $flattened = [];

        foreach ($templates as $slug => $template) {
            foreach ($template['channels'] as $channel => $channelData) {
                $flattened[] = [
                    'id'            => $channelData['is_customized'] ? $channelData['id'] : $slug . '_' . $channel,
                    'slug'          => $slug,
                    'name'          => $template['name'],
                    'description'   => $template['description'],
                    // get the context from the slug by getting the word before the first dot
                    'context'       => explode('.', $slug)[0],
                    //                    'placeholders' => implode(', ', $template['placeholders']),
                    'channel'       => $channel,
                    'subject'       => $channelData['subject'],
                    'body'          => $channelData['body'],
                    'customized'    => $channelData['is_customized'] ? 'Yes' : 'No',
                    'last_modified' => $channelData['is_customized'] ? $channelData['last_modified'] : null,
                ];
            }
        }

        return $flattened;
    }

    public function saveTemplate(string $org_slug, string $slug, string $channel, string | null $subject, string $body): array
    {
        // Check if template exists
//        $existing = $this->db->table('notification_templates')
//            ->where('slug', $slug)
//            ->where('channel', $channel)
//            ->where('organization_id', $organizationId)
//            ->get()
//            ->getRowArray();
//
//        if ($existing) {
//            // Update existing template
//            $this->db->table('notification_templates')
//                ->where('id', $existing['id'])
//                ->update([
//                    'subject'    => $subject,
//                    'body'       => $body,
//                    'updated_at' => date('Y-m-d H:i:s'),
//                ]);
//        } else {
//            // Insert new template
//            $this->db->table('notification_templates')
//                ->insert([
//                    'slug'            => $slug,
//                    'channel'         => $channel,
//                    'organization_id' => $organizationId,
//                    'subject'         => $subject,
//                    'body'            => $body,
//                    'created_at'      => date('Y-m-d H:i:s'),
//                    'updated_at'      => date('Y-m-d H:i:s'),
//                ]);
//        }
//
//        return true;

        $sql = "
            SELECT id
            FROM notification_templates
            WHERE slug = :slug:
              AND channel = :channel:
              AND organization_id = (
                  SELECT id FROM organizations WHERE slug = :org_slug:
              )
            LIMIT 1
        ";

        $existing = $this->db->query($sql, [
            'slug'     => $slug,
            'channel'  => $channel === self::EMAIL_CHANNEL || $channel == self::RAW_EMAIL_CHANNEL ? 1 : 2,
            'org_slug' => $org_slug,
        ])
            ->getRowArray();

        if ($existing) {
            // Update existing template
            $this->db->table('notification_templates')
                ->where('id', $existing['id'])
                ->update([
                    'subject'    => $subject,
                    'body'       => $body,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $organization_id = session()->get('org_id');            // Insert new template

            // For SMS, since there is no subject, we can store an empty string or use descriptive text
            if ($channel === self::SMS_CHANNEL) {
                $subject = '';
            }

            $this->db->table('notification_templates')
                ->insert([
                    'organization_id' => $organization_id,
//                    'channel'         => $channel == 'email' ? 0 : 1,
                    'channel'         => $channel === self::EMAIL_CHANNEL || $channel == self::RAW_EMAIL_CHANNEL ? 1 : 2,
                    'slug'            => $slug,
                    'subject'         => $subject,
                    'body'            => $body,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
        }

        return [
            'success' => true,
            'message' => 'Template saved successfully.'
        ];
    }

    public function getTemplateBySlug(string $org_slug, string $slug, string $channel): ?array
    {
        $sql = "
            SELECT nt.id, nt.slug, nt.subject, nt.body, nt.organization_id, nt.channel
            FROM notification_templates nt
            JOIN organizations o ON nt.organization_id = o.id
            WHERE o.slug = :org_slug:
              AND nt.slug = :slug:
              AND nt.channel = :channel:
            LIMIT 1
        ";

        $template = $this->db->query($sql, [
            'org_slug' => $org_slug,
            'slug'     => $slug,
//            'channel'  => $channel == 'email' ? 0 : 1,
            'channel'  => $channel === self::EMAIL_CHANNEL || $channel == self::RAW_EMAIL_CHANNEL ? 1 : 2,
        ])
            ->getRowArray();

        $def = TemplateRegistry::$definitions[$slug] ?? null;
        if (!$def) {
            return [
                'success' => false,
                'message' => 'Template not found.'
            ];
        }

        // If not found, remember we have default templates in the registry
        if (!$template) {
            // Use default template from registry
            $template = [];
            if ($channel == 'email') {
                $template['subject'] = $def['email']['default_subject'] ?? '';
                // Load body from view file
                $template['body'] = view($def['email']['default_body_view']);
            } else {
                $template['body'] = $def['sms']['default_message'] ?? '';
            }

            $template['slug'] = $slug;
            $template['channel'] = $channel;
            $template['description'] = $def['description'];
        }

        // Get the placeholders so that the edit view can show them
        $template['placeholders'] = $def['placeholders'] ?? [];
        $template['name'] = $def['name'];

        return [
            'success' => true,
            'data'    => $template,
        ];
    }

    public function previewTemplate(string $org_slug, string $slug, string $channel, string | null $new_template = null): array
    {
        // But to ensure we get any organization-specific templates, we need the actual org ID
        $org = $this->db->table('organizations')
            ->where('slug', $org_slug)
            ->get()
            ->getRowArray();

        if (!$org) {
            return [
                'success' => false,
                'message' => 'Organization not found for preview.'
            ];
        }

        $organizationId = $org['id'];

        // Get placeholder values for this template
        $placeholders = $this->placeholder_values[$slug] ?? [];

        // Add 'subject' placeholder
        if (!isset($placeholders['subject'])) {
            $placeholders['subject'] = 'This is a preview subject';
        }

        log_message('debug', 'Generating preview for template {slug} for {channel} in organization {org_slug} with placeholders: {placeholders}', [
            'slug'         => $slug,
            'org_slug'     => $org_slug,
            'channel'      => $channel,
            'placeholders' => json_encode($placeholders, JSON_PRETTY_PRINT)
        ]);
        $rendered = $this->renderTemplate($slug, $channel, $organizationId, $placeholders, $new_template);

        if (!$rendered['success']) {
            return [
                'success' => false,
                'message' => 'Failed to render template for preview: ' . ($rendered['message'] ?? 'Unknown error')
            ];
        }

        return [
            'success' => true,
            'subject' => $rendered['subject'],
            'body'    => $rendered['body'],
        ];
    }

    public function renderTemplate(string $slug, string $channel, int $organizationId, array $data = [], string | null $new_template = null): array
    {
        // Fetch the template from the database
//        $template = $this->db->table('notification_templates')
//            ->where('slug', $slug)
//            ->where('channel', $channel)
//            ->where('organization_id', $organizationId)
//            ->get()
//            ->getRowArray();

        if ($new_template) {
            // Add the email layout if channel is email
//            if ($channel == 'email' || $channel == 0) {
            if ($channel === self::EMAIL_CHANNEL || $channel == self::RAW_EMAIL_CHANNEL) {
                $emailLayout = view('emails/default', [
                    'content_body' => $new_template
                ]);

                $inlined = $this->cssConverter->convert(
                    $emailLayout,
                    $this->baseCss
                );

//                log_message('debug', 'Inlined email template for slug {slug}: {template}', [
//                    'slug'     => $slug,
//                    'template' => $inlined
//                ]);

                $new_template = $inlined;
            }

            // Use the new template provided
            $template = [
                'subject' => $data['subject'] ?? '',
                'body'    => $new_template,
            ];

        } else {
            $sql = "
            SELECT id, slug, subject, body, organization_id, channel
            FROM notification_templates 
            WHERE slug = :slug:
              AND channel = :channel:
              AND (organization_id = :organization_id: OR organization_id IS NULL) 
            ORDER BY organization_id DESC 
            LIMIT 1
        ";

            $template = $this->db->query($sql, [
                'slug'            => $slug,
                'channel'         => (int)$channel,
                'organization_id' => $organizationId
            ])
                ->getRowArray();

            log_message('debug', 'Fetched template for slug {slug} and channel {channel} in organization ID {org_id}: {template}', [
                'slug'     => $slug,
                'channel'  => $channel,
                'org_id'   => $organizationId,
                'template' => json_encode($template, JSON_PRETTY_PRINT)
            ]);

            if (!$template) {
                $def = TemplateRegistry::$definitions[$slug] ?? null;
                if (!$def) {
                    return [
                        'success' => false,
                        'message' => 'Template not found in database or registry.'
                    ];
                }

                // Use default template from registry
                // We'll get the default templates from the registry
                // For email channels, we need to load the body from view files
                $template = [];

                if ($channel == 'email') {
                    $template['subject'] = $def['email']['default_subject'] ?? '';
                    // Load body from view file
                    $template['body'] = view($def['email']['default_body_view']);
                } else {
                    $template['body'] = $def['body'] ?? '';
                }
            }

            // Add the email layout if channel is email
            if ($channel == 'email') {
                $emailLayout = view('emails/default', [
                    'content_body' => $template['body']
                ]);

                $inlined = $this->cssConverter->convert(
                    $emailLayout,
                    $this->baseCss
                );

                $template['body'] = $inlined;
            }
        }

        $parsedSubject = $this->parseString($template['subject'], $data);
        $parsedBody = $this->parseString($template['body'], $data);

        return [
            'success' => true,
            'subject' => $parsedSubject,
            'body'    => $parsedBody,
        ];
    }

    private function parseString($text, $data): array | string | null
    {
        return preg_replace_callback('/{{\s*(\w+)\s*}}/', function ($matches) use ($data) {
            $key = $matches[1];       // The variable name (e.g., 'user_name')
            return $data[$key] ?? ""; // Return value or empty string if missing
        }, $text);
    }
}