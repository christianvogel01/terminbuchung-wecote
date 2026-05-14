<?php
session_start();
header("Content-Type: application/json");

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/validation.php";
require_once __DIR__ . "/includes/helpers.php";
require_once __DIR__ . "/includes/csrf.php";

requireCsrfTokenJson();

if (!isset($_SESSION["patient_id"])) {
    jsonError(401, "Bitte melden Sie sich zuerst an.");
}

$date = validateAppointmentDate($_POST["date"] ?? "");
$time = validateAppointmentTime($_POST["time"] ?? "");
$reason = sanitizeText($_POST["reason"] ?? "", 500);

if ($date === false) {
    jsonError(400, "Ungültiges Datum. Bitte wählen Sie einen Werktag in der Zukunft.");
}

if ($time === false) {
    jsonError(400, "Ungültige Uhrzeit. Bitte wählen Sie einen gültigen Termin-Slot.");
}

$patientId = (int)$_SESSION["patient_id"];
$firstName = $_SESSION["patient_first_name"] ?? "";
$lastName = $_SESSION["patient_last_name"] ?? "";
$email = $_SESSION["patient_email"] ?? "";

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

    jsonSuccess("Termin erfolgreich gebucht.");
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        jsonError(409, "Dieser Termin ist bereits vergeben.");
    }

    error_log("create_booking Fehler: " . $e->getMessage());
    jsonError(500, "Beim Speichern ist ein Fehler aufgetreten.");
}
