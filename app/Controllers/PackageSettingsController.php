<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PackageSettingsController extends BaseController
{
    public function index($org_slug): string
    {
        return view('pages/system/package_settings', ['org_slug' => $org_slug]);
    }
}
