<?php


namespace core;


use DiDom\Document;
use GuzzleHttp\Client;
use models\Category;
use models\Ingredient;
use traits\Parsable;


class General
{
    use Parsable;

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
            $pid = pcntl_fork();
            if (!$pid) {
                $category = new Category($category);
                $categoryData = $category->parse();

                $ingredient = new Ingredient($categoryData[0], $categoryData[1]);
                $ingredient->parse($this->client);
                exit();
            }
        }

    }

}