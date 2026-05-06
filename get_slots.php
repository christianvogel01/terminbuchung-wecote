<?php
header("Content-Type: application/json");
require_once "db.php";

$date = $_GET["date"] ?? "";
$excludeId = $_GET["exclude_id"] ?? "";

if (!$date) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Kein Datum angegeben."
    ]);
    exit;
}

try {
    if ($excludeId) {
        $stmt = $pdo->prepare("
            SELECT appointment_time
            FROM bookings
            WHERE appointment_date = :date
            AND id != :exclude_id
        ");

        $stmt->execute([
            ":date" => $date,
            ":exclude_id" => $excludeId
        ]);
    } else {
        $stmt = $pdo->prepare("
            SELECT appointment_time
            FROM bookings
            WHERE appointment_date = :date
        ");

        $stmt->execute([
            ":date" => $date
        ]);
    }

    $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "success" => true,
        "bookedSlots" => $bookedSlots
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Die verfügbaren Zeiten konnten nicht geladen werden."
    ]);
}
