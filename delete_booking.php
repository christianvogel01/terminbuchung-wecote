<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit;
}

$id = $_POST["id"] ?? "";

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
    $stmt->execute([":id" => $id]);
}

header("Location: admin.php");
exit;
