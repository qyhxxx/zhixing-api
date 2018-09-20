<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['register', 'login']]);
        $this->changeModel();
    }

    public function changeModel() {
        config(['jwt.user' => 'App\Models\Admin']);
        config(['auth.providers.users.model' => Admin::class]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ], [
            'name.required' => '名字不能为空',
            'password.required' => '密码不能为空',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 8,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $admin = Admin::add([
            'name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
        ]);
        $token = JWTAuth::fromUser($admin);
        return response()->json([
            'code' => 0,
            'message' => '注册成功',
            'admin' => $admin,
            'token' => $token
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ], [
            'name.required' => '名字不能为空',
            'password.required' => '密码不能为空',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 8,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $credentials = $request->only('name', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response([
                'code' => 8,
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
        JWTAuth::invalidate();
        return response()->json([
            'code' => 0,
            'message' => '登出成功'
        ]);
    }

    public function user() {
        $admin = JWTAuth::parseToken()->authenticate();
        return response()->json([
            'code' => 0,
            'admin' => $admin
        ]);
    }
}
