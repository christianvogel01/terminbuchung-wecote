<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/helpers.php";
require_once __DIR__ . "/includes/csrf.php";

requirePatient();
requireCsrfToken();

$patientId = currentPatientId();
$bookingId = (int)($_POST["booking_id"] ?? 0);

if ($bookingId <= 0) {
    header("Location: my_bookings.php?error=notfound");
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, appointment_date, appointment_time
    FROM bookings
    WHERE id = :id AND patient_id = :patient_id
");
$stmt->execute([
    ":id" => $bookingId,
    ":patient_id" => $patientId
]);

$booking = $stmt->fetch();

if (!$booking) {
    header("Location: my_bookings.php?error=notfound");
    exit;
}

if (!canCancelBooking($booking["appointment_date"], $booking["appointment_time"])) {
    header("Location: my_bookings.php?error=too_late");
    exit;
}

$deleteStmt = $pdo->prepare("
    DELETE FROM bookings
    WHERE id = :id AND patient_id = :patient_id
");
$deleteStmt->execute([
    ":id" => $bookingId,
    ":patient_id" => $patientId
]);

header("Location: my_bookings.php?cancelled=1");
exit;
