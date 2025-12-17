<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Exception;

class GroupsService extends BaseService
{

    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function myGroups(int $userId, int $orgId): array
    {
        $sql = "
            SELECT g.id, g.name, g.description
            FROM user_groups ug
            INNER JOIN groups g ON ug.group_id = g.id
            WHERE ug.user_id = :user_id:
              AND ug.org_id = :org_id:
              AND ug.deleted_at IS NULL
              AND g.deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'user_id' => $userId,
            'org_id'  => $orgId,
        ]);

        return $query->getResultArray();
    }

    public function getGroupById(int $groupId): ?array
    {
        $sql = "
            SELECT id, name, description
            FROM groups
            WHERE id = :group_id:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'group_id' => $groupId,
        ]);

        return $query->getRowArray();
    }

    public function getGroupByName(string $groupName): ?array
    {
        $sql = "
            SELECT id, name, description
            FROM groups
            WHERE name = :group_name:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'group_name' => $groupName,
        ]);

        return $query->getRowArray();
    }

    public function listPackageGroupPermissionsTemplates($packageId): array
    {
        $sql = "
            SELECT id, name, description, permission_json
            FROM package_group_permissions_templates
            WHERE package_id = :package_id:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'package_id' => $packageId,
        ]);

        $templates = $query->getResultArray();

        foreach ($templates as &$template) {
            $template['permission_json'] = json_decode($template['permission_json'], true) ? : [];
        }

        return $templates;
    }

    public function getPackageGroupPermissionsTemplateById(int $templateId): ?array
    {
        $sql = "
            SELECT id, name, description, permission_json
            FROM package_group_permissions_templates
            WHERE id = :template_id:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'template_id' => $templateId,
        ]);

        $template = $query->getRowArray();

        if ($template) {
            $template['permission_json'] = json_decode($template['permission_json'], true) ? : [];
        }

        return $template;
    }

    public function savePackageGroupPermissionsTemplate(int $packageId, int | string $templateId, array $data)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $permissionJson = json_encode($data['permission_json']);

            // create a slug from the name
            $slug = url_title($data['name'], '-', true);

            log_message('debug', 'Saving Package Group Permissions Template: ' . json_encode([
                    'package_id'      => $packageId,
                    'template_id'     => $templateId,
                    'name'            => $data['name'],
                    'description'     => $data['description'],
                    'permission_json' => $data['permission_json'],
                    'slug'            => $slug,
                ], JSON_PRETTY_PRINT));

            if ($templateId !== 'new') {
                // Update existing template
                $sql = "
                UPDATE package_group_permissions_templates
                SET name = :name:, description = :description:, permission_json = :permission_json:, updated_at = :updated_at:, slug = :slug:
                WHERE id = :template_id:
            ";

                $this->db->query($sql, [
                    'name'            => $data['name'],
                    'description'     => $data['description'],
                    'permission_json' => $permissionJson,
                    'updated_at'      => $now,
                    'template_id'     => $templateId,
                    'slug'            => $slug,
                ]);
            } else {
                // Insert new template
                $sql = "
                INSERT INTO package_group_permissions_templates (package_id, name, description, permission_json, created_at, updated_at, slug)
                VALUES (:package_id:, :name:, :description:, :permission_json:, :created_at:, :updated_at:, :slug:)
            ";

                $this->db->query($sql, [
                    'package_id'      => $packageId,
                    'name'            => $data['name'],
                    'description'     => $data['description'],
                    'permission_json' => $permissionJson,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                    'slug'            => $slug,
                ]);

                $templateId = $this->db->insertID();
            }

            return [
                'success'     => true,
                'message'     => 'Template saved successfully.',
                'template_id' => $templateId
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saving template: ' . $e->getMessage(),
            ];
        }
    }

    public function getPackageGroupPermissionsTemplateBySlug($packageId, $slug): ?array
    {
        $sql = "
            SELECT id, name, description, permission_json
            FROM package_group_permissions_templates
            WHERE package_id = :package_id:
              AND slug = :slug:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'package_id' => $packageId,
            'slug'       => $slug,
        ]);

        $template = $query->getRowArray();

        if ($template) {
            $template['permission_json'] = json_decode($template['permission_json'], true) ? : [];
        }

        return $template;
    }

    public function deletePackageGroupPermissionsTemplate(int $templateId): array
    {
        $now = date('Y-m-d H:i:s');

        $sql = "
            UPDATE package_group_permissions_templates
            SET deleted_at = :deleted_at:
            WHERE id = :template_id:
        ";

        $this->db->query($sql, [
            'deleted_at'  => $now,
            'template_id' => $templateId,
        ]);

//        return $this->db->affectedRows() > 0;
        return [
            'success' => true,
            'message' => 'Template deleted successfully.'
        ];
    }

    public function getGroupsUnderOrganization(string $orgSlug): array
    {
        $sql = "
            SELECT g.id, g.name, g.description
            FROM groups g
            INNER JOIN organizations o ON g.organization_id = o.id
            WHERE o.slug = :org_slug:
              AND g.deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'org_slug' => $orgSlug,
        ]);

        return $query->getResultArray();
    }
}