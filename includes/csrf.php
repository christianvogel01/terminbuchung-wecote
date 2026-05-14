<?php

function csrfToken()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(csrfToken(), ENT_QUOTES, "UTF-8") .
        '">';
}

function isValidCsrfToken($token)
{
    if (!is_string($token) || $token === "") {
        return false;
    }

    return hash_equals($_SESSION["csrf_token"] ?? "", $token);
}

function requireCsrfToken()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        return;
    }

    if (!isValidCsrfToken($_POST["csrf_token"] ?? "")) {
        http_response_code(403);
        die("Ungültige Sicherheitsprüfung. Bitte laden Sie die Seite neu.");
    }
}

function requireCsrfTokenJson()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        return;
    }

    if (!isValidCsrfToken($_POST["csrf_token"] ?? "")) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Ungültige Sicherheitsprüfung. Bitte laden Sie die Seite neu."
        ]);
        exit;
    }
}
