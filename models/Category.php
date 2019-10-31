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
        $name = trim($this->category->first('.widget-list__item-title a')->text());
        $imageUri = $this->getUrlFromStyle($this->category->first('.widget-list__image')->attr('style'));

        $categoryData = [
            'name'              => $name,
            'uri'               => $uri,
            'image'             => "{$name}.jpg",
            'img_origin_link'   => $imageUri
        ];

        $categoryData = $this->getImage('./images/categories/', $categoryData);
        $categoryId = DB::create('categories', $categoryData);

        return [
            'id'    => $categoryId,
            'name'  => $name,
            'uri'   => $uri
        ];
    }
}