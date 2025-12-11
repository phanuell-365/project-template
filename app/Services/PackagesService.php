<?php

namespace App\Services;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\ValidationInterface;
use Config\Database;
use Config\Services;

class PackagesService extends BaseService
{

    protected BaseConnection $db;
    private ValidationInterface $validation;

    public function __construct()
    {
        $this->db = Database::connect();

        $this->validation = Services::validation();
    }

    public function getPackageById(int $packageId): ?array
    {
        $sql = "
            SELECT id, name, description, price, duration_days
            FROM packages
            WHERE id = :package_id:
              AND deleted_at IS NULL
        ";

        $query = $this->db->query($sql, [
            'package_id' => $packageId,
        ]);

        return $query->getRowArray();
    }

    public function createPackage(array $data): array
    {
//        $this->db->table('packages')->insert($data);
        // format data for insert

        // features field was string with comma separated values, convert to json
        $features = explode(',', $data['features'] ?? '');
        $data['features'] = json_encode($features);

        // status field was either 'active' or 'inactive', convert to boolean
        $data['status'] = ($data['status'] ?? 'inactive') === 'active' ? 1 : 0;

        // create the slug from name
        $data['slug'] = strtolower(str_replace(' ', '-', $data['name'] ?? ''));

        // Set validation rules for the slug to be unique
        $this->validation->setRules([
            'slug' => 'is_unique[packages.slug]',
        ]);

        if (!$this->validation->run(['slug' => $data['slug']])) {
//            throw new \RuntimeException('Package slug must be unique.');
            return [
                'success' => false,
                'message' => 'Package slug must be unique. The generated slug "' . $data['slug'] . '" already exists.',
            ];
        }

        $insertData = [
            ...$data,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('packages')
            ->insert($insertData);

//        return $this->db->insertID();
        return [
            'success'    => true,
            'package_id' => $this->db->insertID(),
        ];
    }

    public function listPackages(): array
    {
        $sql = "
            SELECT id, name, description, price, duration_days, features, status, max_users
            FROM packages
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
        ";

        $query = $this->db->query($sql);

        $packages = $query->getResultArray();

        // Now, for every package, find their usages, how many organzations are using them and the number of permissions

        foreach ($packages as &$package) {
            // Get number of organizations using this package
            $usageQuery = $this->db->query("
                SELECT COUNT(*) AS organization_count
                FROM organizations
                WHERE package_id = :package_id:
                  AND deleted_at IS NULL
            ", [
                'package_id' => $package['id'],
            ]);

            $usageResult = $usageQuery->getRowArray();
            $package['organization_count'] = $usageResult['organization_count'] ?? 0;

            // Decode features from JSON to array
            $package['features'] = json_decode($package['features'], true) ? : [];

            // Get number of permissions associated with this package
            $permissionsQuery = $this->db->query("
                SELECT COUNT(*) AS permission_count
                FROM package_permissions
                WHERE package_id = :package_id:
                  AND deleted_at IS NULL
            ", [
                'package_id' => $package['id'],
            ]);

            $permissionsResult = $permissionsQuery->getRowArray();
            $package['permission_count'] = $permissionsResult['permission_count'] ?? 0;
        }

        return $packages;
    }

    public function editPackage(int $packageId, array $data): array
    {
        // format data for update

        // features field was string with comma separated values, convert to json
        if (isset($data['features'])) {
            $features = explode(',', $data['features']);
            $data['features'] = json_encode($features);
        }

        // status field was either 'active' or 'inactive', convert to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active' ? 1 : 0;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->table('packages')
            ->where('id', $packageId)
            ->update($data);

        return [
            'success' => true,
            'message' => 'Package updated successfully.',
        ];
    }

    public function deletePackage(int $packageId): array
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('packages')
            ->where('id', $packageId)
            ->update($data);

        return [
            'success' => true,
            'message' => 'Package deleted successfully.',
        ];
    }

    public function __destruct()
    {
        $this->db->close();
    }
}