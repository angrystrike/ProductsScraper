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
                $shortDescription = $ingredient->first('.lead')->text();
                $ingredientsPage = new Document($client->get($uri)->getBody()->getContents());

                $imageUri = 'https:' . $ingredientsPage->first('.wiki__cover img')->attr('src');
                $name = $ingredientsPage->first('.wiki__title')->text();
                $description = $ingredientsPage->first('.wiki__description')->text();

                file_put_contents("./public/images/ingredients/{$name}.jpg", file_get_contents($imageUri));

                $ingredientData = [
                    'name'              => $name,
                    'short_description' => $shortDescription,
                    'uri'               => $uri,
                    'image'             => "{$name}.jpg",
                    'img_origin_link'   => $imageUri,
                    'description'       => $description,
                    'category_id'       => $this->categoryId
                ];

                echo "Main ingr: {$name}\n";

                $ingredientData['parent_id'] = DB::create('ingridients', $ingredientData);
                $ingredientData['short_description'] = null;

                foreach ($ingredientsPage->find('.wiki__sub-item') as $subItem) {

                    $description = $subItem->first('.wiki__description')->text();
                    $ingredientData['description'] = !empty($description) ? $description : null;

                    $ingredientData['name'] = $subItem->first('.wiki__second-title')->text();

                    $image = $subItem->first('.wiki__sub-img img');
                    if (!empty($image)) {
                        $ingredientData['image'] = $ingredientData['name'] . '.jpg';
                        $ingredientData['img_origin_link'] = 'https:' . $image->attr('src');
                    }

                    echo "Sub ingr: {$ingredientData['name']}\n";

                    file_put_contents("./public/images/ingredients/{$ingredientData['image']}", file_get_contents($ingredientData['img_origin_link']));
                    DB::create('ingridients', $ingredientData);
                }

            }
        }

    }
}