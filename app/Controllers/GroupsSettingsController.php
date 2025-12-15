<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\GroupsService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class GroupsSettingsController extends BaseController
{
    private GroupsService $groups_service;

    public function __construct()
    {
        $this->groups_service = Services::groups_service();
    }

    public function index()
    {
        $groups = $this->groups_service->getAllGroups();

        return view('pages/system/groups_settings');
    }
}
