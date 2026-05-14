<?php

function loadEnvFile()
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;
    $envFile = __DIR__ . "/../.env";

    if (!file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === "" || strpos($line, "#") === 0 || strpos($line, "=") === false) {
            continue;
        }

        list($key, $value) = explode("=", $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (
            strlen($value) >= 2 &&
            (
                ($value[0] === '"' && substr($value, -1) === '"') ||
                ($value[0] === "'" && substr($value, -1) === "'")
            )
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        putenv($key . "=" . $value);
    }
}

function envValue($key, $default = "")
{
    loadEnvFile();

    $value = getenv($key);

    if ($value === false) {
        return $_ENV[$key] ?? $default;
    }

    return $value;
}
