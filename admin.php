<?php
session_start();
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/csrf.php";

requireAdmin();

$stmt = $pdo->query("
    SELECT 
        b.id,
        b.appointment_date,
        b.appointment_time,
        COALESCE(p.first_name, b.first_name) AS first_name,
        COALESCE(p.last_name, b.last_name) AS last_name,
        COALESCE(p.email, b.email) AS email,
        COALESCE(p.phone, b.phone) AS phone,
        b.reason,
        b.created_at,
        p.birthdate,
        p.insurance_number
    FROM bookings b
    LEFT JOIN patients p ON b.patient_id = p.id
    ORDER BY b.appointment_date, b.appointment_time
");

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$patientsStmt = $pdo->query("
    SELECT id, first_name, last_name, email, phone, city, created_at
    FROM patients
    ORDER BY created_at DESC
");

$patients = $patientsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Praxis-Dashboard</title>
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
          <small>Praxis-Dashboard</small>
        </span>
      </a>

      <nav class="nav">
        <div class="profile-menu">
          <div class="profile-button" aria-label="Praxismenü">
            <span class="avatar">P</span>
            <span>Praxis</span>
            <span>▾</span>
          </div>

          <div class="profile-dropdown">
            <a href="admin_calendar.php">Kalenderansicht</a>
            <a href="index.php">Startseite</a>
            <a href="admin_logout.php">Logout</a>
          </div>
        </div>
      </nav>
    </header>

    <main class="container wide">
      <section class="dashboard-summary">
        <div>
          <strong><?= count($bookings) ?></strong>
          <span>Gebuchte Termine</span>
        </div>

        <div>
          <strong><?= count($patients) ?></strong>
          <span>Registrierte Patienten</span>
        </div>
      </section>

      <section class="card">
        <h2>Gebuchte Termine</h2>

        <table>
          <thead>
            <tr>
              <th>Datum</th>
              <th>Zeit</th>
              <th>Name</th>
              <th>E-Mail</th>
              <th>Telefon</th>
              <th>Grund</th>
              <th>Aktionen</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $booking): ?>
              <tr>
                <td data-label="Datum"><?= htmlspecialchars($booking["appointment_date"]) ?></td>
                <td data-label="Zeit"><?= htmlspecialchars(substr($booking["appointment_time"], 0, 5)) ?></td>
                <td data-label="Name"><?= htmlspecialchars($booking["first_name"] . " " . $booking["last_name"]) ?></td>
                <td data-label="E-Mail"><?= htmlspecialchars($booking["email"]) ?></td>
                <td data-label="Telefon"><?= htmlspecialchars($booking["phone"] ?? "") ?></td>
                <td data-label="Grund"><?= htmlspecialchars($booking["reason"] ?? "") ?></td>
                <td data-label="Aktionen" class="action-cell">
                  <a class="small-action" href="edit_booking.php?id=<?= htmlspecialchars($booking["id"]) ?>">Bearbeiten</a>

                  <form method="POST" action="delete_booking.php" onsubmit="return confirm('Termin wirklich stornieren?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">
                    <?= csrfField() ?>
                    <button class="danger-action" type="submit">Stornieren</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($bookings) === 0): ?>
              <tr>
                <td data-label="Datum" colspan="7">Noch keine Termine vorhanden.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <section class="card">
        <h2>Registrierte Patienten</h2>

        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>E-Mail</th>
              <th>Telefon</th>
              <th>Ort</th>
              <th>Registriert am</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($patients as $patient): ?>
              <tr>
                <td data-label="Zeit"><?= htmlspecialchars($patient["id"]) ?></td>
                <td data-label="Name"><?= htmlspecialchars($patient["first_name"] . " " . $patient["last_name"]) ?></td>
                <td data-label="E-Mail"><?= htmlspecialchars($patient["email"]) ?></td>
                <td data-label="Telefon"><?= htmlspecialchars($patient["phone"]) ?></td>
                <td data-label="Grund"><?= htmlspecialchars($patient["city"]) ?></td>
                <td data-label="Aktionen"><?= htmlspecialchars($patient["created_at"]) ?></td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($patients) === 0): ?>
              <tr>
                <td data-label="Datum" colspan="6">Noch keine Patienten registriert.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
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
