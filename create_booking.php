<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_SESSION["patient_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Bitte melden Sie sich zuerst an."
    ]);
    exit;
}

$date = $_POST["date"] ?? "";
$time = $_POST["time"] ?? "";
$reason = trim($_POST["reason"] ?? "");

$patientId = $_SESSION["patient_id"];
$firstName = $_SESSION["patient_first_name"];
$lastName = $_SESSION["patient_last_name"];
$email = $_SESSION["patient_email"];

if (!$date || !$time) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Bitte wählen Sie Datum und Uhrzeit aus."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO bookings
        (patient_id, appointment_date, appointment_time, first_name, last_name, email, phone, reason)
        VALUES
        (:patient_id, :date, :time, :first_name, :last_name, :email, '', :reason)
    ");

    $stmt->execute([
        ":patient_id" => $patientId,
        ":date" => $date,
        ":time" => $time,
        ":first_name" => $firstName,
        ":last_name" => $lastName,
        ":email" => $email,
        ":reason" => $reason
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Termin erfolgreich gebucht."
    ]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Dieser Termin ist bereits vergeben."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Beim Speichern ist ein Fehler aufgetreten."
        ]);
    }
}
