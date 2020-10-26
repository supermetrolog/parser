<?php
require_once('vendor/autoload.php');

const BLOCK_PATH = '.ZINbbc';
const RATING_PATH = '.kCrYT:last > div > .BNeawe > div > .v9i61e > .BNeawe > .r0bn4c:last > .Eq0J8';
const URL_PATH = '.kCrYT:first > a';

const URL_FIRST_PART = 'https://www.google.com/search?q=%D0%94%D0%B8%D0%BD%D0%B0%D1%81%D1%82%D0%B8%D1%8F+%D0%BF%D1%80%D0%B0%D0%B9%D0%B4&client=firefox-b-d&sxsrf=ALeKk01H8mn9mZtyL-kr59mz9eKvT_Gnsw:1603748323014&ei=40GXX6Mew9yuBMjGm0A&start=';
const URL_LAST_PART = '&sa=N&ved=2ahUKEwjjm-iInNPsAhVDrosKHUjjBggQ8tMDegQIHxA0&biw=1304&bih=694';

$data = getRating();
printData($data);

function getRating()
{
    $i = 0;
    $pageMax = 50; //Количество страниц которые необходимо спарсить
    while ($i < $pageMax) {
        $url = URL_FIRST_PART . $i . URL_LAST_PART;
        $content = file_get_contents($url);

        $document = \phpQuery::newDocument($content);
        $block = $document->find(BLOCK_PATH); //Получаем блоки каждого сайта с текущей страницы
        
        $data[] = getData($block); //Получаем массив рейтингов и сайтов из блоков
        $i += 10; //Переходим на следующую страницу
    }
    return $data;
}

function getData($block)
{
    $key = 0;
    foreach ($block as $article) {
        $article = pq($article);
        if(($rating = $article->find(RATING_PATH)->text()) != ""){
            $data[$key]['url'] = $article->find(URL_PATH)->attr('href');
            $data[$key]['rating'] = $rating;
            $key++;
        }
    }
    if ($data === null) {
        return false;
    }
    return normalizeData($data); //Приводим данные в нужный вид и возвращаем
}


function normalizeData($data)
{
    foreach ($data as $key => $value) {
        $result[$key]['rating'] = normalizeRating($value['rating']);
        $result[$key]['url'] = normalizeUrl($value['url']);
    }

    return $result;
}

function normalizeRating($rating)
{
    return substr($rating, 0, 3);
}
function normalizeUrl($url)
{
    $url = substr($url, 15);
    return substr($url, 0, strpos($url, '/'));
}

//Вывод результата работы
function printData($data)
{
    if ($data === false) {
        return false;
    }
    echo '<pre>';
    foreach ($data as $value) {
        if ($value == false) {
            continue;
        }
        foreach ($value as $item) {
            echo '"'.$item['rating'].'" - "'.$item['url'].'"'.'<br>';
        }
    }
}


