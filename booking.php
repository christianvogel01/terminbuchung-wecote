<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php?next=booking");
    exit;
}

$patientName = $_SESSION["patient_first_name"] . " " . $_SESSION["patient_last_name"];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Termin buchen – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Terminbuchung</p>
      </div>

      <nav class="header-actions">
        <a class="header-link" href="index.php">Startseite</a>
        <span class="user-name"><?= htmlspecialchars($patientName) ?></span>
        <a class="header-button" href="logout.php">Logout</a>
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
        <div class="availability-header">
          <h2>2. Verfügbare Zeiten</h2>
        </div>

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
  </div>

  <script src="script.js"></script>
</body>
</html>
