<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RedirectResponse;
use http\Message;
use Modules\Auth\Config\Services;
use Modules\Auth\Models\UsersModel;
use function App\Controllers\Errors\authenticate_user;

class Errors extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->helpers = [
            'verify_jwt',
            'cookie',
            'authenticate_user'
        ];
    }

    public function index()
    {

        // check if the request is application/json

        $headers = $this->request->headers();

//        log_message('debug', 'request headers: ' . json_encode($headers, JSON_PRETTY_PRINT));

        log_message('debug', 'request method is: ' . $this->request->getMethod());

        log_message('debug', 'request uri: ' . $this->request->getUri()
                ->getPath());

        $method = $this->request->getMethod();

        // if the request is OPTIONS, handle CORS

        if ($method === 'OPTIONS') {
            return $this->response->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setHeader('Access-Control-Max-Age', '86400')
                ->setStatusCode(200);
        }

        // if the request is HEAD, return 200 OK
        if ($method === 'HEAD') {
            return $this->response->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setHeader('Access-Control-Max-Age', '86400')
                ->setStatusCode(200);
//            return $this->response->setStatusCode(200);
        }

        if ($this->request->isAJAX()) {
            $this->request->setHeader('Content-Type', 'application/json');
            $this->request->setHeader('Accept', 'application/json');
            return $this->respond([
                'status' => 404,
                'error'  => 'Page not found'
            ]);
        }

        if (!$this->authenticateUser()) {

            $router_service = service('router');

            $matched_route = $router_service->getMatchedRoute();

            $url_info = current_url(true);

            $current_url = current_url();

            log_message('debug', 'route data: ' . json_encode([
                    'matched_route' => $matched_route,
                    'url_info'      => $url_info,
                    'current_url'   => $current_url
                    ,
                    'request'       => $this->request->getUri()
                        ->getPath()
                ], JSON_PRETTY_PRINT));

            $slug = session()->get('supplier_slug');

            return redirect()
                ->to('auth/login?cs=' . $slug)
                ->with('errors', [
                    [
                        'title'   => 'Permission Denied',
                        'message' => 'You do not have permission to access this page. Please login to continue'
                    ]
                ]);
        }

        $router_service = service('router');

        $matched_route = $router_service->getMatchedRoute();

        $url_info = current_url(true);

        $current_url = current_url();

        if ($matched_route) {
            $route = $this->routeExists('/' . $matched_route[0]);

            if ($route) {
                $presentInDb = $route['name'];
                $current_url = $route['uri'];
            } else {
                $presentInDb = false;
            }
            $routeExists = true;
        } else {
            $routeExists = false;
            $route = $this->routeExists($url_info->getPath());

            if ($route) {
                $presentInDb = $route['name'];
                $current_url = $route['uri'];
            } else {
                $presentInDb = false;
            }
        }


//        dd($current_url, $routeExists, $presentInDb, $route);

        $data = [
            'props' => [
                'exists'      => $routeExists,
                'presentInDb' => (bool)$presentInDb,
                'route'       => $current_url,
                'redirect'    => $this->homeRedirect(),
                'routeName'   => $presentInDb ? $presentInDb : '404'
            ]
        ];

//        return view('Errors/404', $data);
        return view('/errors/404', $data);
    }

    public function authenticateUser(): bool
    {
        // if what is returned is a redirect response
        // return false
        return !authenticate_user() instanceof RedirectResponse;
    }

    public function routeExists($route_uri)
    {
        $data = $this->db->query("SELECT name, uri FROM routes WHERE uri = '$route_uri'")
            ->getRowArray();

//        dd($data, $this->db->getLastQuery());

        return $data;
    }

    public function homeRedirect()
    {
        $authentication = Services::authentication();

        $groupName = $authentication->getGroupName();

        // redirect the user respectively
        if ($groupName === 'user.agent') {
            return route_to('agents-marketplace');
        }

        if ($groupName === 'user.sales-rep') {
            return route_to('sales-reps-marketplace');
        }

        if ($groupName === 'user.retailer') {
            return route_to('retailers-marketplace');
        }
        return route_to('dashboard');
    }

    public function show404()
    {
        log_message('debug', 'request method is: ' . $this->request->getMethod());

        log_message('debug', 'request uri: ' . $this->request->getUri()
                ->getPath());

        $method = $this->request->getMethod();

        $uri = $this->request->getUri();
        $segments = $uri->getSegments();
        $org_slug = $segments[0] ?? session()->get('org_slug') ?? null;

        // if the request is OPTIONS, handle CORS

        if ($method === 'OPTIONS') {
            return $this->response->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setHeader('Access-Control-Max-Age', '86400')
                ->setStatusCode(200);
        }

        // if the request is HEAD, return 200 OK
        if ($method === 'HEAD') {
            return $this->response->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setHeader('Access-Control-Max-Age', '86400')
                ->setStatusCode(200);
//            return $this->response->setStatusCode(200);
        }

        if ($this->request->isAJAX()) {
            $this->request->setHeader('Content-Type', 'application/json');
            $this->request->setHeader('Accept', 'application/json');
            return $this->respond([
                'status' => 404,
                'error'  => 'Page not found'
            ]);
        }

        // Set the response status code to 404
        $this->response->setStatusCode(404);

        // Set the headers for CORS
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setHeader('Access-Control-Max-Age', '86400');

        if (!$this->orgExists($org_slug)) {
//                    [
//                        'title'   => 'Organization Not Found',
//                        'message' => 'The organization you are trying to access does not exist. Please check the URL or contact support.'
//                    ]

            // since no organization was found, we'll use the default organization
            return view('errors/main/404', [
                'org_slug' => 'admin'
            ]);
        }

        return view('errors/main/404', [
            'org_slug' => $org_slug
        ]);
    }
}
