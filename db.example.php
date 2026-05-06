<?php
$host = "localhost";
$dbname = "terminbuchung";
$username = "terminuser";
$password = "HIER_PASSWORT_EINTRAGEN";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen.");
}
