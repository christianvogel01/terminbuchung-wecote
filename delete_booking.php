<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/csrf.php";

requireAdmin();
requireCsrfToken();

$id = (int)($_POST["id"] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
    $stmt->execute([":id" => $id]);
}

header("Location: admin.php");
exit;
