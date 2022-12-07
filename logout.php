<?php
require_once __DIR__."/config.php";

if (check_auth()) {
    unset($_SESSION["user_id"]);
};

header("Location: /");
?>