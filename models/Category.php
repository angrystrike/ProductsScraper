<?php


namespace models;


use core\DB;
use traits\HTML;

class Category extends DB
{
    use HTML;

    private $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public function parse($proxyPool, $client)
    {
        $categoryData = [
            'name'  => $this->category->first('.widget-list__item-title a')->text(),
            'uri'   => $this->category->first('.widget-list__item-link')->attr('href'),
            'image' => $this->category->first('.widget-list__image')->attr('style')
        ];

        DB::create('categories', $categoryData);
    }
}