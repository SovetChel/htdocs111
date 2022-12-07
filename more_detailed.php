<?php
require_once __DIR__."/config.php";

$product = null;
$buy = false;

if (isset($_GET["id"]) || isset($_POST["id"])) {
    $id = $_POST["id"] ?? $_GET["id"];

    if (ctype_digit($id) || is_int($id)) {
        $stmt = $db->query("SELECT * FROM `products` WHERE `id` = ".$id);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_POST["id"])) {
            if (check_auth()) {
                $stmt = $db->prepare("INSERT INTO `orders` (`user_id`, `product_id`, `price`) VALUES (:user_id, :product_id, :price)");
                $stmt->execute([
                    "user_id" => $user["id"],
                    "product_id" => $product["id"],
                    "price" => $product["price"]
                ]);
                header("Location: /orders.php");
            };
        };
    };
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страйкбольный магазин</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/more_detailed.css">
    <script src="js/more_detailed.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include_once __DIR__."/header.php"; ?>
        <div class="content">
            <?php
                if ($product) {
                    $bt_buy = '<button id="buy">Купить</button>';

                    if (check_auth()) {
                        if ($user["admin"]) {
                            echo <<< EOT
                            <div class="edit-links">
                                <a href="/admin.php/product/update?id={$product['id']}">Редактировать</a>
                                <a href="/admin.php/product/remove?id={$product['id']}">Удалить товар</a>
                            </div>
                            EOT;
                        };
                    } else {
                        $bt_buy = '<a href="/login.php">Купить</a>';
                    };

                    echo <<< EOT
                    <img src="{$product['preview']}">
                    <h1>{$product['name']}</h1>
                    <div class="bt-container">
                        $bt_buy
                        <span class="price">{$product['price']} р.</span>
                    </div>
                    <p class="description">{$product['description']}</p>
                    EOT;

                    include_once __DIR__."/forms/confirm_purchase.php";
                } else {
                    echo <<< EOT
                    <h1 class="not-product">Товар не найден!</h1>
                    EOT;
                };
            ?>
        </div>
    </div>
</body>
</html>