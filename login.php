<?php
require_once __DIR__."/config.php";

if (check_auth()) {
    header("Location: /");
};

$len_username = 1;
$len_password = 1;
$username_message = "";
$password_message = "";

if (isset($_POST["username"], $_POST["password"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $len_username = strlen($username);
    $len_password = strlen($password);

    if ($len_username && $len_password) {
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `username` = :username");
        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                header("Location: /");
            } else {
                $password_message = "Неверный пароль!";
            };
        } else {
            $username_message = "Пользователь не найден!";
        };
    };
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <a href="/" class="logo">Страйкбольное снаряжение</a>
        </header>
        <div class="content">
            <form action="/login.php" method="POST">
                <?php
                    if ($username_message) {
                        echo sprintf("<div class='error-mes'>%s</div>", $username_message);
                    } else if (!$len_username) {
                        echo "<div class='error-mes'>Введите логин!</div>";
                    };
                ?>
                <input type="text" name="username" value="<?php echo trim($_POST["username"] ?? "") ?>" placeholder="Логин">
                <?php
                    if ($password_message) {
                        echo sprintf("<div class='error-mes'>%s</div>", $password_message);
                    } else if (!$len_password) {
                        echo "<div class='error-mes'>Введите пароль!</div>";
                    };
                ?>
                <input type="password" name="password" value="<?php echo trim($_POST["password"] ?? "") ?>" placeholder="Пароль">
                <input type="submit" value="Войти">
            </form>
            <a href="/registration.php">Регистрация</a>
        </div>
    </div>
</body>
</html>