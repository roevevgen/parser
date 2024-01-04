<?php

    require 'vendor/autoload.php';

    use GuzzleHttp\Client;
    use Sunra\PhpSimple\HtmlDomParser;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Csv;

// Жорстко закодований URL
    $url = "https://shafa.ua/uk/member/monterina";

// Виклик функції для парсингу сайту та збереження результатів в CSV
    parseAndSaveToCsv($url);

    function parseAndSaveToCsv($url)
    {
        // Створення клієнта Guzzle
        $client = new Client([
            // Вимкнення перевірки SSL
            'verify' => false
        ]);

        try {
            // Виконання HTTP-запиту
            $response = $client->request('GET', $url);

            // Перевірка статусу відповіді
            if ($response->getStatusCode() !== 200) {
                throw new Exception('Error: ' . $response->getStatusCode());
            }

            // Отримання HTML-вмісту сторінки
            $html = $response->getBody()->getContents();

            // Парсинг HTML за допомогою SimpleHTMLDom
            $dom = HtmlDomParser::str_get_html($html);

            // Парсинг назви товару
            $productName = $dom->find('.CnMTkDcKcdyrztQsbqaj', 0)->innertext;

            // Парсинг ціни товару
            $productPrice = $dom->find('.D8o9s7KcxqtQ7bd2ka_W', 0)->innertext;

            // Парсинг опису товару
            $productDescription = $dom->find('.product-description-selector', 0)->innertext;

            // Парсинг URL фотографії товару
            $productImage = $dom->find('.WKtFn6qxBj5SbHTbZQJK', 0)->src;

            // Закриття ресурсів
            $dom->clear();
            unset($dom);

            // Створення об'єкта Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Додавання даних до аркуша CSV
            $sheet->setCellValue('A1', 'Product Name');
            $sheet->setCellValue('B1', 'Price');
            $sheet->setCellValue('C1', 'Description');
            $sheet->setCellValue('D1', 'Image URL');
            $sheet->setCellValue('A2', $productName);
            $sheet->setCellValue('B2', $productPrice);
            $sheet->setCellValue('C2', $productDescription);
            $sheet->setCellValue('D2', $productImage);

            // Збереження файлу CSV
            $csvFileName = 'shafa.csv';
            $writer = new Csv($spreadsheet);
            $writer->save($csvFileName);

            echo "Data saved to $csvFileName\n";
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

