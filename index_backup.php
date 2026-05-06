<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Praxis Dr. Müller – Terminbuchung</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Allgemeinmedizin</p>
      </div>

      <div class="user-icon" aria-label="Benutzerkonto">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21a8 8 0 0 0-16 0"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </div>
    </header>

    <main class="container">
      <div class="notice">
       strong>Hinweis:</strong> Um einen Termin zu buchen, müssen Sie sich anmelden oder registrieren.
      </div>

      <section class="card">
        <h2>Datum auswählen</h2>

        <div class="date-box">
          <label for="dateInput">Wählen Sie ein Datum für Ihren Arzttermin</label>
          <input type="date" id="dateInput">
        </div>

 	<a class="user-icon" href="register.php" aria-label="Patientenkonto registrieren" title="Registrieren">
  	<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
  	     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  	  <path d="M20 21a8 8 0 0 0-16 0"></path>
   	 <circle cx="12" cy="7" r="4"></circle>
   	 <line x1="19" y1="8" x2="19" y2="14"></line>
	    <line x1="22" y1="11" x2="16" y2="11"></line>
 	 </svg>
	</a>
     </section>

      <section class="card">
        <div class="availability-header">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#566173"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <h2>Verfügbare Zeiten</h2>
        </div>

        <div class="section-date" id="selectedDateLabel">
          Bitte wählen Sie zuerst ein Datum aus.
        </div>

        <div id="slots">
          <p class="hint">Noch kein Datum ausgewählt.</p>
        </div>
      </section>

      <section id="bookingForm" class="card booking-form hidden">
        <h2>Patientendaten</h2>

        <div class="selected-info" id="selectedInfo"></div>

        <form id="patientForm">
          <div class="form-grid">
            <label>
              Vorname *
              <input type="text" name="first_name" required>
            </label>

            <label>
              Nachname *
              <input type="text" name="last_name" required>
            </label>

            <label>
              E-Mail *
              <input type="email" name="email" required>
            </label>

            <label>
              Telefon
              <input type="tel" name="phone">
            </label>
          </div>

          <label>
            Grund des Termins
            <textarea name="reason" rows="3"></textarea>
          </label>

          <footer class="footer-action">
            <button class="book-btn" type="submit">Termin buchen</button>
            <button class="cancel-btn" type="button" id="cancelBtn">Abbrechen</button>
          </footer>
        </form>
      </section>

      <div id="message" class="message"></div>

      <p class="admin-link">
        <a href="admin.php">Zur Terminübersicht der Praxis</a>
      </p>
    </main>
  </div>

  <script src="script.js"></script>
<script id="brand-home-script">
document.querySelectorAll(".topbar .title").forEach(function(el) {
  el.style.cursor = "pointer";
  el.addEventListener("click", function() {
    window.location.href = "index.php";
  });
});
</script>
</body>
</htm
