<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    if ($password === "praxis123") {
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin.php");
        exit;
    } else {
        $message = "Falsches Passwort.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Praxis-Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="title">
        <h1>Praxis Dr. Müller</h1>
        <p>Praxisbereich</p>
      </div>
      <a class="header-link" href="index.php">Zur Startseite</a>
    </header>

    <main class="container narrow">
      <section class="card">
        <h2>Praxis-Login</h2>
        <p class="intro-text">Dieser Bereich ist für die Arztpraxis vorgesehen.</p>

        <?php if ($message): ?>
          <div class="message error" style="display:block;"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
          <label>Passwort
            <input type="password" name="password" required>
          </label>

          <button class="book-btn" type="submit">Einloggen</button>
        </form>

        <p class="center-note">Demo-Passwort: <strong>praxis123</strong></p>
      </section>
    </main>
  </div>
</body>
</html>
