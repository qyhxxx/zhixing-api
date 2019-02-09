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

    public function getList() {
        $admins = Admin::getAllAdmins();
        return response()->json([
            'code' => 0,
            'admins' => $admins
        ]);
    }

    public function add() {
        $admin = Admin::add(Functions::generateAdmin());
        return response()->json([
            'code' => 0,
            'message' => '添加成功',
            'admin' => $admin
        ]);
    }

    public function delete($id) {
        Admin::deleteByAid($id);
        return response()->json([
            'code' => 0,
            'message' => '删除成功'
        ]);
    }
}
