<?php
require_once __DIR__."/config.php";

if (check_auth()) {
    if (!$user["admin"]) {
        header("Location: /");
    };
} else {
    header("Location: /");
};

$create_list = ["product", "category"];
$sql_name_table = [
    "product" => "products",
    "category" => "categories"
];
$request_url = explode("/", preg_replace("/(^\/(.*)\/$|\/(.*))/", "$2$3", $_SERVER["REQUEST_URI"]));
$end_request_url = explode("?", end($request_url))[0];

if (count($request_url) < 2) {
    header("Location: /admin.php/product");
};

$table_name = $request_url[1];

if (!in_array($table_name, $create_list)) {
    header("Location: /admin.php/product");
};

$message = "";
$data = [];

$required_fields = [
    "product" => ["name", "description", "price", "category_id"],
    "category" => ["name"]
];
$fields = [];

if ($end_request_url == "create") {
    $data = $_POST;
    $insert_text = [
        "product" => "INSERT INTO `products` (`name`, `description`, `price`, `category_id`, `preview`) VALUES (:name, :description, :price, :category_id, :preview)",
        "category" => "INSERT INTO `categories` (`name`) VALUES (:name)"
    ];
    
    foreach($required_fields[$table_name] as $field) {
        if (isset($_POST[$field])) {
            $fields[$field] = $_POST[$field];
        } else {
            $message = "Заполните все поля!";
            break;
        };
    };
    
    if (!isset($_FILES["preview"]) && $table_name == "product") {
        $message = "Заполните все поля!";
    };
    
    if (!$message) {
        if ($table_name == "product") {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $ext = array_search(
                $finfo->file($_FILES['preview']['tmp_name']),
                [
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ],
                true
            );
        
            $fields["preview"] = "/uploads/".bin2hex(random_bytes(5)).".".$ext;
        
            move_uploaded_file($_FILES["preview"]["tmp_name"],__DIR__.$fields["preview"]);
        };
        
        $stmt = $db->prepare($insert_text[$table_name]);
        $stmt->execute($fields);

        if ($table_name == "product") {
            header("Location: /more_detailed.php?id={$db->lastInsertId()}");
        };
    };
} else if ($end_request_url == "update") {
    $id = $_GET["id"] ?? $_POST["id"] ?? -1;

    if ($id >= 0) {
        $_POST["id"] = $id;

        $update_text = [
            "product" => "UPDATE `products` SET `name` = :name, `description` = :description, `price` = :price, `category_id` = :category_id, `preview` = :preview WHERE `id` = :id",
            "category" => "UPDATE `categories` SET `name` = :name WHERE `id` = :id"
        ];

        $is_update = true;

        foreach($required_fields[$table_name] as $field) {
            if (!isset($_POST[$field])) {
                $is_update = false;
                break;
            };
        };

        if ($table_name == "product" && $is_update) {
            $stmt = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$id);
            $data = $stmt->fetch();

            $_POST["preview"] = $data["preview"];

            if (!$_FILES["preview"]["error"]) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $ext = array_search(
                    $finfo->file($_FILES['preview']['tmp_name']),
                    [
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ],
                    true
                );
            
                $name_file = explode("/", $data["preview"]);
                $name_file = explode(".", end($name_file));
                if ($name_file[1] == $ext) {
                    $_POST["preview"] = "/uploads/".$name_file[0].".".$ext;
                } else {
                    $_POST["preview"] = "/uploads/".bin2hex(random_bytes(5)).".".$ext;
                };
            
                move_uploaded_file($_FILES["preview"]["tmp_name"], __DIR__.$_POST["preview"]);
            };
        };

        if ($is_update) {
            $stmt = $db->prepare($update_text[$table_name]);
            $stmt->execute($_POST);
        };

        $stmt = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$id);
        $data = $stmt->fetch();
    };
} else if ($end_request_url == "remove") {
    if ($_POST["id"] ?? false) {
        $stmt = $db->query("DELETE FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$_POST["id"]);

        if ($table_name == "product") {
            header("Location: /");
        } else {
            header("Location: /admin.php/".$table_name);
        };
    } else if ($_GET["id"] ?? false) {
        $stmt = $db->query("SELECT * FROM `".$sql_name_table[$table_name]."` WHERE `id` = ".$_GET["id"]);
        $data = $stmt->fetch();
    };
};

$stmt = $db->query("SELECT * FROM `categories`");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <a href="/" class="logo">Страйкбольное<br>снаряжение</a>
            <?php
                if ($end_request_url == "product") {
                    echo '<a href="#" class="active">Добавить товар</a>';
                } else {
                    echo '<a href="/admin.php/product">Добавить товар</a>';
                };
                if ($end_request_url == "category") {
                    echo '<a href="#" class="active">Категории товаров</a>';
                } else {
                    echo '<a href="/admin.php/category">Категории товаров</a>';
                };
            ?>
        </header>
        <div class="content">
            <?php
            if ($message) {
                echo '<div class="error-mes">'.$message.'</div>';
            };
            ?>
            <?php
            if ($end_request_url == "update" || $end_request_url == "remove") {
                if ($_GET["id"] ?? $_POST["id"] ?? false) {
                    include_once __DIR__."/forms/".$end_request_url."_".$table_name.".php";
                } else {
                    echo '<h1>Запись не найдена!</h1>';
                };
            } else {
                include_once __DIR__."/forms/create_".$table_name.".php";
            };
            ?>
            <?php
            if ($table_name == "category") {
                echo '<div class="categories">';
                foreach($categories as $category) {
                    echo <<< EOT
                    <a href="/admin.php/category/update?id={$category['id']}">{$category['name']}</a>
                    EOT;
                };
                echo "</div>";
            };
            ?>
        </div>
    </div>
</body>
</html>