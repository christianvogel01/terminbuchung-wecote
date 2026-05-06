<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit;
}

$patientId = $_SESSION["patient_id"];
$bookingId = $_POST["booking_id"] ?? "";

if (!$bookingId) {
    header("Location: my_bookings.php");
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

$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: my_bookings.php?error=notfound");
    exit;
}

$appointmentDateTime = strtotime($booking["appointment_date"] . " " . $booking["appointment_time"]);
$limitDateTime = time() + (24 * 60 * 60);

if ($appointmentDateTime <= $limitDateTime) {
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
