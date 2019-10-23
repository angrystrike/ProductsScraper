<?php

namespace traits;


use core\ProxyPool;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;


trait Parsable
{
    function getHTML($link, ProxyPool $proxyPool, Client $client)
    {
        $triesBeforeRemoveProxy = 3;

        while ($proxyPool->getProxiesCount()) {

            for ($i = 0; $i < $triesBeforeRemoveProxy; $i++) {
                try {
                    $html = $client->get($link, ['proxy' => $proxyPool->getCurrent()])->getBody()->getContents();
                    return new Document($html);
                } catch (Exception $exception) {
                    echo "Proxy crashed on link: $link. Error: {$exception->getMessage()}";
                    sleep(5);
                }
            }
            error_log("Proxy totally crashed and was replaced: $link", 0);
            $proxyPool->getRandom();
        }

        error_log("No proxies left. Aborting parsing", 0);
        die;
    }

    private function getUrlFromStyle($styleString)
    {
        $uri = substr($styleString, strpos($styleString, '//') + strlen('//'));
        return 'https://' . trim($uri, ')');
    }
}