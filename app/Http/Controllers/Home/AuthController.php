<?php

namespace App\Http\Controllers\Home;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['register', 'login']]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|unique:users|numeric',
            'password' => 'required',
        ], [
            'name.required' => '名字不能为空',
            'phone.required' => '手机号不能为空',
            'phone.unique' => '该手机号已被注册',
            'phone.numeric' => '手机号必须为数字',
            'password.required' => '密码不能为空',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 8,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $user = User::add([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'code' => 0,
            'message' => '注册成功',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'password' => 'required',
        ], [
            'phone.required' => '手机号不能为空',
            'phone.numeric' => '手机号必须为数字',
            'password.required' => '密码不能为空',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 8,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $credentials = $request->only('phone', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response([
                'code' => 8,
                'message' => '手机号或密码错误'
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
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json([
            'code' => 0,
            'user' => $user
        ]);
    }
}
