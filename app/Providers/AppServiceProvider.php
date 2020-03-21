<?php

namespace App\Providers;

use Schema;
use Request;
use Response;
use App\Services\BaiduLbs\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // 当前时间
        $GLOBALS['_TIME'] = $_SERVER['REQUEST_TIME'];
        $GLOBALS['_DATE'] = date('Y-m-d H:i:s');

        // 上传访问域名
        $GLOBALS['_FS_DN_HOST'] = config('filesystems.disks.public.url');

        // 响应宏
        Response::macro('output', function (string $message, int $code = 0, $data = null) {

            // 为保持输出的空 map/list 结构一致
            // 这里统一把所有空 array 转换为 null
            if (is_array($data)) {
                $data = setEmptyArrayToNull($data);
            }

            // 总耗时
            $elapsed = round(microtime(true) - LARAVEL_START, 3);

            return toJson([
                'code'     => $code,
                'message'  => $message,
                'data'     => $data,
                'elapsed'  => $elapsed,
            ]);
        });

        Request::macro('integer', function ($key, $default = null) {
            return (int) request()->input($key, $default);
        });

        Request::macro('boolean', function ($key, $default = null) {
            return (boolean) request()->input($key, $default);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 百度lbs服务
        $this->app->singleton('lbs', function ($app) {
            return new Factory(config('cloud.baidu_lbs'));
        });
    }
}
