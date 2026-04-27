<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit;
}

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
  <title>Praxis-Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Praxis-Dashboard</p>
      </div>

      <nav class="header-actions">
        <a class="header-link" href="index.php">Startseite</a>
        <a class="header-button" href="admin_logout.php">Logout</a>
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
                <td><?= htmlspecialchars($booking["appointment_date"]) ?></td>
                <td><?= htmlspecialchars(substr($booking["appointment_time"], 0, 5)) ?></td>
                <td><?= htmlspecialchars($booking["first_name"] . " " . $booking["last_name"]) ?></td>
                <td><?= htmlspecialchars($booking["email"]) ?></td>
                <td><?= htmlspecialchars($booking["phone"] ?? "") ?></td>
                <td><?= htmlspecialchars($booking["reason"] ?? "") ?></td>
                <td class="action-cell">
                  <a class="small-action" href="edit_booking.php?id=<?= htmlspecialchars($booking["id"]) ?>">Bearbeiten</a>

                  <form method="POST" action="delete_booking.php" onsubmit="return confirm('Termin wirklich stornieren?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">
                    <button class="danger-action" type="submit">Stornieren</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($bookings) === 0): ?>
              <tr>
                <td colspan="7">Noch keine Termine vorhanden.</td>
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
                <td><?= htmlspecialchars($patient["id"]) ?></td>
                <td><?= htmlspecialchars($patient["first_name"] . " " . $patient["last_name"]) ?></td>
                <td><?= htmlspecialchars($patient["email"]) ?></td>
                <td><?= htmlspecialchars($patient["phone"]) ?></td>
                <td><?= htmlspecialchars($patient["city"]) ?></td>
                <td><?= htmlspecialchars($patient["created_at"]) ?></td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($patients) === 0): ?>
              <tr>
                <td colspan="6">Noch keine Patienten registriert.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
