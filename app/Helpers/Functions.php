<?php

function vd($s, $exit = true)
{
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $exit && exit();
}

function pr($s, $exit = true)
{
    echo '<pre>';
    print_r($s);
    echo '</pre>';
    $exit && exit();
}

function throws($message, $code = -1)
{
    throw new \App\Exceptions\UserException($message, $code);
}

function ok($message = 'OK', $data = [])
{
    return response()->output($message, 0, $data);
}

function imgUrl($imgUrl)
{
    if (! $imgUrl) {
        return '';
    }

    if (strpos($imgUrl, 'http') === 0) {
        return $imgUrl;
    }

    return rtrim($GLOBALS['_FS_DN_HOST'], '/') . '/' . ltrim($imgUrl, '/');
}

function imgPath($path)
{
    $host = config('filesystems.disks.public.root');

    return rtrim($host, '/') . '/' . ltrim($path, '/');
}

function buildToken($uniqueId = null)
{
    return sha1(uniqid($uniqueId) . mt_rand(1, 10000));
}

function setEmptyArrayToNull(array $array)
{
    if (! $array) {
        return null;
    }

    foreach ($array as $key => &$value) {
        if ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
            $value = $value->toArray();
        }
        if (is_array($value)) {
            if (! $value) {
                $value = null;
            }
            else {
                $func = __FUNCTION__;
                $value = $func($value);
            }
        }
    }

    return $array;
}

function toJson($source)
{
    return response()->json($source, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// 获取 URL 资源的二进制流
function fetchImg(string $imgUrl, int $timeout = 30)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $imgUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $contents = curl_exec($ch);
    curl_close($ch);

    return $contents;
}

// 当前毫秒时间
function nowTimeMs()
{
    return now()->format('Y-m-d H:i:s.u');
}

function nowMsTimestamp()
{
    return str_replace('.', '', number_format(microtime(true), 3, '.', ''));
}

/**
 * 图片缩略图
 *
 * @see https://cloud.tencent.com/document/product/460/6929
 */
function getImgThumbUrl(string $imgUrl, int $w = 750, int $h = 750, int $quality = 100, int $mode = 1)
{
    $imgUrl = str_replace('cos.ap-shanghai.myqcloud.com', 'picsh.myqcloud.com', $imgUrl);

    $con = strpos($imgUrl, '?') === false ? '?' : rawurlencode('/');

    $imgUrl .= $con . 'imageView2/' . $mode . '/interlace/1';

    if ($w !== null) {
        $imgUrl .= '/w/' . $w;
    }

    if ($h !== null) {
        $imgUrl .= '/h/' . $h;
    }

    if ($quality !== null) {
        $imgUrl .= '/q/' . $quality;
    }

    return $imgUrl;
}

// 将远程图片转存到COS并返回URL
function storageByUrl(string $dirName, string $imgUrl)
{
    $imgPath = $dirName . '/' . date('YmdH') . '/'. str_random(40);

    // 将图片存入腾讯云COS
    \Storage::put($imgPath, fetchImg($imgUrl));
    $ourImgUrl = \Storage::url($imgPath);

    return $ourImgUrl;
}

/**
 * Get the token for the current request.
 *
 * @return string
 */
function getTokenForRequest($inputKey)
{
    $request = request();

    $token = $request->query($inputKey);

    if (empty($token)) {
        $token = $request->input($inputKey);
    }

    if (empty($token)) {
        $token = $request->bearerToken();
    }

    if (empty($token)) {
        $token = $request->getPassword();
    }

    return $token;
}

function getStoragePath(string $dirName)
{
    return $dirName . '/' . date('YmdH') . '/' . str_random(32) . '.jpg';
}
