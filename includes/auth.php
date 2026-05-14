<?php

function requirePatient($next = "")
{
    if (!isset($_SESSION["patient_id"])) {
        $redirect = "login.php";

        if ($next !== "") {
            $redirect .= "?next=" . urlencode($next);
        }

        header("Location: " . $redirect);
        exit;
    }
}

function requireAdmin()
{
    if (!isset($_SESSION["admin_logged_in"])) {
        header("Location: admin_login.php");
        exit;
    }
}

function currentPatientId()
{
    return (int)($_SESSION["patient_id"] ?? 0);
}
