<?php

    require 'vendor/autoload.php'; // Підключення autoload Composer
    use voku\helper\HtmlDomParser;

// URL сторінки, яку ви хочете парсити
    $url = 'https://shafa.ua/uk/member/monterina';

// Отримання HTML-коду сторінки
    $html = HtmlDomParser::file_get_html($url);

// Перевірка, чи сторінка вдалося завантажити
    if (!$html) {
        die('Не вдалося завантажити сторінку.');
    }

// Відкриття CSV-файлу для запису
    $file = fopen('shafa.csv', 'w');

// Запис заголовків у CSV-файл
    fputcsv($file, ['Назва', 'Ціна', 'Опис', 'Посилання', 'Зображення']);

// Парсинг даних товарів
    foreach ($html->find('.product-item') as $product) {
        $name = $product->find('.product-title', 0)->plaintext;
        $price = $product->find('.D8o9s7KcxqtQ7bd2ka_W', 0)->plaintext;
        $description = $product->find('.product-description', 0)->plaintext;
        $link = $product->find('.product-title a', 0)->href;
        $image = $product->find('.WKtFn6qxBj5SbHTbZQJK', 0)->src;

        // Запис даних у CSV-файл
        fputcsv($file, [$name, $price, $description, $link, $image]);
    }

// Закриття CSV-файлу
    fclose($file);

    echo 'Дані успішно витягнуті та збережені у файл shafa.csv';

