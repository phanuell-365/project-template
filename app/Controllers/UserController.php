<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function index()
    {
        //
    }

    public function createUserView($org_slug): string
    {
        return view('pages/users/create_user', ['org_slug' => $org_slug]);
    }
}
