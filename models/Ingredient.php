<?php


namespace models;


use core\DB;
use DiDom\Document;
use traits\HTML;

class Ingredient extends DB
{
    use HTML;

    private $categoryLink;

    public function __construct($categoryLink)
    {
        $this->categoryLink = $categoryLink;
    }

    public function parse($client)
    {
        $counter = 0;
        $ingredientsPage = new Document($client->get($this->categoryLink)->getBody()->getContents());
        $paginationLinks = $ingredientsPage->find('.wiki-page__alphabet a');

        foreach ($paginationLinks as $link) {
            echo $link->attr('href') . "\n";
            $paginated = new Document($client->get($link->attr('href'))->getBody()->getContents());
           // echo $paginated->first('.title a')->text() . " ";
            foreach ($paginated->find('.item-description .title a') as $title) {
                echo $title->text() . " ";
                $counter++;
            }
        }
        echo "\n" . $counter;
    }
}