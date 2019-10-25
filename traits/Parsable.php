<?php

namespace traits;


use core\ProxyPool;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;


trait Parsable
{
    private function getHTML($link, ProxyPool $proxyPool, Client $client)
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

    private function getImage($path, $data)
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        try {
            file_put_contents($path . $data['image'] . '.jpg', file_get_contents($data['img_origin_link']));
        } catch(Exception $e){
            $data['image'] = null;
            $data['img_origin_link'] = null;
        }

        restore_error_handler();
        return $data;
    }
}