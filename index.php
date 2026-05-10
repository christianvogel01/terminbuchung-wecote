<?php
session_start();

$isLoggedIn = isset($_SESSION["patient_id"]);
$patientName = $isLoggedIn
    ? $_SESSION["patient_first_name"] . " " . $_SESSION["patient_last_name"]
    : "";
$initial = $isLoggedIn ? strtoupper(substr($_SESSION["patient_first_name"], 0, 1)) : "";
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Praxis Dr. Müller – Online-Terminbuchung</title>
  <link rel="stylesheet" href="styles.css?v=30">
  <link rel="stylesheet" href="mobile.css?v=20">
  <link rel="stylesheet" href="calendar_fix.css?v=1">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">
      <span class="brand-icon">+</span>
      <span>
        <strong>Praxis Dr. Müller</strong>
        <small>Allgemeinmedizin</small>
      </span>
    </a>

    <nav class="nav">
      <?php if ($isLoggedIn): ?>
        <div class="profile-menu">
          <div class="profile-button" aria-label="Profilmenü">
            <span class="avatar"><?= htmlspecialchars($initial) ?></span>
            <span><?= htmlspecialchars($patientName) ?></span>
            <span>▾</span>
          </div>

          <div class="profile-dropdown">
            <a href="profile.php">Mein Profil</a>
            <a href="my_bookings.php">Meine Buchungen</a>
            <a href="logout.php">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <div class="profile-menu">
          <div class="profile-button" aria-label="Kontomenü">
            <span class="avatar">+</span>
            <span>Konto</span>
            <span>▾</span>
          </div>

          <div class="profile-dropdown">
            <a href="login.php">Einloggen</a>
            <a href="register.php">Registrieren</a>
          </div>
        </div>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="hero">
      <p class="eyebrow">Online-Terminbuchung</p>
      <h1>Ihr Arzttermin in wenigen Klicks.</h1>
      <p class="hero-text">
        Registrieren, einloggen, freien Termin auswählen und direkt eine Bestätigung erhalten.
      </p>

      <div class="hero-actions">
        <?php if ($isLoggedIn): ?>
          <a class="btn primary" href="booking.php">Termin buchen</a>
          <a class="btn light" href="my_bookings.php">Meine Buchungen</a>
        <?php else: ?>
          <a class="btn primary" href="register.php">Jetzt registrieren</a>
          <a class="btn light" href="login.php">Einloggen</a>
        <?php endif; ?>
      </div>

      <div class="badges">
        <span>✓ nur mit Patientenkonto</span>
        <span>✓ freie Termine sichtbar</span>
        <span>✓ direkte Bestätigung</span>
      </div>
    </section>

    <section class="steps">
      <article>
        <span>1</span>
        <h2>Konto erstellen</h2>
        <p>Einmal registrieren, damit Ihre Buchung eindeutig zugeordnet wird.</p>
      </article>

      <article>
        <span>2</span>
        <h2>Termin wählen</h2>
        <p>Datum und freie Uhrzeit auswählen. Belegte Termine sind gesperrt.</p>
      </article>

      <article>
        <span>3</span>
        <h2>Bestätigung erhalten</h2>
        <p>Nach der Buchung sehen Sie sofort, dass der Termin gespeichert wurde.</p>
      </article>
    </section>

    <footer class="footer-links">
      
      <a href="admin_login.php">Praxisbereich</a>
    </footer>
  </main>
  <script src="auto_logout_on_reload.js?v=1"></script>
</body>
</html>
