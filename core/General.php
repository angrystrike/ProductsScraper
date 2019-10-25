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
        $this->client = new Client();
        $params = require 'config/params.php';
        $this->proxyPool = new ProxyPool($params['proxiesLink']);
        DB::setConnection($params);
    }

    public function parseWholeSite()
    {
        $categoriesPage = $this->getHTML(ROOT, $this->proxyPool, $this->client);
        $categories = $categoriesPage->find('.widget-list__item');

        foreach ($categories as $category) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                die("Error: impossible to fork \n");
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


        $sql = 'SELECT categories.name, count(*) as ingredient_count FROM ingredients JOIN categories ON ingredients.category_id = categories.id GROUP BY category_id';
        $stats = DB::getConnection()->query($sql)->fetchAll();
        echo "\nParsing finished. Statistics:\n";

        foreach ($stats as $stat) {
            echo "{$stat['ingredient_count']} ingredients in {$stat['name']}\n";
        }
        echo "\nTotal categories: " .  DB::count('categories') . "\n";
        echo "Total ingredients: " .  DB::count('ingredients') . "\n";
    }

}