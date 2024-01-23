<?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Client;
    use GuzzleHttp\Cookie\CookieJar;
    use Sunra\PhpSimple\HtmlDomParser;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Csv;

    // Жорстко закодований URL
    $url = "https://avrora.ua/novynky/";

    // Виклик функції для парсингу сайту та збереження результатів в CSV
    parseAndSaveToCsv($url);

    function parseAndSaveToCsv($url)
    {
        // Створення об'єкта CookieJar для зберігання cookies
        $cookieJar = new CookieJar();

        // Створення клієнта Guzzle з використанням cookies
        $client = new Client([
            'cookies' => $cookieJar,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
            ],
            'verify' => false
        ]);

        try {
            // Виконання HTTP-запиту методом POST
            $response = $client->request('POST', $url, [
                'form_params' => [
                    // Тут ваші параметри для POST запиту
                ]
            ]);

            // Перевірка статусу відповіді
            if ($response->getStatusCode() !== 200) {
                throw new Exception('Error: ' . $response->getStatusCode());
            }

            // Отримання HTML-вмісту сторінки
            $html = $response->getBody()->getContents();

            // Парсинг HTML за допомогою SimpleHTMLDom
            $dom = HtmlDomParser::str_get_html($html);

            // Парсинг назви товару
            $productName = $dom->find('.product-title', 0)->innertext;

            // Парсинг ціни товару
            $productPrice = $dom->find('.ty-price-num', 0)->innertext;

            // Парсинг URL фотографії товару
            $productImage = $dom->find('.gallery-products__items', 0)->src;

            // Закриття ресурсів
            $dom->clear();
            unset($dom);

            // Створення об'єкта Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Додавання даних до аркуша CSV
            $sheet->setCellValue('A1', 'Product Name');
            $sheet->setCellValue('B1', 'Price');
            $sheet->setCellValue('C1', 'Image URL');
            $sheet->setCellValue('A2', $productName);
            $sheet->setCellValue('B2', $productPrice);
            $sheet->setCellValue('C2', $productImage);

            // Збереження файлу CSV
            $csvFileName = 'products.csv';
            $writer = new Csv($spreadsheet);
            $writer->save($csvFileName);

            echo "Data saved to $csvFileName\n";
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

