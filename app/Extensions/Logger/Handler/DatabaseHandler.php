<?php

namespace App\Extensions\Logger\Handler;

use DB;
use Schema;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Database\Schema\Blueprint;

/**
 * 日志记录到数据库（扩展 Monolog）
 *
 * @author JiangJian <silverd@sohu.com>
 *
 * @see https://github.com/Seldaek/monolog/blob/master/doc/04-extending.md
 */

class DatabaseHandler extends AbstractProcessingHandler
{
    protected $table;

    public function __construct(string $table, $level = Logger::INFO, $bubble = true)
    {
        $this->table = $table;

        // 创建日志表
        $this->checkCreateTable();

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        DB::table($this->table)->insert([
            'level'      => $record['level'],
            'level_name' => $record['level_name'],
            'channel'    => $record['channel'],
            'message'    => $record['message'],
            'context'    => toJson($record['context']),
            'extra'      => toJson($record['extra']),
            'created_at' => $record['datetime']->format('Y-m-d H:i:s'),
        ]);
    }

    protected function checkCreateTable()
    {
        // 创建日志表
        if (! Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->increments('id')->unsigned();
                $table->integer('level')->unsigned();
                $table->string('level_name');
                $table->string('channel');
                $table->mediumText('message');
                $table->mediumText('context');
                $table->mediumText('extra');
                $table->timestamp('created_at')->nullable();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }
}
