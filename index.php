<?php

use core\General;

define('ROOT', 'https://eda.ru/wiki/ingredienty');

require_once 'vendor/autoload.php';


$scraper = new General();
$scraper->parseWholeSite();

