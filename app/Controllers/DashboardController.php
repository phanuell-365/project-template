<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index(string $org_slug): string
    {
        // Render the dashboard view
        return view('pages/dashboard');
    }
}
