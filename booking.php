<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php?next=booking");
    exit;
}

$patientName = $_SESSION["patient_first_name"] . " " . $_SESSION["patient_last_name"];
$initial = strtoupper(substr($_SESSION["patient_first_name"], 0, 1));
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Termin buchen – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=40">
  <link rel="stylesheet" href="mobile.css?v=301">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">
      <span class="brand-icon">+</span>
      <span>
        <strong>Praxis Dr. Müller</strong>
        <small>Terminbuchung</small>
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
          <a href="profile.php">Mein Profil</a>
          <a href="my_bookings.php">Meine Buchungen</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="container">
    <div class="notice success-notice">
      Sie sind angemeldet. Die Buchung wird Ihrem Patientenkonto zugeordnet.
    </div>

    <section class="card">
      <h2>1. Datum auswählen</h2>

      <div class="date-box">
        <label for="dateInput">Wählen Sie ein Datum für Ihren Arzttermin</label>
        <input type="date" id="dateInput">
      </div>
    </section>

    <section class="card">
      <h2>2. Verfügbare Zeiten</h2>

      <div class="section-date" id="selectedDateLabel">
        Bitte wählen Sie zuerst ein Datum aus.
      </div>

      <div id="slots">
        <p class="hint">Noch kein Datum ausgewählt.</p>
      </div>
    </section>

    <section id="bookingForm" class="card booking-form hidden">
      <h2>3. Termin bestätigen</h2>

      <div class="selected-info" id="selectedInfo"></div>

      <form id="patientForm">
        <p class="intro-text">
          Die Buchung wird für <strong><?= htmlspecialchars($patientName) ?></strong> gespeichert.
        </p>

        <label>
          Grund des Termins
          <textarea name="reason" rows="3" placeholder="z.B. Kontrolle, Beschwerden, Beratung"></textarea>
        </label>

        <footer class="footer-action">
          <button class="book-btn" type="submit">Termin verbindlich buchen</button>
          <button class="cancel-btn" type="button" id="cancelBtn">Abbrechen</button>
        </footer>
      </form>
    </section>

    <div id="message" class="message"></div>
  </main>

  <script src="script.js?v=40"></script>
  <script src="auto_logout_on_reload.js?v=1"></script>
</body>
</html>
