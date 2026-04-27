<?php
header("Content-Type: application/json");
require_once "db.php";

$date = $_GET["date"] ?? "";

if (!$date) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Kein Datum angegeben."
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT appointment_time 
    FROM bookings 
    WHERE appointment_date = :date
");

$stmt->execute([
    ":date" => $date
]);

$bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    "success" => true,
    "bookedSlots" => $bookedSlots
]);
?>

