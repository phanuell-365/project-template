<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

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
}