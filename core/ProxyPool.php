<?php

namespace core;


use Exception;
use GuzzleHttp\Client;


class ProxyPool
{
    private $proxies = [];
    private $current;

    public function __construct($proxiesLink)
    {
        $proxies = $this->checkProxiesLink($proxiesLink);

        foreach ($proxies as $item) {
            $proxy = $item['ip'] . ':' . $item['port'];
            if ($this->checkProxy($proxy)) {
                $this->proxies[] = $proxy;
            }
        }

        $this->setCurrent($proxies[0]);
    }

    public function getRandom()
    {
        $badProxyKey = array_search($this->getCurrent(), $this->proxies);
        unset($this->proxies[$badProxyKey]);
        $this->proxies = array_values($this->proxies);

        $randomIndex = rand(0, $this->getProxiesCount() - 1);
        $newProxy = $this->proxies[$randomIndex];
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

    private function checkProxiesLink($link)
    {
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            die("\nNot a valid url provided for proxies\n");
        }

        $client = new Client();

        $proxies = $client->get($link)->getBody()->getContents();
        $proxies = json_decode($proxies, true);

        if (empty($proxies)) {
            die("\nInvalid proxy link provided. Valid link must contain json with proxies\n");
        }

        foreach ($proxies as $key => $proxy) {
            if (!array_key_exists('ip', $proxy) || !array_key_exists('port', $proxy)) {
                echo "Proxy must contain ip and port keys. Invalid proxy will not be used\n";
                unset($proxies[$key]);
            }
        }

        return $proxies;
    }

    private function checkProxy($proxy)
    {
        try {
            $client = new Client();
            $client->get(ROOT,
                [
                    'proxy' => $proxy,
                    'timeout' => 5,
                    'connect_timeout' => 5,
                ])->getBody()->getContents();
        } catch (Exception $exception) {
            echo "Proxy fails\n";
            return false;
        }
        echo "Proxy works\n";
        return true;
    }
}