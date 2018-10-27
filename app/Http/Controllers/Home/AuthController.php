<?php

namespace App\Http\Controllers\Home;

use App\Models\User;
use App\Utils\Functions;
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
                'code' => 1,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $user = User::add([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
        ]);
        $token = auth()->login($user);
        return response()->json([
            'code' => 0,
            'message' => '注册成功',
            'token' => $token
        ]);
    }

    public function login(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'phone' => 'required|numeric',
//            'password' => 'required',
//        ], [
//            'phone.required' => '手机号不能为空',
//            'phone.numeric' => '手机号必须为数字',
//            'password.required' => '密码不能为空',
//        ]);
//        if ($validator->fails()) {
//            return response()->json([
//                'code' => 1,
//                'message' => $validator->errors()->first()
//            ], 400);
//        }
//        $credentials = $request->only('phone', 'password');
//        if (!$token = auth()->attempt($credentials)) {
//            return response([
//                'code' => 1,
//                'message' => '手机号或密码错误'
//            ], 400);
//        }
//        return response()->json([
//            'code' => 0,
//            'message' => '登录成功',
//            'token' => $token
//        ]);

        $appId = config('wx.appId');
        $appSecret = config('wx.appSecret');
        $js_code = $request->input('js_code');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appId}&secret={$appSecret}&js_code={$js_code}&grant_type=authorization_code";
        $res = Functions::httpGet($url);
        if (isset($res->errcode)) {
            switch ($res->errcode) {
                case -1:
                    $message = '系统繁忙，请稍候再试';
                    break;
                case 40029:
                case 40163:
                    $message = '登录失败，请重试';
                    break;
                case 45011:
                    $message = '登录操作过于频繁，请稍后再试';
                    break;
                default:
            }
            return response()->json([
                'code' => 1,
                'message' => $message ?? ''
            ], 400);
        }
        $openid = $res->openid;
        $session_key = $res->session_key;
        $user = User::add([
            'openid' => $openid,
            'session_key' => $session_key
        ]);
        $token = auth()->login($user);
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
        $user = auth()->user();
        return response()->json([
            'code' => 0,
            'user' => $user
        ]);
    }
}
