<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/validation.php";
require_once __DIR__ . "/includes/csrf.php";

requirePatient();

$patientId = currentPatientId();
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

    if (
        $firstName === false ||
        $lastName === false ||
        $birthdate === false ||
        $phone === false ||
        $street === false ||
        $postalCode === false ||
        $city === false ||
        $insuranceNumber === false ||
        $email === false
    ) {
        $message = "Bitte prüfen Sie die eingegebenen Daten.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE patients
                SET first_name = :first_name,
                    last_name = :last_name,
                    birthdate = :birthdate,
                    phone = :phone,
                    street = :street,
                    postal_code = :postal_code,
                    city = :city,
                    insurance_number = :insurance_number,
                    email = :email
                WHERE id = :id
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
                ":id" => $patientId
            ]);

            $_SESSION["patient_first_name"] = $firstName;
            $_SESSION["patient_last_name"] = $lastName;
            $_SESSION["patient_email"] = $email;

            $message = "Profil erfolgreich aktualisiert.";
            $messageType = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Diese E-Mail-Adresse wird bereits verwendet.";
            } else {
                error_log("profile Fehler: " . $e->getMessage());
                $message = "Profil konnte nicht gespeichert werden.";
            }
            $messageType = "error";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
$stmt->execute([":id" => $patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$patientName = $patient["first_name"] . " " . $patient["last_name"];
$initial = strtoupper(substr($patient["first_name"], 0, 1));
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mein Profil – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=31">
  <link rel="stylesheet" href="mobile.css?v=301">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">
      <span class="brand-icon">+</span>
      <span>
        <strong>Praxis Dr. Müller</strong>
        <small>Mein Profil</small>
      </span>
    </a>

    <nav class="nav">
      <div class="profile-menu">
        <div class="profile-button" aria-label="Profilmenü">
          <span class="avatar"><?= htmlspecialchars($initial) ?></span>
          <span><?= htmlspecialchars($patientName) ?></span>
          <span>▾</span>
        </div>

        <div class="profile-dropdown">
          <a href="my_bookings.php">Meine Buchungen</a>
          <a href="booking.php">Termin buchen</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="container narrow">
    <section class="card">
      <h2>Mein Profil</h2>
      <p class="intro-text">Hier können Sie Ihre Patientendaten einsehen und bei Bedarf anpassen.</p>

      <?php if ($message): ?>
        <div class="message <?= htmlspecialchars($messageType) ?>" style="display:block;">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="profile.php">
        <?= csrfField() ?>
        <h3>Persönliche Daten</h3>

        <div class="form-grid">
          <label>
            Vorname *
            <input type="text" name="first_name" value="<?= htmlspecialchars($patient["first_name"]) ?>" required>
          </label>

          <label>
            Nachname *
            <input type="text" name="last_name" value="<?= htmlspecialchars($patient["last_name"]) ?>" required>
          </label>

          <label>
            Geburtsdatum *
            <input type="date" name="birthdate" value="<?= htmlspecialchars($patient["birthdate"]) ?>" required>
          </label>

          <label>
            Telefon *
            <input type="tel" name="phone" value="<?= htmlspecialchars($patient["phone"]) ?>" required>
          </label>
        </div>

        <h3>Adresse</h3>

        <label>
          Strasse und Hausnummer *
          <input type="text" name="street" value="<?= htmlspecialchars($patient["street"]) ?>" required>
        </label>

        <div class="form-grid">
          <label>
            Postleitzahl *
            <input type="text" name="postal_code" value="<?= htmlspecialchars($patient["postal_code"]) ?>" required>
          </label>

          <label>
            Ort *
            <input type="text" name="city" value="<?= htmlspecialchars($patient["city"]) ?>" required>
          </label>
        </div>

        <h3>Versicherung und Login</h3>

        <label>
          Versicherungsnummer *
          <input type="text" name="insurance_number" value="<?= htmlspecialchars($patient["insurance_number"]) ?>" required>
        </label>

        <label>
          E-Mail *
          <input type="email" name="email" value="<?= htmlspecialchars($patient["email"]) ?>" required>
        </label>

        <button class="book-btn" type="submit">Profil speichern</button>
      </form>
    </section>
  </main>
  <script src="auto_logout_on_reload.js?v=1"></script>
</body>
</html>
