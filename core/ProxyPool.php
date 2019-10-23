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

        $allProxies = $client->get($proxiesLink)->getBody()->getContents();
        $allProxies = json_decode($allProxies, true);

        foreach ($allProxies as $proxy) {
            if ($this->checkProxy($proxy['ip'], $proxy['port'])) {
                $this->proxies[] = $proxy['ip'] . ':' . $proxy['port'];
            }
        }
        $this->getRandom();
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

    private function checkProxy($ip, $port)
    {
        $timeout = 3;
        if ($con = @fsockopen($ip, $port, $errorNumber, $errorMessage, $timeout)) {
            return true;
        }

        return false;
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