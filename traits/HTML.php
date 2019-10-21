<?php

namespace traits;


use core\ProxyPool;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;


trait HTML
{
    function getHTML($link, ProxyPool $proxyPool, Client $client)
    {
        try {
            $html = $client->get($link, ['proxy' => $proxyPool->getCurrent()])->getBody()->getContents();
            error_log("Current proxy: {$proxyPool->getCurrent()}", 0);

        } catch (Exception $exception) {
            error_log("Error: $link", 0);
            sleep(25);
            $html = $client->get($link, ['proxy' => $proxyPool->getRandom()])->getBody()->getContents();
        }

        return new Document($html);
    }
}