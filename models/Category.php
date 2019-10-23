<?php


namespace models;


use core\DB;
use traits\Parsable;

class Category extends DB
{
    use Parsable;

    private $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public function parse()
    {
        $uri = $this->category->first('.widget-list__item-link')->attr('href');
        $name = $this->category->first('.widget-list__item-title a')->text();
        $imageUri = $this->getUrlFromStyle($this->category->first('.widget-list__image')->attr('style'));

        $categoryData = [
            'name'              => $name,
            'uri'               => $uri,
            'image'             => "{$name}.jpg",
            'img_origin_link'   => $imageUri
        ];
        file_put_contents("./public/images/categories/{$name}.jpg", file_get_contents($imageUri));

        echo "Category: {$name}\n";
        $categoryId = DB::create('categories', $categoryData);

        return [$uri, $categoryId];
    }
}