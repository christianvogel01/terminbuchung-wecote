<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/vendor/autoload.php";

function sendConfirmationMail($toEmail, $toName, $date, $time, $reason = "") {
    $config = require __DIR__ . "/mail_config.php";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config["host"];
        $mail->SMTPAuth = true;
        $mail->Username = $config["username"];
        $mail->Password = $config["password"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config["port"];

        $mail->setFrom($config["from_email"], $config["from_name"]);
        $mail->addAddress($toEmail, $toName);

        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->Subject = "Terminbestätigung – Praxis Dr. Müller";

        $safeName = htmlspecialchars($toName);
        $safeDate = htmlspecialchars($date);
        $safeTime = htmlspecialchars($time);
        $safeReason = htmlspecialchars($reason);

        $mail->Body = "
            <h2>Ihr Termin wurde bestätigt</h2>
            <p>Guten Tag {$safeName}</p>
            <p>Ihr Termin wurde erfolgreich gebucht.</p>
            <p><strong>Datum:</strong> {$safeDate}</p>
            <p><strong>Uhrzeit:</strong> {$safeTime}</p>
            <p><strong>Grund:</strong> {$safeReason}</p>
            <p>Freundliche Grüsse<br>Praxis Dr. Müller</p>
        ";

        $mail->AltBody = "Ihr Termin wurde bestätigt.\nDatum: {$date}\nUhrzeit: {$time}\nGrund: {$reason}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail konnte nicht gesendet werden: " . $mail->ErrorInfo);
        return false;
    }
}
