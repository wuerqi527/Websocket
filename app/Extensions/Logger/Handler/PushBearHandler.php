<?php

namespace App\Extensions\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Curl\Util;

/**
 * PushBear（扩展 Monolog）
 * 基于微信模板的一对多消息送达服务
 *
 * @author JiangJian <silverd@sohu.com>
 *
 * @see https://pushbear.ftqq.com/admin/#/
 * @see https://github.com/Seldaek/monolog/blob/master/doc/04-extending.md
 */

class PushBearHandler extends ServerChanHandler
{
    protected $sendKey;

    public function __construct(string $sendKey, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->sendKey = $sendKey;
    }

    protected function write(array $record)
    {
        $url = 'https://pushbear.ftqq.com/sub';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'sendkey' => $this->sendKey,
            'text'    => $record['message'],
            'desp'    => toJson($record['context']),
        ]);

        Util::execute($ch);
    }
}
