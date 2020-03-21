<?php

namespace App\Services\BaiduLbs;

use GuzzleHttp\Client as HttpClient;
use App\Services\BaiduLbs\Exceptions\BaiduLbsException;

abstract class AbstractService
{
    protected $config;

    protected $httpClient;

    const REQUEST_HOST = 'http://api.map.baidu.com';

    public function __construct(array $config)
    {
        $this->config     = $config;
        $this->httpClient = new HttpClient(['verify' => false]);
    }

    protected function call(string $method, string $url, array $params)
    {
        $params += [
            'ak' => $this->config['app_key'],
        ];

        // 构造签名
        $params['sn'] = static::buildSign($url, $params, $this->config['secret_key'], $method);

        $url = self::REQUEST_HOST . $url;

        // httpClient请求组织参数get请求：query  post请求form_params
        if ($method == 'GET') {
            $requestBody = ['query' => $params];
        } else {
            $requestBody = ['form_params' => $params];
        }

        $result = $this->httpClient->request($method, $url, $requestBody);

        $result = json_decode($result->getBody(), true);

        if (! $result || ! isset($result['status'])) {
            throw new BaiduLbsException('接口请求失败');
        }

        if ($result['status'] != 0 && $result['status'] != 21) {
            throw new BaiduLbsException('接口请求异常：' . ($result['msg'] ?? $result['message']) . ' (' . $result['status'] . ')');
        }

        return $result;
    }

    // 构造签名
    public static function buildSign(string $url, array $params, string $secretKey, string $method = 'GET')
    {
        if ($method == 'POST') {
            ksort($params);
        }

        return md5(urlencode($url . '?' . http_build_query($params) . $secretKey));
    }
}

