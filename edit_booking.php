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
    $phone = trim($_POST["phone"] ?? "");

    if (!$date || !$time) {
        $message = "Datum und Uhrzeit sind Pflichtfelder.";
        $messageType = "error";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE bookings
                SET appointment_date = :date,
                    appointment_time = :time,
                    reason = :reason,
                    phone = :phone
                WHERE id = :id
            ");

            $stmt->execute([
                ":date" => $date,
                ":time" => $time,
                ":reason" => $reason,
                ":phone" => $phone,
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
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Termin bearbeiten – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Termin bearbeiten</p>
      </div>

      <nav class="header-actions">
        <a class="header-link" href="admin.php">Zurück zum Dashboard</a>
      </nav>
    </header>

    <main class="container narrow">
      <section class="card">
        <h2>Termin bearbeiten</h2>

        <?php if ($message): ?>
          <div class="message <?= htmlspecialchars($messageType) ?>" style="display:block;">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="edit_booking.php">
          <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">

          <label>
            Datum
            <input type="date" name="appointment_date" value="<?= htmlspecialchars($booking["appointment_date"]) ?>" required>
          </label>

          <label>
            Uhrzeit
            <input type="time" name="appointment_time" value="<?= htmlspecialchars(substr($booking["appointment_time"], 0, 5)) ?>" required>
          </label>

          <label>
            Telefon
            <input type="tel" name="phone" value="<?= htmlspecialchars($booking["phone"] ?? "") ?>">
          </label>

          <label>
            Grund
            <textarea name="reason" rows="3"><?= htmlspecialchars($booking["reason"] ?? "") ?></textarea>
          </label>

          <button class="book-btn" type="submit">Änderungen speichern</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
