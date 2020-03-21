<?php

namespace App\Business;

use App\Models\User;
use App\Models\UsersStats;

class UserBusiness
{
    // 微信小程序登录
    public static function loginByMina(array $params)
    {
        $wechat = app('wechat.mini_program');

        // 去微信服务器获取 openId / unionId 和 sessionKey
        $session = $wechat->auth->session($params['code']);

        if ($params['signature'] != sha1($params['raw_data'] . $session['session_key'])) {
            throws('小程序获取用户信息-验签失败');
        }

        $fullData = $wechat->encryptor->decryptData(
            $session['session_key'],
            $params['iv'],
            $params['encrypted_data']
        );

        $pk = [
            'mina_openid' => $session['openid'],
        ];

        $user = User::firstOrNew($pk);

        $params = [
            'wx_unionid'  => $session['unionid'] ?? $session['openid'],
            'session_key' => $session['session_key'],
            'nickname'    => $fullData['nickName'],
            'gender'      => $fullData['gender'],
            'city'        => $fullData['city'],
            'province'    => $fullData['province'],
            'country'     => $fullData['country'],
        ];

        if (! $user->avatar_url) {
            try {
                $params['avatar_url'] = storageByUrl('wechat', $fullData['avatarUrl']);
            } catch (\Throwable $e) {
                // do nothing
            }
        }

        $user->fill($params)->save();

        // 生成一条统计数据
        UsersStats::firstOrCreate(['uid' => $user->id]);

        return $user;
    }

    // 获取用户微信手机号
    public static function getWxMobile(User $user, $posts)
    {
        try {
            $decrypted = app('wechat.mini_program')->encryptor->decryptData(
                $user->session_key,
                $posts['iv'],
                $posts['encrypted_data']
            );
        } catch (\Throwable $e) {
            throws('获取手机号码失败');
        }

        return $decrypted['phoneNumber'];
    }
}
