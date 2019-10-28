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
        $page = 1;
        while (true) {
            $paginated = $this->getHTML($this->categoryLink . '?page=' . $page, $pool, $client);

            if (!$paginated) {
                break;
            }

            foreach ($paginated->find('.item-description') as $ingredient) {

                $ingredientData = $this->getInfo($ingredient, $client, $pool);
                $ingredientData = $this->getImage('./images/ingredients/', $ingredientData);

                $ingredientData['parent_id'] = DB::create('ingredients', $ingredientData);
                $ingredientData['short_description'] = null;

                echo "Parsed ingredient: {$ingredientData['name']} \n";

                foreach ($this->page->find('.wiki__sub-item') as $subItem) {
                    $this->parseSubItem($subItem, $ingredientData);
                }

            }

            $page++;
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
            $data = $this->getImage('./images/ingredients/', $data);
        } else {
            $data['image'] = null;
            $data['img_origin_link'] = null;
        }

        DB::create('ingredients', $data);
        echo "Parsed ingredient: {$data['name']} \n";
    }

    private function getInfo($item, $client, $pool)
    {
        $uri = $item->first('.title a')->attr('href');
        $shortDescription = trim($item->first('.lead')->text());

        $this->page = $this->getHTML($uri, $pool, $client);
        $imageUri = 'https:' . $this->page->first('.wiki__cover img')->attr('src');

        $name = $this->page->first('.wiki__title')->text();
        $description = $this->page->first('.wiki__description')->text();

        return [
            'name'              => $name,
            'short_description' => !empty($shortDescription) ? $shortDescription : null,
            'uri'               => $uri,
            'image'             => "{$name}.jpg",
            'img_origin_link'   => $imageUri,
            'description'       => !empty($description) ? trim($description) : null,
            'category_id'       => $this->categoryId,
        ];
    }
}