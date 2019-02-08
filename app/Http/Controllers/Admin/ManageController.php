<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Utils\Functions;

class ManageController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkAuthority');
    }

    public function getAdminList() {
        $admins = Admin::getAllAdmins();
        return response()->json([
            'code' => 0,
            'admins' => $admins
        ]);
    }

    public function addAdmin() {
        $admin = Admin::add(Functions::generateAdmin());
        return response()->json([
            'code' => 0,
            'message' => '添加成功',
            'admin' => $admin
        ]);
    }
}
