<?php

namespace traits;


use core\ProxyPool;
use DiDom\Document;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;


trait Parsable
{
    private function getHTML($link, ProxyPool $proxyPool, Client $client)
    {
        $triesBeforeRemoveProxy = 4;

        while ($proxyPool->getProxiesCount() > 1) {

            for ($i = 0; $i < $triesBeforeRemoveProxy; $i++) {
                try {
                    $html = $client->get($link, ['proxy' => $proxyPool->getCurrent()])->getBody()->getContents();
                    return new Document($html);
                } catch (ClientException $exception) {
                    return false;
                } catch (RequestException $exception) {
                    echo "Proxy crashed on link: $link \n";
                    echo "Error: {$exception->getMessage()} \n";
                    sleep(8);
                }
            }
            echo "Broken proxy was replaced \n";
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
        } catch(Exception $exception){
            $data['image'] = null;
            $data['img_origin_link'] = null;
        }

        restore_error_handler();
        return $data;
    }
}