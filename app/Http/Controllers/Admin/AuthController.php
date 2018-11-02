<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('verifyToken', ['except' => ['register', 'login']]);
    }

    public function register(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'unique:admins',
        ], [
            'name.unique' => '用户名已被注册',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 1,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $admin = Admin::add([
            'name' => $data['name'],
            'password' => bcrypt($data['password']),
        ]);
        $token = auth()->login($admin);
        return response()->json([
            'code' => 0,
            'message' => '注册成功',
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
}
