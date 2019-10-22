<?php


namespace models;


use core\DB;
use DiDom\Document;
use traits\Parsable;

class Ingredient extends DB
{
    use Parsable;

    private $categoryLink;
    private $categoryId;

    public function __construct($categoryLink, $categoryId)
    {
        $this->categoryLink = $categoryLink;
        $this->categoryId = $categoryId;
    }

    public function parse($client)
    {
        $ingredientsMainPage = new Document($client->get($this->categoryLink)->getBody()->getContents());
        $paginationLinks = $ingredientsMainPage->find('.wiki-page__alphabet a');

        foreach ($paginationLinks as $link) {
            $paginated = new Document($client->get($link->attr('href'))->getBody()->getContents());

            foreach ($paginated->find('.item-description') as $ingredient) {
                $uri = $ingredient->first('.title a')->attr('href');
                $name = $ingredient->first('.lead')->text();
                /*$ingredientData = [
                    'name'              => $ingredient->first('.title a')->text(),
                    'short_description' =>,
                    'uri'               => $uri
                ];*/
                $ingredientsPage = new Document($client->get($uri)->getBody()->getContents());
                $imageLink = 'https:' . $ingredientsPage->first('wiki__cover img')->attr('src');

                //DB::create('ingridients', ['name' => $title->text()]);
            }
        }

    }
}