<?php


namespace core;


use DiDom\Document;
use GuzzleHttp\Client;
use models\Category;
use traits\HTML;


class General
{
    use HTML;

    private $proxyPool;
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 300,
            'curl' => [ CURLOPT_SSLVERSION => 1 ],
        ]);

        //$params = require 'config/params.php';
       // $this->proxyPool = new ProxyPool($params['proxiesLink']);
    }

    public function parseWholeSite()
    {
        $categoriesPage = new Document($this->client->get(ROOT)->getBody()->getContents());

        $categories = $categoriesPage->find('.widget-list__item');
        foreach ($categories as $category) {
            $category = new Category($category);
            $categoryLink = $category->parse($this->client, $this->proxyPool);

            exit();
        }

    }

}