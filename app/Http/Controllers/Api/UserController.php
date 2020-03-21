<?php

namespace App\Http\Controllers\Api;

use Auth;
use SmsManager;
use Illuminate\Http\Request;
use App\Models\UserMinaFormId;
use App\Business\UserBusiness;

class UserController extends AbstractController
{
    // 当前用户信息
    public function user(Request $request)
    {
        $user = Auth::user();

        return ok(__FUNCTION__, compact('user'));
    }

    // 绑定用户微信绑定手机号
    public function bindWxMobile(Request $request)
    {
        $posts = $request->validate([
            'iv'             => 'bail|required|string',
            'encrypted_data' => 'bail|required|string',
        ]);

        $user = Auth::user();

        // 获取用户微信绑定手机号
        $mobile = UserBusiness::getWxMobile($user, $posts);

        // 保存用户手机号
        $user->update(['mobile' => $mobile]);

        return ok(__FUNCTION__, compact('mobile'));
    }

    // 保存用户提交表单的form_id
    public function saveFormId(Request $request)
    {
        $request->validate([
            'form_id' => 'required|string',
        ]);

        UserMinaFormId::setUsable(Auth::id(), $request->form_id, 'form_id');

        return ok(__FUNCTION__);
    }
}
