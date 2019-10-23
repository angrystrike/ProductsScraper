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
        $triesBeforeRemoveProxy = 4;

        while ($proxyPool->getProxiesCount()) {

            for ($i = 0; $i < $triesBeforeRemoveProxy; $i++) {
                try {
                    $html = $client->get($link, ['proxy' => $proxyPool->getCurrent()])->getBody()->getContents();
                    return new Document($html);
                } catch (Exception $exception) {
                    echo "\nProxy crashed on link: $link";
                    echo "\nError: {$exception->getMessage()}";
                    sleep(8);
                }
            }
            echo "\nProxy totally crashed and was replaced";
            $proxyPool->getRandom();
        }

        exit("\nNo proxies left. Aborting parsing\n");
    }

    private function getUrlFromStyle($styleString)
    {
        $uri = substr($styleString, strpos($styleString, '//') + strlen('//'));
        return 'https://' . trim($uri, ')');
    }
}