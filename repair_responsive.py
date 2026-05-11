from pathlib import Path
import re

# -------------------------
# 1. admin_calendar.php reinigen und mobile Wochenansicht ergänzen
# -------------------------

p = Path("admin_calendar.php")
text = p.read_text()

# Alte mobile Agenda entfernen
start = text.find('      <div class="mobile-calendar-agenda">')
end_marker = '      <?php if ($view === "week"): ?>'

if start != -1:
    end = text.find(end_marker, start)
    if end != -1:
        text = text[:start] + text[end:]

# Alte mobile-calendar-board entfernen
text = re.sub(
    r'\s*<\?php if \(\$view === "week" \|\| \$view === "month"\): \?>\s*<div class="mobile-calendar-board">.*?</div>\s*<\?php endif; \?>',
    '\n',
    text,
    flags=re.DOTALL
)

# Alte calendar-scroll Wrapper entfernen, falls vorhanden
text = text.replace('      <div class="calendar-scroll">\n', '')
text = text.replace('      </div>\n    </section>\n  </main>', '    </section>\n  </main>')

mobile_week = r'''      <?php if ($view === "week"): ?>
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

'''

if "mobile-week-calendar" not in text:
    marker = '      <?php if ($view === "week"): ?>'
    if marker not in text:
        raise SystemExit("Marker für Wochenansicht nicht gefunden.")
    text = text.replace(marker, mobile_week + marker, 1)

p.write_text(text)


# -------------------------
# 2. mobile.css sauber neu schreiben
# -------------------------

