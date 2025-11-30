<?php

namespace App\Controllers;

use App\Services\PermissionsService;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [
        'session',
        'url',
        'form',
        'text',
        'date',
        'filesystem',
        'html',
        'flash_message'
    ];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    protected array $user = [];

    protected BaseConnection | null $db = null;

    protected CacheInterface $cache;

    protected PermissionsService | null $permissionsService;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        $this->db = Database::connect();

        $this->user = [
            'id'              => session()->get('user_id'),
            'full_name'       => session()->get('full_name'),
            'email'           => session()->get('email'),
            'phone'           => session()->get('phone'),
            //            'group_id'        => session()->get('group_id'),
            'group_name'      => session()->get('group_name'),
            'organization_id' => session()->get('org_id'),
        ];

        $this->cache = Services::cache();

        $this->permissionsService = Services::permissions_service();
    }

    /**
     * @param string $action Represents the action performed (e.g., 'login', 'update_profile')
     * @param bool $success Indicates whether the action was successful
     * @param array|object|null $data Additional data related to the action (optional)
     * @return void
     */
    protected function log_audit(string $action, bool $success, array | object | null $data = null): void
    {
        // Prepare the path, ip address, user agent, and method
        $matched_route = service('router')->getMatchedRoute()[0];
        $path = '/' . $matched_route;

        $audit_data = [
            'user_id'    =>  session()->get('user_id'),
//            'user_id'    =>  null,
            'action'     => $action,
            'path'       => $path,
            'success'    => $success,
            'details'    => $data ? json_encode($data) : null,
            'ip_address' => $this->request->getIPAddress() ?? null,
            'user_agent' => $this->request->getUserAgent()
                    ->getAgentString() ?? null,
            'method'     => $this->request->getMethod(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        log_message('info', '[AUDIT] {action} | Success: {success} | Path: {path} | User ID: {user_id} | IP: {ip_address} | Data: {data}', [
            'action'     => $action,
            'success'    => $success ? 'true' : 'false',
            'path'       => $path,
            'user_id'    => session()->get('user_id'),
            'ip_address' => $audit_data['ip_address'],
            'data'       => $data ? json_encode($data) : 'N/A',
        ]);

        // Insert audit log into the database
        $this->db->table('audit_logs')
            ->insert($audit_data);
    }

    protected function orgExists(string | null $slug): bool
    {
        if (!$slug) {
            throw new PageNotFoundException('Organization Not Found');
        }

        $org = $this->db->table('organizations')
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

//        return (bool)$org;

        // if it does not exist, throw a 404 error
        if (!$org) {
            throw new PageNotFoundException('Organization Not Found');
        }

        return (bool)$org;
    }

    protected function orgData(string | null $slug): array
    {
        if (!$slug) {
            throw new PageNotFoundException('Organization Not Found');
        }

        $org = $this->db->table('organizations')
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

        // if it does not exist, throw a 404 error
        if (!$org) {
            throw new PageNotFoundException('Organization Not Found');
        }

        return $org;
    }
}
