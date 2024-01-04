<?php

    require 'vendor/autoload.php'; // Завантажте автозавантаження Composer

    use voku\helper\HtmlDomParser;

// URL сайту для парсингу
    $url = 'https://shafa.ua/';

// Отримати HTML-код сторінки
    $html = HtmlDomParser::file_get_html($url);

// Відкрийте CSV-файл для запису
    $csvFile = fopen('shafa.csv', 'w');

// Запишіть заголовок CSV-файлу
    fputcsv($csvFile, ['Назва', 'Ціна', 'Опис', 'Посилання', 'Зображення']);

// Знайдіть необхідні елементи на сторінці та запишіть їх у CSV-файл
    foreach ($html->find('.product-item') as $product) {
        $productName = trim($product->find('.product-title', 0)->plaintext);
        $productPrice = trim($product->find('.product-price', 0)->plaintext);
        $productDescription = trim($product->find('.product-description', 0)->plaintext);
        $productLink = 'https://shafa.ua' . $product->find('.product-title a', 0)->href;
        $productImage = $product->find('.product-image img', 0)->src;

        fputcsv($csvFile, [$productName, $productPrice, $productDescription, $productLink, $productImage]);
    }

// Закрийте CSV-файл
    fclose($csvFile);

    echo 'Дані були успішно записані в shafa.csv';
