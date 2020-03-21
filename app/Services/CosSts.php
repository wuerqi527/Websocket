<?php

/**
 * 腾讯云 COS 对象存储临时密钥生成
 *
 * @see https://github.com/tencentyun/cos-js-sdk-v5/blob/master/server
 * @see https://cloud.tencent.com/document/product/436/14048
 *
 * @author JiangJian <silverd@sohu.com>
 */

namespace App\Services;

use GuzzleHttp\Client as HttpClient;

class CosSts
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function buildString(array $params)
    {
        // 键名升序
        ksort($params);

        $strs = [];

        foreach ($params as $key => $value) {
            $strs[] = $key . '=' . $value;
        }

        // 拼接待签名字符串
        $paramStr = implode('&', $strs);

        // 返回最终完整参数串
        return $paramStr;
    }

    // 计算临时密钥用的签名
    protected function buildSignature(array $params, string $method = 'GET')
    {
        $string = $method . $this->config['domain'] . '/v2/index.php?' . $this->buildString($params);

        $sign = hash_hmac('sha1', $string, $this->config['secret_key']);
        $sign = base64_encode(hex2bin($sign));

        return $sign;
    }

    // 生成临时凭证
    public function getFederationToken(array $policy)
    {
        $params = [
            'Action'          => 'GetFederationToken',
            'Nonce'           => uniqid(),
            'Region'          => '',
            'SecretId'        => $this->config['secret_id'],
            'Timestamp'       => time() - 1,
            'durationSeconds' => 7200,
            'name'            => '',
            'policy'          => json_encode($policy, JSON_UNESCAPED_SLASHES),
        ];

        $params['Signature'] = $this->buildSignature($params);

        $httpClient = new HttpClient(['verify' => false]);

        $result = $httpClient->request('GET', $this->config['url'], ['query' => $params]);
        $result = json_decode($result->getBody(), true);

        if (! $result) {
            throw new \Exception('请求腾讯云 STS 失败');
        }

        if (! isset($result['data'])) {
            throw new \Exception('请求腾讯云 STS 失败：' . $result['code'] . ' / ' . $result['message']);
        }

        return $result['data'];
    }

    // @see https://github.com/tencentyun/cos-wx-sdk-v5
    public function getCosUploadToken(string $allowPrefix = '*')
    {
        $policy = [
            'version'=> '2.0',
            'statement'=> [
                [
                    'action'=> [
                        // 简单文件操作
                        'name/cos:PutObject',
                        'name/cos:PostObject',
                        'name/cos:AppendObject',
                        'name/cos:GetObject',
                        'name/cos:HeadObject',
                        'name/cos:OptionsObject',
                        'name/cos:PutObjectCopy',
                        'name/cos:PostObjectRestore',
                    ],
                    'effect'=> 'allow',
                    'principal'=> [
                        'qcs'=> ['*'],
                    ],
                    'resource'=> "*",
                ],
            ],
        ];

        return $this->getFederationToken($policy);
    }
}
