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
        $this->middleware('verifyToken', ['except' => ['login']]);
    }

    public function login(Request $request) {
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
                case 45011:
                    $message = '登录操作过于频繁，请稍后再试';
                    break;
                default:
                    $message = '登录失败，请重试';
            }
            return response()->json([
                'code' => 1,
                'message' => $message
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

    public function setUser(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'phone' => 'unique:users',
        ], [
            'phone.unique' => '手机号已被注册'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 1,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $uid = auth()->id();
        $user = User::setInfo($uid, $data);
        return response()->json([
            'code' => 0,
            'message' => '设置成功',
            'user' => $user
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
