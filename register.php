<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/validation.php";
require_once __DIR__ . "/includes/csrf.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    requireCsrfToken();

    $firstName = validateRequiredText($_POST["first_name"] ?? "", 100);
    $lastName = validateRequiredText($_POST["last_name"] ?? "", 100);
    $birthdate = validateBirthdate($_POST["birthdate"] ?? "");
    $phone = validatePhone($_POST["phone"] ?? "");
    $street = validateRequiredText($_POST["street"] ?? "", 150);
    $postalCode = validatePostalCode($_POST["postal_code"] ?? "");
    $city = validateRequiredText($_POST["city"] ?? "", 100);
    $insuranceNumber = validateRequiredText($_POST["insurance_number"] ?? "", 100);
    $email = validateEmailAddress($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $passwordConfirm = $_POST["password_confirm"] ?? "";

    if (
        $firstName === false ||
        $lastName === false ||
        $birthdate === false ||
        $phone === false ||
        $street === false ||
        $postalCode === false ||
        $city === false ||
        $insuranceNumber === false ||
        $email === false ||
        !$password ||
        !$passwordConfirm
    ) {
        $message = "Bitte prüfen Sie die eingegebenen Daten.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Das Passwort muss mindestens 6 Zeichen lang sein.";
        $messageType = "error";
    } elseif ($password !== $passwordConfirm) {
        $message = "Die Passwörter stimmen nicht überein.";
        $messageType = "error";
    } else {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO patients
                (first_name, last_name, birthdate, phone, street, postal_code, city, insurance_number, email, password_hash)
                VALUES
                (:first_name, :last_name, :birthdate, :phone, :street, :postal_code, :city, :insurance_number, :email, :password_hash)
            ");

            $stmt->execute([
                ":first_name" => $firstName,
                ":last_name" => $lastName,
                ":birthdate" => $birthdate,
                ":phone" => $phone,
                ":street" => $street,
                ":postal_code" => $postalCode,
                ":city" => $city,
                ":insurance_number" => $insuranceNumber,
                ":email" => $email,
                ":password_hash" => $passwordHash
            ]);

            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Diese E-Mail-Adresse ist bereits registriert.";
                $messageType = "error";
            } else {
                error_log("register Fehler: " . $e->getMessage());
                $message = "Die Registrierung konnte nicht gespeichert werden.";
                $messageType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrierung – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=40">
  <link rel="stylesheet" href="mobile.css?v=301">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <a class="brand" href="index.php">
        <span class="brand-icon">+</span>
        <span>
          <strong>Praxis Dr. Müller</strong>
          <small>Patientenkonto erstellen</small>
        </span>
      </a>

      <nav class="nav">
        <div class="profile-menu">
          <div class="profile-button" aria-label="Kontomenü">
            <span class="avatar">+</span>
            <span>Konto</span>
            <span>▾</span>
          </div>

          <div class="profile-dropdown">
            <a href="login.php">Einloggen</a>
          </div>
        </div>
      </nav>
    </header>

    <main class="container narrow">
      <section class="card">
        <h2>Registrierung</h2>
        <p class="intro-text">Erstellen Sie ein Patientenkonto, damit Ihre Daten bei der Terminbuchung verwendet werden können.</p>

        <?php if ($message): ?>
          <div class="message <?= htmlspecialchars($messageType) ?>" style="display:block;">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
          <?= csrfField() ?>
          <h3>Persönliche Daten</h3>

          <div class="form-grid">
            <label>Vorname * <input type="text" name="first_name" required></label>
            <label>Nachname * <input type="text" name="last_name" required></label>
            <label>Geburtsdatum * <input type="date" name="birthdate" required></label>
            <label>Telefonnummer * <input type="tel" name="phone" required></label>
          </div>

          <h3>Adresse</h3>

          <label>Strasse und Hausnummer * <input type="text" name="street" required></label>

          <div class="form-grid">
            <label>Postleitzahl * <input type="text" name="postal_code" required></label>
            <label>Ort * <input type="text" name="city" required></label>
          </div>

          <h3>Versicherung</h3>

          <label>Versicherungsnummer * <input type="text" name="insurance_number" required></label>

          <h3>Login-Daten</h3>

          <label>E-Mail * <input type="email" name="email" required></label>

          <div class="form-grid">
            <label>Passwort * <input type="password" name="password" required></label>
            <label>Passwort bestätigen * <input type="password" name="password_confirm" required></label>
          </div>

          <div class="privacy-box">
            Ihre Daten werden in diesem Prototyp für die Terminverwaltung gespeichert.
          </div>

          <button class="book-btn" type="submit">Registrieren</button>
        </form>

        <p class="center-note">
          Bereits registriert? <a href="login.php">Einloggen</a>
        </p>
      </section>
    </main>
  </div>
<script id="brand-home-script">
document.querySelectorAll(".topbar .title").forEach(function(el) {
  el.style.cursor = "pointer";
  el.addEventListener("click", function() {
    window.location.href = "index.php";
  });
});
</script>
</body>
</html>
