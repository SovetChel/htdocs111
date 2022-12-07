<?php
if (preg_replace("#/#", "", explode("?", $_SERVER["REQUEST_URI"])[0]) == "header.php") {
    header("Location: /");
};

$auth = '<a href="/login.php">Войти</a>';
if (check_auth()) {
    $auth = '<a href="/logout.php">Выход</a>';
};

$stmt = $db->query("SELECT * FROM `categories`");
$categories = $stmt->fetchAll();
?>
<header>
    <a href="/" class="logo">Страйкбольное<br>снаряжение</a>
    <div class="link-container">
        <div class="title">Категории</div>
        <div class="links">
        <a href="/">Все</a>
        <?php
            foreach($categories as $category) {
                echo <<< EOT
                <a href="/?category={$category['id']}">{$category['name']}</a>
                EOT;
            };
        ?>
        </div>
    </div>
    <?php
        if (check_auth()) {
            echo '<a href="/orders.php">Мои заказы</a>';
            if ($user["admin"]) {
                echo '<a href="/admin.php">Админ панель</a>';
            };
        };
    ?>
    <div class="header-right">
        <?php echo $auth ?>
    </div>
</header>