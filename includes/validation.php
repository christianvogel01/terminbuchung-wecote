<?php

function allowedAppointmentTimes()
{
    return [
        "08:00", "08:30", "09:00", "09:30",
        "10:00", "10:30", "11:00", "11:30",
        "14:00", "14:30", "15:00", "15:30",
        "16:00", "16:30", "17:00", "17:30",
    ];
}

function sanitizeText($input, $maxLength = 500)
{
    $value = trim(strip_tags((string)$input));

    if (function_exists("mb_substr")) {
        return mb_substr($value, 0, $maxLength);
    }

    return substr($value, 0, $maxLength);
}

function validateRequiredText($input, $maxLength = 255)
{
    $value = sanitizeText($input, $maxLength);

    if ($value === "") {
        return false;
    }

    return $value;
}

function validateEmailAddress($email)
{
    $email = trim((string)$email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    return $email;
}

function isValidDateFormat($date)
{
    $d = DateTime::createFromFormat("Y-m-d", $date);
    return $d && $d->format("Y-m-d") === $date;
}

function validateAppointmentDate($date)
{
    $date = trim((string)$date);

    if (!isValidDateFormat($date)) {
        return false;
    }

    $appointmentDate = new DateTime($date);
    $today = new DateTime("today");

    if ($appointmentDate < $today) {
        return false;
    }

    $weekday = (int)$appointmentDate->format("N");

    if ($weekday < 1 || $weekday > 5) {
        return false;
    }

    return $date;
}

function validateBirthdate($date)
{
    $date = trim((string)$date);

    if (!isValidDateFormat($date)) {
        return false;
    }

    $birthdate = new DateTime($date);
    $today = new DateTime("today");

    if ($birthdate >= $today) {
        return false;
    }

    return $date;
}

function normalizeAppointmentTime($time)
{
    $time = trim((string)$time);

    if (preg_match("/^\d{2}:\d{2}:\d{2}$/", $time)) {
        $time = substr($time, 0, 5);
    }

    if (!preg_match("/^\d{2}:\d{2}$/", $time)) {
        return false;
    }

    return $time;
}

function validateAppointmentTime($time)
{
    $time = normalizeAppointmentTime($time);

    if ($time === false) {
        return false;
    }

    if (!in_array($time, allowedAppointmentTimes(), true)) {
        return false;
    }

    return $time;
}

function validatePhone($phone)
{
    $phone = trim((string)$phone);

    if ($phone === "" || strlen($phone) > 40) {
        return false;
    }

    return $phone;
}

function validatePostalCode($postalCode)
{
    $postalCode = trim((string)$postalCode);

    if ($postalCode === "" || strlen($postalCode) > 20) {
        return false;
    }

    return $postalCode;
}
