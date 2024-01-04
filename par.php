<?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Client;
    use Sunra\PhpSimple\HtmlDomParser;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Отримання URL з командного рядка
    if ($argc < 2) {
        die("Usage: php parse_shafa.php <url>\n");
    }

    $url = $argv[1];

// Виклик функції для парсингу сайту та збереження результатів в Excel
    parseAndSaveToExcel($url);

    function parseAndSaveToExcel($url) {
        // Створення клієнта Guzzle
        $client = new Client();

        // Виконання HTTP-запиту
        $response = $client->request('GET', $url);

        // Отримання HTML-вмісту сторінки
        $html = $response->getBody()->getContents();

        // Парсинг HTML за допомогою SimpleHTMLDom
        $dom = HtmlDomParser::str_get_html($html);

        // Ваш код для парсингу конкретних елементів сторінки

        // Приклад: отримання заголовка сторінки
        $pageTitle = $dom->find('title', 0)->innertext;

        // Закриття ресурсів
        $dom->clear();
        unset($dom);

        // Створення об'єкта Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Додавання даних до аркуша Excel
        $sheet->setCellValue('A1', 'Page Title');
        $sheet->setCellValue('B1', $pageTitle);

        // Додайте інші дані, які ви бажаєте записати

        // Збереження файлу Excel
        $excelFileName = 'shafa.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($excelFileName);

        echo "Data saved to $excelFileName\n";
    }
