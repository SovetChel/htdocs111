<?php
session_start();
function check_auth() : bool {
    return !!($_SESSION["user_id"] ?? false);
};

$db = new PDO("sqlite:db.sqlite3");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = null;

if (check_auth()) {
    $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $stmt->execute(["id" => $_SESSION["user_id"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
};
?>