<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use App\Business\UserBusiness;
use Illuminate\Http\Request;

class AuthController extends AbstractController
{
    public function login(Request $request)
    {
        // 非生产环境万能登录code
        if (! \App::environment('production') && $request->code == 1322) {
            $user = User::first();
        }

        else {
            $posts = $request->validate([
                'code'           => 'bail|required|string',
                'raw_data'       => 'bail|required|string',
                'signature'      => 'bail|required|string',
                'encrypted_data' => 'bail|required|string',
                'iv'             => 'bail|required|string',
            ]);

            // 小程序登录
            $user = UserBusiness::loginByMina($posts);
        }

        // 授权登录
        $token = Auth::login($user);

        return ok('登陆成功', compact('token'));
    }

    public function logout()
    {
        Auth::logout();

        return ok('登出成功');
    }
}
