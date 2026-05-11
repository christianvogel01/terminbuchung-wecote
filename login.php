<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "";

$next = $_GET["next"] ?? "";

if (isset($_GET["registered"])) {
    $message = "Registrierung erfolgreich. Bitte melden Sie sich an.";
    $messageType = "success";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $next = $_POST["next"] ?? "";

    if (!$email || !$password) {
        $message = "Bitte geben Sie E-Mail und Passwort ein.";
        $messageType = "error";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && password_verify($password, $patient["password_hash"])) {
            $_SESSION["patient_id"] = $patient["id"];
            $_SESSION["patient_first_name"] = $patient["first_name"];
            $_SESSION["patient_last_name"] = $patient["last_name"];
            $_SESSION["patient_email"] = $patient["email"];

            if ($next === "booking") {
                header("Location: booking.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $message = "Login fehlgeschlagen. Bitte prüfen Sie E-Mail und Passwort.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=31">
  <link rel="stylesheet" href="mobile.css?v=301">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <a class="brand" href="index.php">
        <span class="brand-icon">+</span>
        <span>
          <strong>Praxis Dr. Müller</strong>
          <small>Patientenlogin</small>
        </span>
      </a>

      <nav class="nav">
        <div class="profile-menu">
          <div class="profile-button" aria-label="Kontomenü">
            <span class="avatar">+</span>
            <span>Konto</span>
            <span>▾</span>
          </div>

          <div class="profile-dropdown">
            <a href="register.php">Registrieren</a>
          </div>
        </div>
      </nav>
    </header>

    <main class="container narrow">
      <section class="card">
        <h2>Einloggen</h2>
        <p class="intro-text">Melden Sie sich an, um einen Termin buchen zu können.</p>

        <?php if ($message): ?>
          <div class="message <?= htmlspecialchars($messageType) ?>" style="display:block;">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
          <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

          <label>
            E-Mail
            <input type="email" name="email" required>
          </label>

          <label>
            Passwort
            <input type="password" name="password" required>
          </label>

          <button class="book-btn" type="submit">Einloggen</button>
        </form>

        <p class="center-note">
          Noch kein Konto? <a href="register.php">Jetzt registrieren</a>
        </p>
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