mobile_css = r'''
/* Responsive Design
   Desktop: styles.css
   Tablet: Tabellen bleiben Tabellen
   Handy: Tabellen werden Karten
   Mobile Kalender Woche: eigene Ansicht mit fixer Zeitleiste
*/

*,
*::before,
*::after {
  box-sizing: border-box;
}

.mobile-week-calendar {
  display: none;
}

/* Tablet / iPad */
@media (min-width: 761px) and (max-width: 1024px) {
  html,
  body {
    overflow-x: hidden;
    background: #eef3f9;
  }

  .topbar {
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 22px 28px !important;
  }

  .nav,
  .profile-menu,
  .profile-button {
    width: auto !important;
  }

  .nav {
    margin-left: auto !important;
  }

  .container,
  .container.wide,
  .container.narrow {
    width: 100% !important;
    max-width: 100% !important;
    padding: 34px 26px !important;
  }

  .dashboard-summary {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 18px !important;
    width: 100% !important;
    margin-bottom: 24px !important;
  }

  .card {
    width: 100% !important;
    padding: 24px !important;
    overflow-x: auto !important;
  }

  .card table {
    display: table !important;
    width: 100% !important;
    min-width: 760px !important;
    border-collapse: collapse !important;
  }

  .card thead {
    display: table-header-group !important;
  }

  .card tbody {
    display: table-row-group !important;
  }

  .card tr {
    display: table-row !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
  }

  .card th,
  .card td {
    display: table-cell !important;
    width: auto !important;
    padding: 12px 10px !important;
    font-size: 13px !important;
    line-height: 1.3 !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #eef0f3 !important;
    word-break: normal !important;
    overflow-wrap: normal !important;
  }

  .card td::before {
    content: none !important;
    display: none !important;
  }

  .mobile-actions-row {
    display: flex !important;
    gap: 8px !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
  }

  .mobile-actions-row form {
    margin: 0 !important;
  }

  .small-action,
  .danger-action,
  .mobile-actions-row a,
  .mobile-actions-row button {
    white-space: nowrap !important;
    font-size: 12px !important;
    padding: 8px 11px !important;
    border-radius: 10px !important;
  }
}

/* Handy */
@media (max-width: 760px) {
  html,
  body {
    min-height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    background: #eef3f9;
  }

  .topbar {
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 14px 16px !important;
    gap: 12px !important;
  }

  .brand {
    min-width: 0 !important;
    gap: 10px !important;
  }

  .brand-icon {
    width: 42px !important;
    height: 42px !important;
    min-width: 42px !important;
    border-radius: 12px !important;
  }

  .brand strong {
    font-size: 18px !important;
    line-height: 1.05 !important;
    white-space: nowrap !important;
  }

  .brand small {
    font-size: 12px !important;
  }

  .nav {
    width: auto !important;
    margin-left: auto !important;
  }

  .profile-button {
    padding: 7px 10px !important;
    border-radius: 14px !important;
  }

  .avatar,
  .profile-button .avatar {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    font-size: 14px !important;
  }

  .profile-button span:not(.avatar):not(:last-child) {
    display: none !important;
  }

  .profile-dropdown {
    right: 0 !important;
    left: auto !important;
    min-width: 220px !important;
  }

  .container,
  .container.wide,
  .container.narrow {
    width: 100% !important;
    max-width: 100% !important;
    padding: 18px 14px !important;
  }

  .card {
    width: 100% !important;
    padding: 22px 16px !important;
    border-radius: 22px !important;
    overflow: hidden !important;
  }

  .card h2 {
    font-size: 24px !important;
    line-height: 1.2 !important;
  }

  .intro-text,
  .card p {
    font-size: 15px !important;
    line-height: 1.5 !important;
  }

  /* Tabellen auf Handy als Karten */
  .card table,
  .card thead,
  .card tbody,
  .card tr,
  .card th,
  .card td {
    display: block !important;
    width: 100% !important;
  }

  .card table {
    min-width: 0 !important;
  }

  .card thead {
    display: none !important;
  }

  .card tbody {
    display: grid !important;
    gap: 14px !important;
  }

  .card tr {
    background: #ffffff !important;
    border: 1px solid #dde3eb !important;
    border-radius: 18px !important;
    padding: 16px !important;
    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06) !important;
  }

  .card td {
    border: 0 !important;
    padding: 8px 0 !important;
    display: grid !important;
    grid-template-columns: minmax(90px, 34%) minmax(0, 1fr) !important;
    gap: 10px !important;
    font-size: 15px !important;
    line-height: 1.35 !important;
    min-width: 0 !important;
    overflow-wrap: anywhere !important;
    word-break: break-word !important;
    white-space: normal !important;
  }

  .card td::before {
    content: attr(data-label);
    font-weight: 900 !important;
    color: #5b6475 !important;
  }

  .card td[data-label="Aktion"],
  .card td[data-label="Aktionen"] {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    border-top: 1px solid #eef0f3 !important;
    margin-top: 12px !important;
    padding-top: 14px !important;
  }

  .card td[data-label="Aktion"]::before,
  .card td[data-label="Aktionen"]::before {
    display: none !important;
    content: none !important;
  }

  .card td[data-label="Aktion"] form,
  .card td[data-label="Aktionen"] form {
    margin: 0 !important;
    display: inline-flex !important;
  }

  .small-action,
  .danger-action,
  .card td[data-label="Aktion"] a,
  .card td[data-label="Aktion"] button,
  .card td[data-label="Aktionen"] a,
  .card td[data-label="Aktionen"] button {
    width: auto !important;
    min-height: 42px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 10px 14px !important;
    border-radius: 13px !important;
    font-size: 14px !important;
    font-weight: 900 !important;
    white-space: nowrap !important;
  }

  .table-action,
  .footer-action {
    display: flex !important;
    justify-content: center !important;
    margin-top: 24px !important;
  }

  .table-action .btn,
  .book-btn,
  .btn.primary {
    width: 100% !important;
    max-width: 320px !important;
    min-height: 52px !important;
    justify-content: center !important;
  }

  /* Startseite */
  .hero {
    padding: 46px 18px 34px !important;
    text-align: left !important;
    min-height: auto !important;
  }

  .hero .eyebrow,
  .eyebrow {
    text-align: left !important;
    font-size: 13px !important;
  }

  .hero h1 {
    font-size: 42px !important;
    line-height: 0.98 !important;
    text-align: left !important;
    margin-top: 16px !important;
  }

  .hero-text {
    font-size: 19px !important;
    line-height: 1.35 !important;
    text-align: left !important;
  }

  .hero-actions {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }

  .hero-actions .btn {
    width: 100% !important;
    max-width: none !important;
  }

  .badges {
    display: none !important;
  }

  .steps {
    grid-template-columns: 1fr !important;
    padding: 0 16px 34px !important;
    gap: 14px !important;
  }

  .steps article {
    padding: 20px !important;
    border-radius: 18px !important;
  }

  /* Kalender: Desktop-Wochenraster auf Handy ausblenden */
  .outlook-calendar {
    display: none !important;
  }

  .mobile-week-calendar {
    display: block !important;
    border-top: 1px solid #e5e7eb;
  }

  .calendar-toolbar {
    padding: 20px 16px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 14px !important;
  }

  .calendar-toolbar h2 {
    font-size: 30px !important;
    line-height: 1.05 !important;
    margin: 0 !important;
  }

  .calendar-toolbar p {
    font-size: 15px !important;
    line-height: 1.35 !important;
  }

  .calendar-view-switch {
    width: 100% !important;
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
  }

  .calendar-nav {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 8px !important;
    width: 100% !important;
  }

  .calendar-nav .btn {
    min-height: 44px !important;
    font-size: 14px !important;
    padding: 0 8px !important;
    white-space: nowrap !important;
  }

  /* Mobile Wochenkalender: links fixe Uhrzeiten, rechts swipebare Tage */
  .mobile-week-grid {
    display: grid;
    grid-template-columns: 64px minmax(0, 1fr);
    width: 100%;
  }

  .mobile-week-times {
    background: #f8fafc;
    border-right: 1px solid #e5e7eb;
    z-index: 2;
  }

  .mobile-week-time-head {
    height: 76px;
    border-bottom: 1px solid #e5e7eb;
  }

  .mobile-week-time {
    height: 94px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 14px;
    font-size: 14px;
    font-weight: 900;
    color: #5b6475;
    border-bottom: 1px solid #eef0f3;
  }

  .mobile-week-days-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
  }

  .mobile-week-days {
    display: grid;
    grid-template-columns: repeat(5, calc(100vw - 112px));
    width: max-content;
  }

  .mobile-week-day {
    border-right: 1px solid #e5e7eb;
    background: #ffffff;
  }

  .mobile-week-day-head {
    height: 76px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
  }

  .mobile-week-day-head strong {
    font-size: 16px;
    font-weight: 900;
    color: #070b24;
  }

  .mobile-week-day-head span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef4ff;
    color: #173f9f;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 13px;
    font-weight: 900;
  }

  .mobile-week-day-head.is-today {
    background: #eaf1ff;
  }

  .mobile-week-slot {
    height: 94px;
    padding: 8px;
    border-bottom: 1px solid #eef0f3;
    position: relative;
  }

  .mobile-week-event {
    background: #2147ad;
    color: #ffffff;
    border-radius: 13px;
    padding: 9px;
    box-shadow: 0 8px 18px rgba(33, 71, 173, 0.22);
    overflow: hidden;
  }

  .mobile-week-event strong {
    display: block;
    font-size: 12px;
    margin-bottom: 3px;
  }

  .mobile-week-event span {
    display: block;
    font-size: 14px;
    font-weight: 900;
    line-height: 1.2;
  }

  .mobile-week-event em {
    display: block;
    font-style: normal;
    font-size: 12px;
    margin-top: 4px;
  }

  .mobile-week-event-actions {
    display: flex;
    gap: 6px;
    margin-top: 8px;
    flex-wrap: wrap;
  }

  .mobile-week-event-actions form {
    margin: 0;
  }

  .mobile-week-event-actions a,
  .mobile-week-event-actions button {
    border: 0;
    border-radius: 8px;
    padding: 6px 8px;
    font-size: 11px;
    font-weight: 900;
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
  }

  .mobile-week-event-actions a {
    background: rgba(255,255,255,0.18);
    color: #ffffff;
  }

  .mobile-week-event-actions button {
    background: rgba(255,255,255,0.18);
    color: #ffffff;
  }

  /* Monatsansicht bleibt Kalender und zeigt Mo-So */
  .month-calendar {
    display: grid !important;
    grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
    width: 100% !important;
    min-width: 100% !important;
  }

  .month-day-label {
    padding: 9px 2px !important;
    font-size: 12px !important;
    text-align: center !important;
  }

  .month-day {
    min-height: 78px !important;
    padding: 5px !important;
  }

  .month-day-number {
    font-size: 12px !important;
  }

  .month-event {
    padding: 4px 5px !important;
    font-size: 10px !important;
    border-radius: 7px !important;
  }

  .year-calendar {
    grid-template-columns: 1fr !important;
  }
}

@media (max-width: 390px) {
  .hero h1 {
    font-size: 36px !important;
  }

  .hero-text {
    font-size: 17px !important;
  }

  .card td {
    grid-template-columns: 88px minmax(0, 1fr) !important;
    font-size: 14px !important;
  }

  .mobile-week-days {
    grid-template-columns: repeat(5, calc(100vw - 104px));
  }
}
'''

Path("mobile.css").write_text(mobile_css)


# -------------------------
# 3. CSS-Version erhöhen
# -------------------------

for file in Path(".").glob("*.php"):
    content = file.read_text()

    if "mobile.css" not in content:
        content = re.sub(
            r'(<link rel="stylesheet" href="styles\.css[^"]*">)',
            r'\1' + "\n  " + '<link rel="stylesheet" href="mobile.css?v=300">',
            content
        )
    else:
        content = re.sub(r'mobile\.css\?v=\d+', 'mobile.css?v=300', content)

    file.write_text(content)

print("Responsive Repair abgeschlossen.")
