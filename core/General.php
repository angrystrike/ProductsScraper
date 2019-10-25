<?php


namespace core;


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
        $params = require 'config/params.php';
        $this->proxyPool = new ProxyPool($params['proxiesLink']);
    }

    public function parseWholeSite()
    {
        $categoriesPage = $this->getHTML(ROOT, $this->proxyPool, $this->client);
        $categories = $categoriesPage->find('.widget-list__item');

        foreach ($categories as $category) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                die("Error: impossible to fork\n");
            } elseif ($pid) {
                $childProcesses[] = $pid;
            } else {
                $category = new Category($category);
                $categoryData = $category->parse();

                $ingredient = new Ingredient($categoryData['uri'], $categoryData['id']);
                $ingredient->parse($this->client, $this->proxyPool);

                echo "\nCategory {$categoryData['name']} was parsed\n";

                exit();
            }
        }

        foreach ($childProcesses as $pid) {
            pcntl_waitpid($pid, $status);
        }

        echo "\nParsing finished. Total statistics:\n";
        echo  "\nTotal categories: " .  DB::count('categories');
        echo  "\nTotal ingredients: " .  DB::count('ingredients');
    }

}