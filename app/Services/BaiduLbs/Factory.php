<?php

namespace App\Services\BaiduLbs;

use App\Services\BaiduLbs\Exceptions\BaiduLbsException;

class Factory
{
    protected $config;
    protected $container = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __get(string $name)
    {
        $className = __NAMESPACE__ . '\\' . ucfirst($name) . 'Service';

        if (! class_exists($className)) {
            throw new BaiduLbsException($className . ' Not Found');
        }

        if (isset($this->container[$className])) {
            return $this->container[$className];
        }

        return $this->container[$className] = new $className($this->config);
    }
}