# Terminbuchung Webapplikation – Praxis Dr. Müller

## Kurzbeschreibung

Diese Webapplikation ermöglicht Patientinnen und Patienten eine Online-Terminbuchung für eine Arztpraxis. Die Anwendung läuft auf einer VM mit Apache, PHP und MariaDB.

## Ziel der Anwendung

Die Anwendung vereinfacht den Terminbuchungsprozess. Patientinnen und Patienten können sich registrieren, einloggen, freie Termine auswählen und ihre Buchungen verwalten. Die Praxis erhält ein Dashboard mit Terminübersicht, Kalenderansicht und Verwaltungsfunktionen.

## Hauptfunktionen

### Patientinnen und Patienten

- Registrierung eines Patientenkontos
- Login und Logout
- Terminbuchung mit Anzeige freier und belegter Zeiten
- Übersicht eigener Buchungen
- Stornierung eigener Termine bis 24 Stunden vor Terminbeginn
- Bearbeitung des eigenen Profils

### Praxisbereich

- Praxislogin
- Dashboard mit gebuchten Terminen und registrierten Patienten
- Kalenderansicht mit Wochen-, Monats- und Jahresansicht
- Bearbeiten von bestehenden Terminen
- Stornieren von Terminen

## Technologien

- HTML
- CSS
- JavaScript
- PHP
- MariaDB
- Apache Webserver
- Git und GitHub
- VM Deployment

## Architektur

Browser → HTML/CSS/JavaScript → PHP Backend → MariaDB Datenbank

## Wichtige Dateien

- index.php: Startseite
- register.php: Registrierung
- login.php: Patientenlogin
- booking.php: Terminbuchung
- my_bookings.php: Eigene Buchungen
- profile.php: Patientenprofil
- create_booking.php: Termin speichern
- get_slots.php: Freie und belegte Termine laden
- cancel_my_booking.php: Eigene Buchung stornieren
- admin_login.php: Praxislogin
- admin.php: Praxis-Dashboard
- admin_calendar.php: Kalenderansicht
- edit_booking.php: Termin bearbeiten
- delete_booking.php: Termin stornieren
- styles.css: Styling
- script.js: Terminbuchung im Frontend
- edit_booking_slots.js: Zeitwahl beim Bearbeiten
- auto_logout.php: Session beenden
- auto_logout_on_reload.js: Logout bei Seitenaktualisierung
- db.example.php: Beispiel für Datenbankverbindung

## Datenbank

Die Anwendung verwendet die Datenbank `terminbuchung`.

Wichtige Tabellen:

- patients: registrierte Patientinnen und Patienten
- bookings: gebuchte Termine

## Sicherheit

Die Datei `db.php` enthält echte Zugangsdaten und wird nicht im Git-Repository gespeichert. Stattdessen gibt es `db.example.php` als Vorlage.

Termine können nur von eingeloggten Patientinnen und Patienten erstellt werden. Die Praxis kann Termine einsehen, bearbeiten und stornieren.

## Deployment

Die Anwendung läuft auf der VM im Apache-Webserver-Verzeichnis:

/var/www/html/terminbuchung

## Git Workflow

Änderungen werden mit Git versioniert und über GitHub synchronisiert.

Lokal oder auf der VM:

git status
git add .
git commit -m "Beschreibung der Änderung"
git push

Auf der VM aktualisieren:

cd /var/www/html/terminbuchung
git pull

## Hinweis

Für eine produktive Version wären zusätzliche Massnahmen sinnvoll, zum Beispiel E-Mail-Bestätigungen, ein zeitbasierter Session-Timeout und eine stärkere Absicherung des Praxislogins.
Branch protection test Wed May 13 18:02:04 CEST 2026
