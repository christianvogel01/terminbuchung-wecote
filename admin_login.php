<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    requireCsrfToken();

    $password = $_POST["password"] ?? "";

    $stmt = $pdo->query("
        SELECT id, username, password_hash
        FROM admin_users
        WHERE active = 1
        ORDER BY id
        LIMIT 1
    ");

    $adminUser = $stmt->fetch();

    if ($adminUser && password_verify($password, $adminUser["password_hash"])) {
        session_regenerate_id(true);

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_user_id"] = $adminUser["id"];

        header("Location: admin.php");
        exit;
    }

    $message = "Falsches Passwort.";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Praxis-Login</title>
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
          <?= csrfField() ?>
          <label>Passwort
            <input type="password" name="password" required>
          </label>

          <button class="book-btn" type="submit">Einloggen</button>
        </form>
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
