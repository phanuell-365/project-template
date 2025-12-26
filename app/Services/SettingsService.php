<?php

namespace App\Services;

use App\Libraries\SettingsSchema;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Exception;

class SettingsService extends BaseService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function checkIfSectionExists(string $section): bool
    {
        return isset(SettingsSchema::$structure[$section]);
    }

    public function getRenderableFormBySection(string $section): array
    {
        // Check if section exists
        if (!isset(SettingsSchema::$structure[$section])) {
            return [
                'success' => false,
                'message' => 'Section not found.',
            ];
        }

        $sectionData = SettingsSchema::$structure[$section];

//        $savedSettings = $this->db->table('general_settings')
//            ->where('section', $section)
//            ->get()
//            ->getResultArray();

        $sql = "
            SELECT setting_key, setting_value
            FROM general_settings
            WHERE context = :context:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'context' => $section,
        ]);

        $savedSettings = $query->getResultArray();

        // Flatten saved settings for easy access
        $savedSettingsMap = [];

        foreach ($savedSettings as $setting) {
            $savedSettingsMap[$setting['setting_key']] = $setting['setting_value'];
        }

        // Merge saved values into the structure
        foreach ($sectionData['value'] as $key => &$setting) {
            if (isset($savedSettingsMap[$key])) {
                $setting['value'] = $savedSettingsMap[$key];
            } else {

                // Since PHP doesn't support expressions in array definitions, we handle dynamic defaults here
                // Firstly, if the current section is 'email' then;

                if ($section === 'email') {
                    if ($key === 'from_address' && empty($setting['value'])) {
                        // Set default from_address to system email from env if not set
                        $setting['value'] = getenv('EMAIL_SMTP_MAIL') ? : '';
                    } elseif ($key === 'from_name' && empty($setting['value'])) {
                        // Set default from_name to app name from env if not set
                        $setting['value'] = getenv('EMAIL_SMTP_NAME') ? : 'My Application';
                    } elseif ($key === 'smtp_port' && empty($setting['value'])) {
                        // Set default smtp_port to 587 if not set
                        $setting['value'] = getenv('EMAIL_SMTP_PORT') ? : 587;
                    } elseif ($key === 'smtp_host' && empty($setting['value'])) {
                        // Set default smtp_host to localhost if not set
                        $setting['value'] = getenv('EMAIL_SMTP_HOST') ? : 'localhost';
                    } elseif ($key === 'smtp_pass' && empty($setting['value'])) {
                        // Set default smtp_password to env variable if not set
                        $setting['value'] = getenv('EMAIL_SMTP_PASS') ? : '';
                    } elseif ($key === 'smtp_username' && empty($setting['value'])) {
                        // Set default smtp_username to env variable if not set
                        $setting['value'] = getenv('EMAIL_SMTP_MAIL') ? : '';
                    } else {
                        $setting['value'] = $setting['default'] ?? null;
                    }
                } else {
                    $setting['value'] = $setting['default'] ?? null;
                }


            }
        }

        // Generate other necessary attributes for rendering depending on type
        // For example, for select fields, you might want to prepare options, etc.

        foreach ($sectionData['value'] as $key => &$setting) {
            switch ($setting['type']) {
                case 'radio':
                case 'select':
                    // Ensure options are available
                    if (!isset($setting['options']) || !is_array($setting['options'])) {
                        $setting['options'] = [];
                    }
                    break;
                case 'checkbox':
                    // Convert value to boolean
                    $setting['value'] = filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'textarea':
                    // Additional attributes for textarea if needed
                    $setting['rows'] = 4;
                    break;
                case 'file':
                    // File upload specific attributes
                    $setting['accept'] = $setting['accepted-mime-types'] ?? '*/*';
                    $setting['size'] = $setting['max-file-size'] ?? '5MB';
                    break;
            }

            $setting['id'] = $key;
            $setting['name'] = $key;
            $setting['outer-class'] = 'basis-3/4 md:ml-0 md:mr-2';

            // Add aria labels for accessibility
            $setting['aria-label'] = $setting['label'] ?? $key;
        }


        return [
            'success' => true,
            'data'    => [
                'title' => $sectionData['title'],
                'icon'  => $sectionData['icon'],
                'value' => $sectionData['value'],
                'key'   => $section,
            ],
        ];
    }

    public function saveSettings(string $section, array $settings, int $organization_id): array
    {
        $now = date('Y-m-d H:i:s');

        try {
            foreach ($settings as $key => $value) {
                // Check if setting already exists
                $existing = $this->db->table('general_settings')
                    ->where('setting_key', $key)
                    ->where('context', $section)
                    ->where('organization_id', $organization_id)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    // Update existing setting
                    $this->db->table('general_settings')
                        ->where('id', $existing['id'])
                        ->update([
                            'setting_value' => $value,
                            'updated_at'    => $now,
                        ]);
                } else {
                    // Insert new setting
                    $this->db->table('general_settings')
                        ->insert([
                            'setting_key'   => $key,
                            'setting_value' => $value,
                            'context'       => $section,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                            'organization_id' => $organization_id,
                        ]);
                }
            }

            return [
                'success' => true,
                'message' => 'Settings saved successfully.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saving settings: ' . $e->getMessage(),
            ];
        }

    }

    public function getRenderableSettingsSections(string $active_section, string $org_slug): array
    {
        $sections = [];

        foreach (SettingsSchema::$structure as $sectionKey => $sectionData) {
            $sections[] = [
                'key'   => $sectionKey,
                'title' => $sectionData['title'],
                'icon'  => $sectionData['icon'],
                'order' => $sectionData['order'] ?? 0,
            ];
        }

        // Sort sections by order
        usort($sections, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

//        return $sections;

        $html = '<div class="border-b border-gray-200 overflow-hidden">
                <nav class="flex gap-x-1 overflow-x-auto [&::-webkit-scrollbar]:h-0 snap-mandatory snap-x scroll-pb-1" aria-label="Tabs" role="tablist" aria-orientation="horizontal">';

        foreach ($sections as $section) {
            $isActive = ($section['key'] === $active_section) ? 'active' : '';
            $isActiveClass = ($section['key'] === $active_section) ? 'bg-white border-b-transparent text-soko-600' : '';
            $html .= '<a href="' . route_to('general-settings-view', $org_slug) . '?section=' . $section['key'] . '#settings-tab-item-' . esc($section['key']) . '"
                    class="snap-start py-3 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border border-gray-200 text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-hidden focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none' . $isActive . ' ' . $isActiveClass . '"
                    id="settings-tab-item-' . esc($section['key']) . '" aria-selected="' . ($isActive ? 'true' : 'false') . '" data-hs-tab="#' . esc($section['key']) . '-tab"
                    aria-controls="' . esc($section['key']) . '-tab" role="tab">
                        <div class="' . ($isActive ? 'bg-soko-100 text-soko-600' : 'bg-gray-200 text-gray-500') . ' rounded-full p-1.5">
                            <span class="material-symbols-rounded">
                                ' . esc($section['icon']) . '
                            </span>
                        </div>
                    <span class="text-nowrap">' . esc($section['title']) . '</span>
            </a>';
        }

        $html .= '</nav></div>';

        return [
            'sections' => $sections,
            'html'     => $html,
        ];
    }

    public function resetSettings(string $section): array
    {
        try {
            $this->db->table('general_settings')
                ->where('context', $section)
                ->delete();

            return [
                'success' => true,
                'message' => 'Settings reset to default successfully.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error resetting settings: ' . $e->getMessage(),
            ];
        }
    }

    public function getSiteLogoUrl(string $org_slug): ?string
    {
        $sql = "
            SELECT setting_value
            FROM general_settings
            WHERE context = :context:
              AND setting_key = :setting_key:
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $query = $this->db->query($sql, [
            'context'     => 'site',
            'setting_key' => 'site_logo',
        ]);

        $result = $query->getRowArray();

        if ($result && !empty($result['setting_value'])) {
            return base_url('uploads/' . $result['setting_value']);
        }

        return null;
    }

    public function getSiteFaviconUrl(string $org_slug): ?string
    {
        $sql = "
            SELECT setting_value
            FROM general_settings
            WHERE context = :context:
              AND setting_key = :setting_key:
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $query = $this->db->query($sql, [
            'context'     => 'site',
            'setting_key' => 'site_favicon',
        ]);

        $result = $query->getRowArray();

        if ($result && !empty($result['setting_value'])) {
            return base_url('uploads/' . $result['setting_value']);
        }

        return null;
    }

    public function getSiteName(string $org_slug): ?string
    {
        $sql = "
            SELECT setting_value
            FROM general_settings
            WHERE context = :context:
              AND setting_key = :setting_key:
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $query = $this->db->query($sql, [
            'context'     => 'site',
            'setting_key' => 'site_name',
        ]);

        $result = $query->getRowArray();

        if ($result && !empty($result['setting_value'])) {
            return $result['setting_value'];
        }

        return null;
    }

    public function getSetting(string $org_slug, string $setting_key): ?string
    {
        $sql = "
            SELECT setting_value
            FROM general_settings
            WHERE context = :context:
              AND setting_key = :setting_key:
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $query = $this->db->query($sql, [
            'context'     => 'site',
            'setting_key' => $setting_key,
        ]);

        $result = $query->getRowArray();

        if ($result && !empty($result['setting_value'])) {
            return $result['setting_value'];
        }

        return null;
    }
}
