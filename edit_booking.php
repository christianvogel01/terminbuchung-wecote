<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET["id"] ?? $_POST["id"] ?? "";

if (!$id) {
    header("Location: admin.php");
    exit;
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["appointment_date"] ?? "";
    $time = $_POST["appointment_time"] ?? "";
    $reason = trim($_POST["reason"] ?? "");

    if (!$date || !$time) {
        $message = "Datum und Uhrzeit sind Pflichtfelder.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE bookings
                SET appointment_date = :date,
                    appointment_time = :time,
                    reason = :reason
                WHERE id = :id
            ");

            $stmt->execute([
                ":date" => $date,
                ":time" => $time,
                ":reason" => $reason,
                ":id" => $id
            ]);

            header("Location: admin.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Dieser Termin ist bereits vergeben.";
                $messageType = "error";
            } else {
                $message = "Der Termin konnte nicht gespeichert werden.";
                $messageType = "error";
            }
        }
    }
}

$stmt = $pdo->prepare("
    SELECT *
    FROM bookings
    WHERE id = :id
");
$stmt->execute([":id" => $id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: admin.php");
    exit;
}

$currentTime = substr($booking["appointment_time"], 0, 5);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Termin bearbeiten – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=100">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="admin.php">
      <span class="brand-icon">+</span>
      <span>
        <strong>Praxis Dr. Müller</strong>
        <small>Termin bearbeiten</small>
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
          <a href="admin.php">Dashboard</a>
          <a href="admin_calendar.php?view=week">Kalenderansicht</a>
          <a href="index.php">Startseite</a>
          <a href="admin_logout.php">Logout</a>
        </div>
      </div>
    </nav>
  </header>

  <main class="container">
    <section class="card">
      <h2>1. Datum auswählen</h2>

      <?php if ($message): ?>
        <div class="message <?= htmlspecialchars($messageType) ?>" style="display:block;">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="edit_booking.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">

        <div class="date-box">
          <label for="editDateInput">Wählen Sie ein Datum für den Arzttermin</label>
          <input
            type="date"
            name="appointment_date"
            id="editDateInput"
            value="<?= htmlspecialchars($booking["appointment_date"]) ?>"
            required
          >
        </div>

        <input
          type="hidden"
          name="appointment_time"
          id="editTimeInput"
          value="<?= htmlspecialchars($currentTime) ?>"
          required
        >

        <section class="edit-time-section">
          <h2>2. Verfügbare Zeiten</h2>

          <div class="section-date" id="editDateLabel"></div>

          <div class="selected-info" id="editSelectedInfo">
            Aktuelle Uhrzeit: <?= htmlspecialchars($currentTime) ?> Uhr
          </div>

          <div id="editSlotHint" class="hint">Verfügbare Zeiten werden geladen...</div>
          <div id="editSlots"></div>
        </section>

        <section class="edit-details-section">
          <h2>3. Angaben bearbeiten</h2>


          <label>
            Grund
            <textarea name="reason" rows="3"><?= htmlspecialchars($booking["reason"] ?? "") ?></textarea>
          </label>

          <button class="book-btn" type="submit">Änderungen speichern</button>
        </section>
      </form>
    </section>
  </main>

  <script>
    window.currentBookingId = <?= json_encode((int)$booking["id"]) ?>;
  </script>
  <script src="edit_booking_slots.js?v=100"></script>
</body>
</html>
