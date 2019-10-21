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
            if (self::checkProxy($proxy['ip'], $proxy['port'])) {
                $this->proxies[] = $proxy['ip'] . ':' . $proxy['port'];
            }
        }

        self::getRandom();
    }

    public function setCurrent($proxy)
    {
        $this->current = $proxy;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getRandom()
    {
        $lastIndex = rand(0, count($this->proxies) - 1);
        $newProxy = $this->proxies[$lastIndex];
        self::setCurrent($newProxy);

        return $newProxy;
    }

    private function checkProxy($ip, $port)
    {
        $timeout = 10;
        if ($con = @fsockopen($ip, $port, $errorNumber, $errorMessage, $timeout)) {
            return true;
        } else {
            return false;
        }
    }
}