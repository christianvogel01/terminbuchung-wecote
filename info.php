<?php
session_start();

$isLoggedIn = isset($_SESSION["patient_id"]);
$patientName = $isLoggedIn
    ? $_SESSION["patient_first_name"] . " " . $_SESSION["patient_last_name"]
    : "";
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>So funktioniert die Buchung – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>So funktioniert die Buchung</p>
      </div>

      <nav class="header-actions">
        <a class="header-link" href="index.php">Startseite</a>
        <?php if ($isLoggedIn): ?>
          <a class="header-button" href="booking.php">Termin buchen</a>
          <a class="header-link" href="logout.php">Logout</a>
        <?php else: ?>
          <a class="header-link" href="login.php">Einloggen</a>
          <a class="header-button" href="register.php">Registrieren</a>
        <?php endif; ?>
      </nav>
    </header>

    <main class="container narrow">
      <section class="card info-simple">
        <h2>In 3 Schritten zum Termin</h2>
        <p class="intro-text">
          Die Online-Terminbuchung führt Sie Schritt für Schritt durch den Prozess.
        </p>

        <div class="simple-steps">
          <div>
            <span>1</span>
            <h3>Registrieren oder einloggen</h3>
            <p>Damit Ihre Buchung eindeutig zugeordnet werden kann.</p>
          </div>

          <div>
            <span>2</span>
            <h3>Datum und Uhrzeit wählen</h3>
            <p>Freie Termine sind auswählbar. Belegte Zeiten sind gesperrt.</p>
          </div>

          <div>
            <span>3</span>
            <h3>Termin bestätigen</h3>
            <p>Nach der Buchung erhalten Sie sofort eine Bestätigung.</p>
          </div>
        </div>

        <div class="guide-actions">
          <?php if ($isLoggedIn): ?>
            <a class="primary-cta" href="booking.php">Termin buchen</a>
          <?php else: ?>
            <a class="primary-cta" href="register.php">Registrieren</a>
            <a class="secondary-cta" href="login.php">Einloggen</a>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
