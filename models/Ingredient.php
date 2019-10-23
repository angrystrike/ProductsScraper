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
    private $page;

    public function __construct($categoryLink, $categoryId)
    {
        $this->categoryLink = $categoryLink;
        $this->categoryId = $categoryId;
    }

    public function parse($client, $pool)
    {
        $ingredientsMainPage = $this->getHTML($this->categoryLink, $pool, $client);
        $paginationLinks = $ingredientsMainPage->find('.wiki-page__alphabet a');

        foreach ($paginationLinks as $link) {
            $paginated = $this->getHTML($link->attr('href'), $pool, $client);

            foreach ($paginated->find('.item-description') as $ingredient) {

                $ingredientData = $this->getInfo($ingredient, $client, $pool);
                file_put_contents("./public/images/ingredients/{$ingredientData['image']}", file_get_contents($ingredientData['img_origin_link']));

                $ingredientData['parent_id'] = DB::create('ingredients', $ingredientData);
                $ingredientData['short_description'] = null;

                echo "\nMain ingredient: {$ingredientData['name']}";

                foreach ($this->page->find('.wiki__sub-item') as $subItem) {
                    $this->parseSubItem($subItem, $ingredientData);
                }

            }
        }

    }

    private function parseSubItem($item, $data)
    {
        $description = $item->first('.wiki__description')->text();
        $data['description'] = !empty($description) ? trim($description) : null;

        $data['name'] = $item->first('.wiki__second-title')->text();

        $image = $item->first('.wiki__sub-img img');
        if (!empty($image)) {
            $data['image'] = $data['name'] . '.jpg';
            $data['img_origin_link'] = 'https:' . $image->attr('src');
            file_put_contents("./public/images/ingredients/{$data['image']}", file_get_contents($data['img_origin_link']));
        } else {
            $data['image'] = null;
            $data['img_origin_link'] = null;
        }

        DB::create('ingredients', $data);
        echo "\nSub ingredient: {$data['name']}";
    }

    private function getInfo($item, $client, $pool)
    {
        $uri = $item->first('.title a')->attr('href');
        $shortDescription = $item->first('.lead')->text();

        $this->page = $this->getHTML($uri, $pool, $client);
        $imageUri = 'https:' . $this->page->first('.wiki__cover img')->attr('src');

        $name = $this->page->first('.wiki__title')->text();
        $description = $this->page->first('.wiki__description')->text();

        return [
            'name'              => $name,
            'short_description' => trim($shortDescription),
            'uri'               => $uri,
            'image'             => "{$name}.jpg",
            'img_origin_link'   => $imageUri,
            'description'       => trim($description),
            'category_id'       => $this->categoryId,
        ];
    }
}