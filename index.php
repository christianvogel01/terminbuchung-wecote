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
  <title>Praxis Dr. Müller – Online-Terminbuchung</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Allgemeinmedizin</p>
      </div>

      <nav class="header-actions">
        <a class="header-link" href="info.php">Ablauf</a>
        <?php if ($isLoggedIn): ?>
          <span class="user-name"><?= htmlspecialchars($patientName) ?></span>
          <a class="header-button" href="booking.php">Termin buchen</a>
          <a class="header-link" href="logout.php">Logout</a>
        <?php else: ?>
          <a class="header-link" href="login.php">Einloggen</a>
          <a class="header-button" href="register.php">Registrieren</a>
        <?php endif; ?>
      </nav>
    </header>

    <main>
      <section class="landing-hero refined-hero">
        <div class="landing-copy">
          <p class="eyebrow">Online-Terminbuchung</p>
          <h2>Ihr Arzttermin in wenigen Klicks.</h2>
          <p class="landing-text">
            Buchen Sie Ihren Termin bequem online. Registrieren, einloggen, freien Termin auswählen und direkt bestätigen.
          </p>

          <div class="landing-actions">
            <?php if ($isLoggedIn): ?>
              <a class="primary-cta" href="booking.php">Termin buchen</a>
            <?php else: ?>
              <a class="primary-cta" href="register.php">Jetzt registrieren</a>
              <a class="secondary-cta" href="login.php">Einloggen</a>
            <?php endif; ?>
          </div>

          <div class="trust-row">
            <span>✓ freie Termine sichtbar</span>
            <span>✓ nur mit Patientenkonto</span>
            <span>✓ direkte Bestätigung</span>
          </div>
        </div>

        <div class="landing-visual refined-visual">
          <div class="phone-preview">
            <div class="phone-top"></div>
            <div class="preview-title">Terminbuchung</div>
            <div class="preview-date">Heute verfügbare Zeiten</div>
            <div class="preview-slots">
              <span>08:30</span>
              <span class="active">09:00</span>
              <span>10:30</span>
              <span class="disabled">11:00</span>
            </div>
            <div class="preview-confirm">Termin bestätigen</div>
          </div>
        </div>
      </section>

      <section class="quick-flow refined-flow">
        <article class="flow-card">
          <span class="flow-number">1</span>
          <h3>Konto erstellen</h3>
          <p>Einmal registrieren, damit Ihre Buchung eindeutig zugeordnet werden kann.</p>
        </article>

        <article class="flow-card">
          <span class="flow-number">2</span>
          <h3>Termin wählen</h3>
          <p>Freie Zeiten auswählen. Bereits belegte Termine sind automatisch gesperrt.</p>
        </article>

        <article class="flow-card">
          <span class="flow-number">3</span>
          <h3>Bestätigung erhalten</h3>
          <p>Nach der Buchung sehen Sie sofort, dass der Termin gespeichert wurde.</p>
        </article>
      </section>

      <section class="soft-footer">
        <a href="info.php">So funktioniert die Buchung</a>
        <span>·</span>
        <a href="admin_login.php">Praxisbereich</a>
      </section>
    </main>
  </div>
</body>
</html>
