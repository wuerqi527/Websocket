<?php

namespace App\Extensions\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\Curl\Util;

/**
 * 零信 Incoming 通知（扩展 Monolog）
 *
 * @author JiangJian <silverd@sohu.com>
 *
 * @see https://pubu.im/integrations
 * @see https://github.com/Seldaek/monolog/blob/master/doc/04-extending.md
 */

class LeanChatHandler extends AbstractProcessingHandler
{
    protected $channel;

    public function __construct(string $channel, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->channel = $channel;
    }

    protected function write(array $record)
    {
        $url = 'https://hooks.pubu.im/services/' . $this->channel;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'text' => $record['message'],
        ]);

        Util::execute($ch);
    }
}
