<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
helper('flash_message');
class PermissionsFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $permissionsService = Services::permissions_service();

        $route = service('router')->getMatchedRoute()[0];

        $pattern = '/\(\[\^\/]\+\)/';  // Matches '([^/]+)'

        // we need to remove the first segment from the route
        $normalizedRoute = preg_replace($pattern, ':var', $route);
        $routeSegments = explode('/', ltrim($normalizedRoute, '/'));
        array_shift($routeSegments);
        $normalizedRoute = '/' . implode('/', $routeSegments);

//        // log the normalized route
        log_message('debug', '[DEBUG] Normalized Route: {route}, {original_route}', [
            'original_route' => $route,
            'route' => $normalizedRoute,
        ]);

        $user_id = session()->get('user_id');
        $org_id = session()->get('org_id');

        if (!$permissionsService->canAccessRoute($user_id, $org_id, $normalizedRoute)) {
            // User does not have permission to access this route
//            session()->setFlashdata('error-alert', [
//                'title'   => 'Error',
//                'message' => 'Permission Denied. You do not have sufficient permissions to access this resource'
//            ]);

            flash_message('Permission Denied', 'You do not have sufficient permissions to access this resource.', 'error');

            return redirect()->to(previous_url() ?: '/unauthorized');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
