<?php

/**
 * 通知类
 */

namespace App\Business;

use Log;
use EasyWeChat;
use App\Models\User;
use App\Models\UserMinaFormId;

class NoticeBusiness
{
    /**
     * 发送小程序模板消息
     *
     * @param int    $user       用户信息
     * @param string $tplId 模板id
     * @param array  $content    内容
     * @param null   $targetUrl  跳转到小程序页面
     *
     * @see  https://www.easywechat.com/docs/master/zh-CN/mini-program/template_message
     */
    public static function send(User $user, string $tplId, array $content, $targetUrl = '')
    {
        if (! $tplId = config('wechat.mina_tpl_message_ids.' . $tplId)) {
            throws('Invalid TemplateId');
        }

        if (! $content) {
            throws('消息内容不能为空');
        }

        $content = self::buildMsgData($content);

        // 获取可用的formId
        $formId = UserMinaFormId::getUsable($user->id);

        if (! $formId) {
            return false;
        }

        $result = EasyWeChat::miniProgram()->template_message->send(array_filter([
            'touser'      => $user->mina_openid,
            'template_id' => $tplId,
            'page'        => $targetUrl,
            'form_id'     => $formId,
            'data'        => $content,
        ]));

        UserMinaFormId::setUsed($formId);

        if ($result['errcode'] == 0) {
            return true;
        }

        // 错误类型:Invalid Form Id （小概率报该错误，未发现原因，暂时忽略该错误）
        if ($result['errcode'] == 41028) {
            // 若formId报该错误，将其置为不可用，避免下次继续读取到该formId
            UserMinaFormId::setDisabled($formId);
        }

        return true;
    }

    private static function buildMsgData(array $keywords = [])
    {
        $data = [];
        foreach ($keywords as $key => $keyword) {
            $data['keyword' . ($key + 1)] = [
                'value' => $keyword,
                'color' => '#000000',
            ];
        }

        return $data;
    }
}
