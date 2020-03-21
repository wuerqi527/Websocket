<?php

namespace App\Providers;

use Log;
use Queue;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;
use Monolog\Handler\RedisHandler;
use Monolog\Handler\SwiftMailerHandler;
use App\Extensions\Logger\Handler\DeduplicationHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;

class LogServiceProvider extends ServiceProvider
{
    // 需要记录『执行成功日志』的队列
    protected $logSucceedQueues = [];

    public function boot()
    {
        // 邮件日志处理器
        $this->app->bind(SwiftMailerHandler::class, function ($app, array $with) {

            $config = $app['config']['mail'];

            $message = (new \Swift_Message($with['subject']))
                ->setFrom($config['from']['address'], $config['from']['name'])
                ->setTo($with['to']);

            $handler = new SwiftMailerHandler($app->make('swift.mailer'), $message, $with['level']);

            // 额外记录环境变量
            $handler->pushProcessor(new WebProcessor);

            // 额外记录请求数据
            $handler->pushProcessor(function (array $record) {
                $record['extra']['headers'] = \Request::header();
                $record['extra']['gets']    = \Request::all();
                $record['extra']['posts']   = \Request::post();
                $record['extra']['cookies'] = \Request::cookie();
                return $record;
            });

            // 重复消息冷却去重（装饰模式）
            if (isset($with['cd_secs']) && $with['cd_secs'] > 0) {
                return new DeduplicationHandler(
                    $handler,
                    $app['cache']->store('redis'),
                    $with['level'],
                    $with['cd_secs']
                );
            }

            return $handler;
        });

        // Redis 日志处理器
        $this->app->bind(RedisHandler::class, function ($app, array $with) {

            $redis = $app['redis']->connection('logging');

            return new RedisHandler(
                $redis->client(),
                $with['key'],
                $with['level'],
                true,
                $with['cap_size']
            );
        });

        // 队列执行失败
        Queue::failing(function (JobFailed $event) {
            $payload = $event->job->payload();
            Log::channel('queue_failed')->error($payload['displayName'], [
                'connection'  => $event->connectionName,
                'job_payload' => $payload,
                'exception'   => [
                    $event->exception->getCode(),
                    $event->exception->getMessage(),
                    $event->exception->getFile(),
                    $event->exception->getLine(),
                ],
            ]);
        });

        // 队列执行成功
        Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            $jobClass = $payload['data']['commandName'];
            if (in_array($jobClass, $this->logSucceedQueues)) {
                Log::channel('queue_succeed')->info($payload['displayName'], [
                    'connection'  => $event->connectionName,
                    'job_payload' => $payload,
                ]);
            }
        });
    }
}
