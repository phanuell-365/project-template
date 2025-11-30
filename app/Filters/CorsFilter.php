<?php

//class CorsFilter implements FilterInterface
//{
//
//    /**
//     * @inheritDoc
//     */
//    public function before(RequestInterface $request, $arguments = null)
//    {
//        // TODO: Implement before() method.
//
//        // check if it is a prefight request
//        log_message('debug', 'request method: ' . $request->getMethod());
//
//        if ($request->getMethod() === 'options') {
//            return service('response')->setHeader('Access-Control-Allow-Origin', '*')
//                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
//                ->setHeader('Access-Control-Max-Age', '86400')
//                ->setStatusCode(200);
//        }
//
//        return $request;
//    }
//
//    /**
//     * @inheritDoc
//     */
//    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
//    {
//        // TODO: Implement after() method.
//
//        return $response;
//    }
//}


namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CorsFilter implements FilterInterface
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
     * @return RequestInterface|ResponseInterface
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight OPTIONS request
//        if ($request->getMethod() === 'OPTIONS') {
//            $response = Services::response();
//            $this->setCorsHeaders($response);
//            $response->setStatusCode(204);
//            return $response;
//        }
//
//        // For HEAD requests, we can also set CORS headers in the response
//        if ($request->getMethod() === 'HEAD') {
//            $response = Services::response();
//            $this->setCorsHeaders($response);
//
//            log_message('debug', 'CORS headers set for HEAD request');
//            return $request;
//        }

        // Get the response service
        $response = service('response');

        // Define allowed origins - adjust based on your needs
        $allowedOrigins = [
            'https://ptemp.kidakwa.com'
        ];

        $origin = $request->header('Origin');
        $originValue = $origin ? $origin->getValue() : '';

        // Check if the origin is allowed
        if (in_array($originValue, $allowedOrigins) || $originValue === '') {
            $response->setHeader('Access-Control-Allow-Origin', $originValue ?: '*');
        }

        // Set CORS headers
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE, PATCH');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-KEY');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Max-Age', '86400'); // Cache preflight for 24 hours

        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'options' || $request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(200);
            $response->send();
            exit;
        }


        return $request;
    }

    /**
     * Set CORS headers based on the Cors config
     */
    private function setCorsHeaders(ResponseInterface $response): void
    {
        // Get the CORS configuration
        $corsConfig = config('Cors');
        $config = $corsConfig->default ?? [];

        // Set default values if config is not available
        $allowedOrigins = $config['allowedOrigins'] ?? ['http://localhost:11005'];
        $allowedHeaders = $config['allowedHeaders'] ?? [
            'Content-Type',
            'Authorization',
            'X-Requested-With'
        ];
        $allowedMethods = $config['allowedMethods'] ?? [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS'
        ];

        $supportsCredentials = $config['supportsCredentials'] ?? false;

        $maxAge = $config['maxAge'] ?? 7200;

        // Set Access-Control-Allow-Origin
        if (in_array('*', $allowedOrigins, true)) {
            $response->setHeader('Access-Control-Allow-Origin', '*');
        } else {
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if (in_array($origin, $allowedOrigins, true)) {
                $response->setHeader('Access-Control-Allow-Origin', $origin);
            }
        }

        // Set other CORS headers
        $response->setHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $response->setHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        $response->setHeader('Access-Control-Max-Age', (string)$maxAge);

        // Set credentials if needed
        if ($supportsCredentials) {
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Expose headers if configured
        if (!empty($config['exposedHeaders'])) {
            $response->setHeader('Access-Control-Expose-Headers', implode(', ', $config['exposedHeaders']));
        }
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
     * @return ResponseInterface
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $this->setCorsHeaders($response);
        return $response;
    }
}