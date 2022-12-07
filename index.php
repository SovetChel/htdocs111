<?php
// Подключение php файла
// __DIR__ - текущий каталог
require_once __DIR__."/config.php";
// Фильтер для поиска по товарам
$filter = "";
// Проверка есть ли в запросе категория
if (isset($_GET["category"])) {
    // Проверка является ли значение числом
    if (ctype_digit($_GET["category"])) {
        // Добавляем условие в запрос
        $filter = "WHERE `category_id` = ". $_GET["category"];
    };
};
if (isset($_GET["search"])) {
    $filter = "WHERE `name` like '%". $_GET["search"] ."%'";
};
// Запрос из БД товаров с условием, если оно есть
$stmt = $db->query("SELECT * FROM `products` ". $filter ." ORDER BY -`id`");
// Запрос массива с значениями
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страйкбольный магазин</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/index.css">
</head>
<body>
    <div class="wrapper">
        <?php include_once __DIR__."/header.php"; ?>
        <form action="" method="GET">
            <input type="search" name="search" placeholder="Поиск">
            <input type="submit" value="Поиск">
        </form>
        <div class="content">
            <?php
            if (count($products)) {
                foreach ($products as $product) {
                    // EOT - начало и конец строки
                    echo <<< EOT
                    <a href="/more_detailed.php?id={$product['id']}" class="card">
                        <img src="{$product['preview']}">
                        <p>{$product['name']}</p>
                        <span style="color:#2775ba">{$product['price']} р.</span>
                    </a>
                    EOT;
                };
            } else {
                echo <<< EOT
                <h1>Товар не найден!</h1>
                EOT;
            };
            ?>
        </div>
    </div>
</body>
</html>