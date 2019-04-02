<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('verifyToken', ['except' => ['init', 'login']]);
    }

    public function init() {
        if (count(Admin::getAllAdmins()) != 0) {
            return response()->json([
                'code' => 3,
                'message' => '权限不足'
            ], 403);
        }
        $admin = Admin::add([
            'name' => 'sup0',
            'password' => bcrypt('sup0'),
            'authority' => 1
        ]);
        $token = auth()->login($admin);
        return response()->json([
            'code' => 0,
            'message' => '超级管理员初始化成功',
            'token' => $token
        ]);
    }

    public function login(Request $request) {
        $credentials = $request->only('name', 'password');
        if (!$token = auth()->attempt($credentials)) {
            return response([
                'code' => 1,
                'message' => '名字或密码错误'
            ], 400);
        }
        return response()->json([
            'code' => 0,
            'message' => '登录成功',
            'token' => $token
        ]);
    }

    public function logout() {
        auth()->logout();
        return response()->json([
            'code' => 0,
            'message' => '登出成功'
        ]);
    }

    public function me() {
        $admin = auth()->user();
        return response()->json([
            'code' => 0,
            'admin' => $admin
        ]);
    }

    public function resetPassword(Request $request) {
        $data = $request->all();
        $admin = auth()->user();
        $credentials = [
            'name' => $admin->name,
            'password' => $data['old']
        ];
        if (!auth()->validate($credentials)) {
            return response([
                'code' => 1,
                'message' => '原密码错误'
            ], 400);
        }
        $data['password'] = bcrypt($data['new1']);
        unset($data['old']);
        unset($data['new1']);
        unset($data['new2']);
        Admin::reset($admin->id, $data);
        return response()->json([
            'code' => 0,
            'message' => '重置密码成功'
        ]);
    }
}
