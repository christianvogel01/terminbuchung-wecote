<?php
header("Content-Type: application/json");

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/validation.php";
require_once __DIR__ . "/includes/helpers.php";

$date = validateAppointmentDate($_GET["date"] ?? "");
$excludeId = (int)($_GET["exclude_id"] ?? 0);

if ($date === false) {
    jsonError(400, "Ungültiges Datum angegeben.");
}

try {
    $sql = "
        SELECT appointment_time
        FROM bookings
        WHERE appointment_date = :date
    ";

    $params = [
        ":date" => $date
    ];

    if ($excludeId > 0) {
        $sql .= " AND id != :exclude_id";
        $params[":exclude_id"] = $excludeId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $bookedSlots = array_map("formatTime", $stmt->fetchAll(PDO::FETCH_COLUMN));

    echo json_encode([
        "success" => true,
        "bookedSlots" => $bookedSlots
    ]);
} catch (PDOException $e) {
    error_log("get_slots Fehler: " . $e->getMessage());
    jsonError(500, "Die verfügbaren Zeiten konnten nicht geladen werden.");
}
