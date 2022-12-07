<?php
require_once __DIR__."/config.php";

if (!check_auth()) {
    header("Location: /");
};

if (isset($_POST["id"])) {
    if (ctype_digit($_POST["id"]) || is_int($_POST["id"])) {
        $db->query("DELETE FROM `orders` WHERE `id` = ".$_POST["id"]." and `user_id` = ".$user["id"]);
    };
};

$stmt = $db->prepare("SELECT * FROM `orders` WHERE `user_id` = :id");
$stmt->execute(["id" => $user["id"]]);
$orders = $stmt->fetchAll();
$products = [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страйкбольный магазин</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/orders.css">
    <script src="js/orders.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include_once __DIR__."/header.php"; ?>
        <div class="content">
            <?php
            if (count($orders)) {
                foreach($orders as $order) {
                    if (!in_array($order["product_id"], $products)) {
                        $stmt = $db->query("SELECT * FROM `products` WHERE `id` = ".$order["product_id"]);
                        $products[$order["product_id"]] = $stmt->fetch();
                    };
                    echo <<< EOT
                    <div class="order">
                        <div class="name">{$products[$order['product_id']]['name']}</div>
                        <div class="price">{$order['price']} р.</div>
                        <button id="{$order['id']}" class="order-cancel">Отменить заказ</button>
                    </div>
                    EOT;
                };
            } else {
                echo '<h1>Заказов ещё нет</h1>';
            };
            ?>
        </div>
    </div>
    <div id="order-cancel">
        <p>Подтверждение отмены заказа<button id="close-order-cancel"></button></p>
        <form action="" method="POST">
            <input type="hidden" name="id" id="id-order" value="">
            <p class="name">Товар: </p>
            <p class="price">Цена:  р.</p>
            <input type="submit" value="Отменить заказ" disabled>
        </form>
    </div>
</body>
</html>