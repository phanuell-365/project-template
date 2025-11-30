<?php

namespace App\Filters;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
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
     * @param array|null $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Here you can implement your authentication logic
        // For example, check if the user is logged in
        $isLoggedIn = session()->get('isLoggedIn');

        // Get the organization slug from request, it's usually the first segment
        $uri = $request->getUri();
        $segments = $uri->getSegments();
        $org_slug = $segments[0] ?? session()->get('org_slug') ?? null;

        if (!$isLoggedIn) {
            if ($org_slug) {
                $db = \Config\Database::connect();

                // check if organization exists
                $org = $db->table('organizations')
                    ->where('slug', $org_slug)
                    ->get()
                    ->getRowArray();

                log_message('debug', 'Organization lookup for slug ' . $org_slug . ': ' . json_encode($org, JSON_PRETTY_PRINT));

                if (!$org) {
                    // If organization does not exist, redirect to a generic login page
//                    return redirect()->to('/auth/login');
                    throw new PageNotFoundException('Organization Not Found');
                }

                // If not logged in, redirect to login page
                return redirect()->to("/{$org_slug}/auth/login");
            } else {
                // If org slug is not present, redirect to a generic login page
                return redirect()->to('/auth/login');
            }
        }

        // If logged in, allow the request to proceed
        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
