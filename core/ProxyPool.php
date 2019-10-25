<?php

namespace core;


use GuzzleHttp\Client;


class ProxyPool
{
    private $proxies = [];
    private $current;

    public function __construct($proxiesLink)
    {
        $client = new Client([
            'timeout' => 300,
            'curl' => [ CURLOPT_SSLVERSION => 1 ],
        ]);

        $proxies = $client->get($proxiesLink)->getBody()->getContents();
        $proxies = json_decode($proxies, true);
        foreach ($proxies as $proxy) {
            $this->proxies[] = $proxy['ip'] . ':' . $proxy['port'];
        }
        $this->setCurrent($proxies[0]);
    }

    public function getRandom()
    {
        $badProxyKey = array_search($this->getCurrent(), $this->proxies);
        unset($this->proxies[$badProxyKey]);
        $this->proxies = array_values($this->proxies);

        $lastIndex = rand(0, count($this->proxies) - 1);
        $newProxy = $this->proxies[$lastIndex];
        $this->setCurrent($newProxy);

        return $newProxy;
    }

    public function setCurrent($proxy)
    {
        $this->current = $proxy;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getProxiesCount()
    {
        return count($this->proxies);
    }
}