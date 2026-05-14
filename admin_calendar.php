<?php
session_start();
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/csrf.php";

requireAdmin();

$view = $_GET["view"] ?? "week";
$allowedViews = ["week", "month", "year"];

if (!in_array($view, $allowedViews, true)) {
    $view = "week";
}

$offset = isset($_GET["offset"]) ? (int)$_GET["offset"] : 0;

$today = new DateTime();

if ($view === "week") {
    $start = new DateTime("monday this week");
    if ($offset !== 0) {
        $start->modify(($offset * 7) . " days");
    }

    $end = clone $start;
    $end->modify("+4 days");

    $periodTitle = "Woche vom " . $start->format("d.m.Y") . " bis " . $end->format("d.m.Y");
}

if ($view === "month") {
    $start = new DateTime("first day of this month");
    if ($offset !== 0) {
        $start->modify(($offset) . " months");
    }

    $end = clone $start;
    $end->modify("last day of this month");

    $periodTitle = $start->format("m.Y");
}

if ($view === "year") {
    $start = new DateTime("first day of january this year");
    if ($offset !== 0) {
        $start->modify(($offset) . " years");
    }

    $end = clone $start;
    $end->modify("last day of december this year");

    $periodTitle = $start->format("Y");
}

$stmt = $pdo->prepare("
    SELECT 
        b.id,
        b.appointment_date,
        b.appointment_time,
        COALESCE(p.first_name, b.first_name) AS first_name,
        COALESCE(p.last_name, b.last_name) AS last_name,
        COALESCE(p.email, b.email) AS email,
        COALESCE(p.phone, b.phone) AS phone,
        b.reason
    FROM bookings b
    LEFT JOIN patients p ON b.patient_id = p.id
    WHERE b.appointment_date BETWEEN :start_date AND :end_date
    ORDER BY b.appointment_date, b.appointment_time
");

$stmt->execute([
    ":start_date" => $start->format("Y-m-d"),
    ":end_date" => $end->format("Y-m-d")
]);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bookingsByDate = [];
$bookingsByDateTime = [];
$bookingsByMonth = [];

foreach ($bookings as $booking) {
    $date = $booking["appointment_date"];
    $time = substr($booking["appointment_time"], 0, 5);
    $monthKey = substr($date, 0, 7);

    $bookingsByDate[$date][] = $booking;
    $bookingsByDateTime[$date][$time][] = $booking;
    $bookingsByMonth[$monthKey][] = $booking;
}

$times = [
    "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
    "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30"
];

function dayNameGerman(DateTime $date) {
    $days = [
        "Mon" => "Montag",
        "Tue" => "Dienstag",
        "Wed" => "Mittwoch",
        "Thu" => "Donnerstag",
        "Fri" => "Freitag",
        "Sat" => "Samstag",
        "Sun" => "Sonntag"
    ];

    return $days[$date->format("D")] ?? $date->format("D");
}

function monthNameGerman($monthNumber) {
    $months = [
        "01" => "Januar",
        "02" => "Februar",
        "03" => "März",
        "04" => "April",
        "05" => "Mai",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "August",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Dezember"
    ];

    return $months[$monthNumber] ?? $monthNumber;
}

function buildUrl($view, $offset) {
    return "admin_calendar.php?view=" . urlencode($view) . "&offset=" . urlencode((string)$offset);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kalenderansicht – Praxis Dr. Müller</title>
  <link rel="stylesheet" href="styles.css?v=80">
  <link rel="stylesheet" href="mobile.css?v=301">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="admin.php">
      <span class="brand-icon">+</span>
      <span>
        <strong>Praxis Dr. Müller</strong>
        <small>Kalenderansicht</small>
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

  <main class="container wide">
    <section class="calendar-shell">
      <div class="calendar-toolbar">
        <div>
          <h2>Praxis-Kalender</h2>
          <p><?= htmlspecialchars($periodTitle) ?></p>
        </div>

        <div class="calendar-controls">
          <div class="calendar-view-switch">
            <a class="<?= $view === "week" ? "active" : "" ?>" href="admin_calendar.php?view=week&offset=0">Woche</a>
            <a class="<?= $view === "month" ? "active" : "" ?>" href="admin_calendar.php?view=month&offset=0">Monat</a>
            <a class="<?= $view === "year" ? "active" : "" ?>" href="admin_calendar.php?view=year&offset=0">Jahr</a>
          </div>

          <div class="calendar-nav">
            <a class="btn light" href="<?= htmlspecialchars(buildUrl($view, $offset - 1)) ?>">← Zurück</a>
            <a class="btn light" href="<?= htmlspecialchars(buildUrl($view, 0)) ?>">Heute</a>
            <a class="btn light" href="<?= htmlspecialchars(buildUrl($view, $offset + 1)) ?>">Weiter →</a>
          </div>
        </div>
      </div>

      <?php if ($view === "week"): ?>
        <div class="mobile-week-calendar">
          <?php
            $mobileSlots = [
              "08:00", "08:30", "09:00", "09:30",
              "10:00", "10:30", "11:00", "11:30",
              "14:00", "14:30", "15:00", "15:30",
              "16:00", "16:30", "17:00", "17:30"
            ];

            $mobileDays = [];
            $mobileDay = clone $start;

            for ($i = 0; $i < 5; $i++) {
              $mobileDays[] = clone $mobileDay;
              $mobileDay->modify("+1 day");
            }
          ?>

          <div class="mobile-week-grid">
            <div class="mobile-week-times">
              <div class="mobile-week-time-head"></div>

              <?php foreach ($mobileSlots as $slot): ?>
                <div class="mobile-week-time"><?= htmlspecialchars($slot) ?></div>
              <?php endforeach; ?>
            </div>

            <div class="mobile-week-days-scroll">
              <div class="mobile-week-days">
                <?php foreach ($mobileDays as $day): ?>
                  <?php
                    $dateKey = $day->format("Y-m-d");
                    $isToday = $dateKey === (new DateTime())->format("Y-m-d");
                  ?>

                  <div class="mobile-week-day">
                    <div class="mobile-week-day-head <?= $isToday ? "is-today" : "" ?>">
                      <strong><?= htmlspecialchars(dayNameGerman($day)) ?></strong>
                      <span><?= htmlspecialchars($day->format("d.m.")) ?></span>
                    </div>

                    <?php foreach ($mobileSlots as $slot): ?>
                      <div class="mobile-week-slot">
                        <?php foreach ($bookings as $booking): ?>
                          <?php
                            $bookingDate = $booking["appointment_date"] ?? "";
                            $bookingTime = substr((string)($booking["appointment_time"] ?? ""), 0, 5);
                          ?>

                          <?php if ($bookingDate === $dateKey && $bookingTime === $slot): ?>
                            <article class="mobile-week-event">
                              <strong><?= htmlspecialchars($bookingTime) ?></strong>
                              <span><?= htmlspecialchars($booking["first_name"] . " " . $booking["last_name"]) ?></span>
                              <em><?= htmlspecialchars($booking["reason"] ?: "Termin") ?></em>

                              <div class="mobile-week-event-actions">
                                <a href="edit_booking.php?id=<?= htmlspecialchars($booking["id"]) ?>">Bearbeiten</a>

                                <form method="POST" action="delete_booking.php" onsubmit="return confirm('Termin wirklich stornieren?');">
                                  <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">
                                  <?= csrfField() ?>
                                  <button type="submit">Stornieren</button>
                                </form>
                              </div>
                            </article>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($view === "week"): ?>
        <?php
          $weekDays = [];
          for ($i = 0; $i < 5; $i++) {
              $day = clone $start;
              $day->modify("+$i days");
              $weekDays[] = $day;
          }
        ?>

        <div class="outlook-calendar">
          <div class="calendar-corner"></div>

          <?php foreach ($weekDays as $day): ?>
            <?php $isToday = $day->format("Y-m-d") === (new DateTime())->format("Y-m-d"); ?>
            <div class="calendar-day-title <?= $isToday ? "today-week-day" : "" ?>">
              <strong><?= htmlspecialchars(dayNameGerman($day)) ?></strong>
              <span><?= htmlspecialchars($day->format("d.m.")) ?></span>
            </div>
          <?php endforeach; ?>

          <?php foreach ($times as $time): ?>
            <div class="calendar-time"><?= htmlspecialchars($time) ?></div>

            <?php foreach ($weekDays as $day): ?>
              <?php
                $dateKey = $day->format("Y-m-d");
                $slotBookings = $bookingsByDateTime[$dateKey][$time] ?? [];
              ?>

              <div class="calendar-slot">
                <?php foreach ($slotBookings as $booking): ?>
                  <article class="calendar-event">
                    <div class="event-time"><?= htmlspecialchars($time) ?></div>
                    <strong><?= htmlspecialchars($booking["first_name"] . " " . $booking["last_name"]) ?></strong>
                    <span><?= htmlspecialchars($booking["reason"] ?: "Termin") ?></span>

                    <div class="event-actions">
                      <a href="edit_booking.php?id=<?= htmlspecialchars($booking["id"]) ?>">Bearbeiten</a>

                      <form method="POST" action="delete_booking.php" onsubmit="return confirm('Termin wirklich stornieren?');">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($booking["id"]) ?>">
                                  <?= csrfField() ?>
                        <button type="submit">Stornieren</button>
                      </form>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($view === "month"): ?>
        <?php
          $monthStart = clone $start;
          $monthStart->modify("monday this week");

          $monthEnd = clone $end;
          $monthEnd->modify("sunday this week");

          $current = clone $monthStart;
        ?>

        <div class="month-calendar">
          <?php foreach (["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"] as $dayLabel): ?>
            <div class="month-day-label"><?= htmlspecialchars($dayLabel) ?></div>
          <?php endforeach; ?>

          <?php while ($current <= $monthEnd): ?>
            <?php
              $dateKey = $current->format("Y-m-d");
              $isCurrentMonth = $current->format("m") === $start->format("m");
              $isToday = $current->format("Y-m-d") === (new DateTime())->format("Y-m-d");
              $dayBookings = $bookingsByDate[$dateKey] ?? [];
            ?>

            <div class="month-day <?= $isCurrentMonth ? "" : "muted-month-day" ?> <?= $isToday ? "today-month-day" : "" ?>">
              <div class="month-day-number"><?= htmlspecialchars($current->format("d")) ?></div>

              <?php foreach (array_slice($dayBookings, 0, 3) as $booking): ?>
                <a class="month-event" href="edit_booking.php?id=<?= htmlspecialchars($booking["id"]) ?>">
                  <?= htmlspecialchars(substr($booking["appointment_time"], 0, 5)) ?>
                  <?= htmlspecialchars($booking["first_name"] . " " . $booking["last_name"]) ?>
                </a>
              <?php endforeach; ?>

              <?php if (count($dayBookings) > 3): ?>
                <div class="more-events">+<?= count($dayBookings) - 3 ?> weitere</div>
              <?php endif; ?>
            </div>

            <?php $current->modify("+1 day"); ?>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>

      <?php if ($view === "year"): ?>
        <div class="year-calendar">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <?php
              $month = str_pad((string)$m, 2, "0", STR_PAD_LEFT);
              $monthKey = $start->format("Y") . "-" . $month;
              $monthBookings = $bookingsByMonth[$monthKey] ?? [];
            ?>

            <a class="year-month-card" href="admin_calendar.php?view=month&offset=<?= ($m - (int)$today->format("m")) + ($offset * 12) ?>">
              <strong><?= htmlspecialchars(monthNameGerman($month)) ?></strong>
              <span><?= count($monthBookings) ?> Termin(e)</span>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
