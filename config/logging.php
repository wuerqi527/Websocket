<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        // 邮件错误报警
        'errormail' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\SwiftMailerHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::ERROR,
                'subject' => env('APP_NAME') . ' Error Logs',
                'to'      => explode(',', env('LOG_MAIL_TO')),
                'cd_secs' => 60,
            ],
        ],


        // 邮件日志记录
        'email' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\SwiftMailerHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::DEBUG,
                'subject' => env('APP_NAME') . ' Info Logs',
                'to'      => explode(',', env('LOG_MAIL_TO')),
            ],
        ],

        // 瀑布零信
        'lean_chat' => [
            'driver'  => 'monolog',
            'handler' => \App\Extensions\Logger\Handler\LeanChatHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::DEBUG,
                'channel' => env('LOG_PUBU_ROOM'),
            ],
        ],

        // Server 酱
        'server_chan' => [
            'driver'  => 'monolog',
            'handler' => \App\Extensions\Logger\Handler\ServerChanHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::DEBUG,
                'sendKey' => env('LOG_SERVER_CHAN_SKEY'),
            ],
        ],

        // PushBear
        'push_bear' => [
            'driver'  => 'monolog',
            'handler' => \App\Extensions\Logger\Handler\PushBearHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::DEBUG,
                'sendKey' => env('LOG_PUSH_BEAR_SKEY'),
            ],
        ],

        // 落地到数据库
        'database' => [
            'driver'  => 'monolog',
            'handler' => \App\Extensions\Logger\Handler\DatabaseHandler::class,
            'with'    => [
                'level' => \Monolog\Logger::DEBUG,
                'table' => 'log_default',
            ],
        ],

        // 落地到 Redis
        'redis' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\RedisHandler::class,
            'with'    => [
                'level'    => \Monolog\Logger::DEBUG,
                'key'      => 'log_default',
                'cap_size' => 1000,
            ],
        ],

        // 队列执行失败
        'queue_failed' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\SwiftMailerHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::ERROR,
                'subject' => env('APP_NAME') . ' 队列任务失败',
                'to'      => explode(',', env('LOG_MAIL_TO')),
                'cd_secs' => 60,
            ],
        ],

        // 队列执行成功
        'queue_succeed' => [
            'driver'  => 'monolog',
            'handler' => \App\Extensions\Logger\Handler\DatabaseHandler::class,
            'with'    => [
                'level' => \Monolog\Logger::DEBUG,
                'table' => 'log_queue_succeed',
            ],
        ],

        // 运维预警
        'alarm_hourly' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\SwiftMailerHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::ERROR,
                'subject' => env('APP_NAME') . ' 运维预警',
                'to'      => explode(',', env('LOG_MAIL_TO')),
                'cd_secs' => 3600,
            ],
        ],

        // 运维预警
        'alarm_daily' => [
            'driver'  => 'monolog',
            'handler' => \Monolog\Handler\SwiftMailerHandler::class,
            'with'    => [
                'level'   => \Monolog\Logger::ERROR,
                'subject' => env('APP_NAME') . ' 运维预警',
                'to'      => explode(',', env('LOG_MAIL_TO')),
                'cd_secs' => 43200,
            ],
        ],
    ],

];
