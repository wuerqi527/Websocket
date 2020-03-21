<?php

namespace App\Extensions\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Curl\Util;

/**
 * Server 酱微信通知（扩展 Monolog）
 *
 * @author JiangJian <silverd@sohu.com>
 *
 * @see http://sc.ftqq.com/3.version
 * @see https://github.com/Seldaek/monolog/blob/master/doc/04-extending.md
 */

class ServerChanHandler extends AbstractProcessingHandler
{
    protected $sendKey;

    public function __construct(string $sendKey, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->sendKey = $sendKey;
    }

    protected function write(array $record)
    {
        $url = 'https://sc.ftqq.com/' . $this->sendKey . '.send';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'text' => $record['message'],
            'desp' => toJson($record['context']),
        ]);

        Util::execute($ch);
    }
}
