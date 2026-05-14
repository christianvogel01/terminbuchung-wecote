<?php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function formatTime($time)
{
    return substr((string)$time, 0, 5);
}

function canCancelBooking($date, $time)
{
    $appointmentTimestamp = strtotime($date . " " . $time);

    if ($appointmentTimestamp === false) {
        return false;
    }

    return $appointmentTimestamp > (time() + 86400);
}

function jsonError($status, $message)
{
    http_response_code($status);
    echo json_encode([
        "success" => false,
        "message" => $message
    ]);
    exit;
}

function jsonSuccess($message, $extra = [])
{
    echo json_encode(array_merge([
        "success" => true,
        "message" => $message
    ], $extra));
    exit;
}
