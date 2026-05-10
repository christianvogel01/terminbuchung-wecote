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
  <link rel="stylesheet" href="styles.css?v=40">
  <link rel="stylesheet" href="mobile.css?v=20">
  <link rel="stylesheet" href="calendar_fix.css?v=1">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <a class="brand" href="index.php">
        <span class="brand-icon">+</span>
        <span>
          <strong>Praxis Dr. Müller</strong>
          <small>Praxisbereich</small>
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
            <a href="index.php">Zur Startseite</a>
          </div>
        </div>
      </nav>
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
