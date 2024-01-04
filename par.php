<?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Client;
    use GuzzleHttp\Cookie\CookieJar;
    use Sunra\PhpSimple\HtmlDomParser;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Csv;

    // Отримання URL з командного рядка
    if (isset($argc) && $argc < 2) {
        die("Usage: php par.php <url>\n");
    }

    $url = isset($argv[1]) ? $argv[1] : ''; // Встановлюємо значення $url, якщо доступний другий аргумент

// Виклик функції для парсингу сайту та збереження результатів в CSV
    parseAndSaveToCsv($url);

    function parseAndSaveToCsv($url)
    {
        // Створення об'єкта CookieJar для зберігання cookies
        $cookieJar = new CookieJar();

        // Створення клієнта Guzzle
        $client = new Client(['cookies' => $cookieJar]);

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

            // Приклад: отримання заголовка сторінки
//        $pageTitle = $dom->find('title', 0)->innertext;

            // Закриття ресурсів
            $dom->clear();
            unset($dom);

            // Створення об'єкта Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Додавання даних до аркуша CSV
//        $sheet->setCellValue('A1', 'Page Title');
//        $sheet->setCellValue('B1', $pageTitle);
            $sheet->setCellValue('A1', 'Product Name');
            $sheet->setCellValue('B1', 'Price');
            $sheet->setCellValue('C1', 'Description');
            $sheet->setCellValue('D1', 'Image URL');
            $sheet->setCellValue('A2', $productName);
            $sheet->setCellValue('B2', $productPrice);
            $sheet->setCellValue('C2', $productDescription);
            $sheet->setCellValue('D2', $productImage);

            // Додайте інші дані, які ви бажаєте записати

            // Збереження файлу CSV
            $csvFileName = 'shafa.csv';
            $writer = new Csv($spreadsheet);
            $writer->save($csvFileName);

            echo "Data saved to $csvFileName\n";
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

