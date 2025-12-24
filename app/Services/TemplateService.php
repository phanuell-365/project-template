<?php

namespace App\Services;

use App\Libraries\TemplateRegistry;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class TemplateService extends BaseService
{

    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function renderTemplate(string $slug, string $channel, int $organizationId, array $data = []): array
    {
        // Fetch the template from the database
//        $template = $this->db->table('notification_templates')
//            ->where('slug', $slug)
//            ->where('channel', $channel)
//            ->where('organization_id', $organizationId)
//            ->get()
//            ->getRowArray();

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
            'slug' => $slug,
            'channel' => $channel,
            'organization_id' => $organizationId
        ])->getRowArray();

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

        $parsedSubject = $this->parseString($template['subject'], $data);
        $parsedBody = $this->parseString($template['body'], $data);

        return [
            'success' => true,
            'subject' => $parsedSubject,
            'body' => $parsedBody,
        ];
    }

    private function parseString($text, $data)
    {
        return preg_replace_callback('/{{\s*(\w+)\s*}}/', function ($matches) use ($data) {
            $key = $matches[1]; // The variable name (e.g., 'user_name')
            return $data[$key] ?? ""; // Return value or empty string if missing
        }, $text);
    }

    public function listTemplates(string $org_slug): array
    {
        $templateData = TemplateRegistry::$definitions;

        $sql = "
            SELECT id, slug, subject, body, organization_id, channel, created_at as last_modified
            FROM notification_templates
            WHERE organization_id = (
                SELECT id FROM organizations WHERE slug = :org_slug:
            )
            ORDER BY organization_id DESC
        ";

        $savedTemplates = $this->db->query($sql, [
            'org_slug' => $org_slug
        ])->getResultArray();

        $templates = [];

        foreach ($templateData as $slug => $definition) {
            $templates[$slug] = [
                'name' => $definition['name'],
                'description' => $definition['description'],
                'placeholders' => $definition['placeholders'],
                'channels' => [],
            ];

            foreach (['email', 'sms'] as $channel) {
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
                    'subject' => $savedTemplate['subject'] ?? $defaultSubject,
                    'body' => $savedTemplate['body'] ?? $defaultBody,
                    'is_customized' => (bool)$savedTemplate,
                ];
            }
        }

//        return $templates;

        // We'll flatten the templates for easier rendering in the datatable
        $flattened = [];

        foreach ($templates as $slug => $template) {
            foreach ($template['channels'] as $channel => $channelData) {
                $flattened[] = [
                    'id' => $channelData['is_customized'] ? $template['id'] : $slug . '_' . $channel,
                    'slug' => $slug,
                    'name' => $template['name'],
                    'description' => $template['description'],
                    // get the context from the slug by getting the word before the first dot
                    'context' => explode('.', $slug)[0],
//                    'placeholders' => implode(', ', $template['placeholders']),
                    'channel' => $channel,
                    'subject' => $channelData['subject'],
                    'body' => $channelData['body'],
                    'customized' => $channelData['is_customized'] ? 'Yes' : 'No',
                    'last_modified' => $channelData['is_customized'] ? $template['last_modified'] : 'N/A',
                ];
            }
        }

        return $flattened;
    }

    public function saveTemplate(string $slug, string $channel, int $organizationId, string $subject, string $body): bool
    {
        // Check if template exists
        $existing = $this->db->table('notification_templates')
            ->where('slug', $slug)
            ->where('channel', $channel)
            ->where('organization_id', $organizationId)
            ->get()
            ->getRowArray();

        if ($existing) {
            // Update existing template
            $this->db->table('notification_templates')
                ->where('id', $existing['id'])
                ->update([
                    'subject' => $subject,
                    'body' => $body,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            // Insert new template
            $this->db->table('notification_templates')
                ->insert([
                    'slug' => $slug,
                    'channel' => $channel,
                    'organization_id' => $organizationId,
                    'subject' => $subject,
                    'body' => $body,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        return true;
    }
}