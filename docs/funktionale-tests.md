# Funktionale Tests

## 1. Ziel der Tests

Ziel der funktionalen Tests ist es, die wichtigsten Nutzerflüsse der Terminbuchungs-Webapplikation systematisch zu prüfen. Im Fokus steht nicht die interne Implementierung, sondern ob die Anwendung aus Sicht der Benutzerinnen und Benutzer korrekt funktioniert.

Geprüft werden insbesondere:

- Registrierung
- Login und Logout
- Terminbuchung
- Anzeige eigener Buchungen
- Stornierung durch Patientinnen und Patienten
- Praxis-Login
- Verwaltung von Terminen im Adminbereich
- Kalenderansicht
- Mobile Darstellung
- Fehlerfälle und unerlaubte Zugriffe

Die Tests wurden auf der VM durchgeführt:

- URL: http://hosting-060.wecote.ls.eee.intern/terminbuchung/
- Backend: PHP / Apache
- Datenbank: MariaDB / MySQL
- Browser manuell: Chrome / Chromium
- Automatisierte Tests: Playwright

---

## 2. Getestete User Flows

### FLOW-01: Patient registriert sich und bucht einen Termin

Ein Patient öffnet die Startseite, registriert sich mit gültigen Daten, loggt sich ein, bucht einen freien Termin und sieht diesen anschliessend unter "Meine Buchungen".

Dieser Flow ist zentral, weil er den Hauptzweck der Applikation abdeckt: die Online-Terminbuchung für Patientinnen und Patienten.

### FLOW-02: Praxis verwaltet Termine

Eine Praxisperson loggt sich in den Praxisbereich ein, prüft die vorhandenen Buchungen im Dashboard und Kalender, bearbeitet einen Termin und storniert bei Bedarf eine Buchung.

Dieser Flow ist wichtig, weil die Praxis die gebuchten Termine verwalten können muss.

---

## 3. Manuelle funktionale Testfälle

## TEST-01: Registrierung erfolgreich

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ein neuer Patient mit gültigen Daten registriert werden kann. |
| Vorbedingungen | Die verwendete E-Mail-Adresse ist noch nicht registriert. |
| Testdaten | Vorname: Test, Nachname: Patient, E-Mail: test.patient@example.com, Passwort: Test1234 |
| Schritte | 1. Startseite öffnen. 2. "Jetzt registrieren" anklicken. 3. Formular vollständig ausfüllen. 4. Registrierung absenden. |
| Erwartetes Ergebnis | Patient wird erstellt und zur Login-Seite weitergeleitet. Eine Erfolgsmeldung erscheint. |
| Ist-Ergebnis | Registrierung funktioniert wie erwartet. |
| Status | Bestanden |
| Bemerkung | Keine Auffälligkeiten. |

---

## TEST-02: Registrierung mit ungültigen Daten

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ungültige Registrierungsdaten abgefangen werden. |
| Vorbedingungen | Registrierungsseite ist geöffnet. |
| Testdaten | Ungültige E-Mail-Adresse oder nicht übereinstimmende Passwörter. |
| Schritte | 1. Registrierungsformular öffnen. 2. Ungültige E-Mail oder unterschiedliche Passwörter eingeben. 3. Formular absenden. |
| Erwartetes Ergebnis | Registrierung wird nicht gespeichert. Eine verständliche Fehlermeldung erscheint. |
| Ist-Ergebnis | Ungültige Eingaben werden abgelehnt. |
| Status | Bestanden |
| Bemerkung | Validierung funktioniert client- und serverseitig. |

---

## TEST-03: Patient Login erfolgreich

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob sich ein registrierter Patient einloggen kann. |
| Vorbedingungen | Patientenkonto existiert. |
| Testdaten | Gültige E-Mail-Adresse und korrektes Passwort. |
| Schritte | 1. Login-Seite öffnen. 2. E-Mail und Passwort eingeben. 3. Login absenden. |
| Erwartetes Ergebnis | Patient wird eingeloggt und zur Startseite bzw. Buchungsfunktion weitergeleitet. |
| Ist-Ergebnis | Login funktioniert. |
| Status | Bestanden |
| Bemerkung | Session wird korrekt erstellt. |

---

## TEST-04: Patient Login mit falschem Passwort

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob falsche Login-Daten abgewiesen werden. |
| Vorbedingungen | Patientenkonto existiert. |
| Testdaten | Gültige E-Mail-Adresse, falsches Passwort. |
| Schritte | 1. Login-Seite öffnen. 2. E-Mail eingeben. 3. Falsches Passwort eingeben. 4. Login absenden. |
| Erwartetes Ergebnis | Login wird verweigert. Eine Fehlermeldung erscheint. |
| Ist-Ergebnis | Falsche Zugangsdaten werden abgelehnt. |
| Status | Bestanden |
| Bemerkung | Kein Zugriff ohne gültiges Passwort. |

---

## TEST-05: Patient bucht freien Termin

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ein eingeloggter Patient einen freien Termin buchen kann. |
| Vorbedingungen | Patient ist eingeloggt. Der Termin ist noch frei. |
| Testdaten | Datum: nächster Werktag, Uhrzeit: 08:30, Grund: Kontrolltermin |
| Schritte | 1. Terminbuchung öffnen. 2. Datum auswählen. 3. Freie Uhrzeit auswählen. 4. Grund eingeben. 5. Termin buchen. |
| Erwartetes Ergebnis | Termin wird gespeichert. Bestätigung erscheint. Termin ist unter "Meine Buchungen" sichtbar. |
| Ist-Ergebnis | Terminbuchung funktioniert. |
| Status | Bestanden |
| Bemerkung | Daten werden korrekt in der Datenbank gespeichert. |

---

## TEST-06: Doppelbuchung wird verhindert

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ein bereits gebuchter Termin nicht doppelt gebucht werden kann. |
| Vorbedingungen | Ein Termin ist bereits für ein bestimmtes Datum und eine bestimmte Uhrzeit gebucht. |
| Testdaten | Gleiches Datum und gleiche Uhrzeit wie bestehender Termin. |
| Schritte | 1. Terminbuchung öffnen. 2. Datum mit bestehender Buchung auswählen. 3. Prüfen, ob belegte Uhrzeit noch auswählbar ist. 4. Falls möglich, Buchung erneut absenden. |
| Erwartetes Ergebnis | Belegte Uhrzeit ist nicht auswählbar oder Backend lehnt die Buchung ab. |
| Ist-Ergebnis | Doppelbuchung wird verhindert. |
| Status | Bestanden |
| Bemerkung | Die serverseitige Prüfung schützt zusätzlich gegen manipulierte Requests. |

---

## TEST-07: Patient sieht eigene Buchungen

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ein Patient seine eigenen Termine sehen kann. |
| Vorbedingungen | Patient ist eingeloggt und hat mindestens einen Termin gebucht. |
| Testdaten | Bestehende Buchung. |
| Schritte | 1. Einloggen. 2. "Meine Buchungen" öffnen. |
| Erwartetes Ergebnis | Die eigenen Buchungen werden angezeigt. Fremde Buchungen sind nicht sichtbar. |
| Ist-Ergebnis | Eigene Buchungen werden korrekt angezeigt. |
| Status | Bestanden |
| Bemerkung | Die Abfrage ist an die Patient-ID der Session gebunden. |

---

## TEST-08: Patient storniert eigenen Termin

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob ein Patient einen erlaubten Termin stornieren kann. |
| Vorbedingungen | Patient ist eingeloggt. Termin liegt mehr als 24 Stunden in der Zukunft. |
| Testdaten | Bestehende Buchung. |
| Schritte | 1. "Meine Buchungen" öffnen. 2. Stornieren anklicken. 3. Bestätigen. |
| Erwartetes Ergebnis | Termin wird gelöscht und erscheint nicht mehr in der Übersicht. |
| Ist-Ergebnis | Stornierung funktioniert. |
| Status | Bestanden |
| Bemerkung | CSRF-Schutz ist für die Aktion aktiv. |

---

## TEST-09: Admin Login erfolgreich

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob sich die Praxis in den Adminbereich einloggen kann. |
| Vorbedingungen | Admin-User existiert in der Datenbank. |
| Testdaten | Gültiges Praxis-Passwort. |
| Schritte | 1. Praxis-Login öffnen. 2. Passwort eingeben. 3. Login absenden. |
| Erwartetes Ergebnis | Praxis wird eingeloggt und zum Adminbereich weitergeleitet. |
| Ist-Ergebnis | Admin-Login funktioniert. |
| Status | Bestanden |
| Bemerkung | Passwort wird mit password_hash/password_verify geprüft. |

---

## TEST-10: Admin sieht Buchungen im Dashboard und Kalender

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob die Praxis vorhandene Termine sehen kann. |
| Vorbedingungen | Admin ist eingeloggt. Es existieren Buchungen. |
| Testdaten | Bestehende Buchung. |
| Schritte | 1. Adminbereich öffnen. 2. Dashboard prüfen. 3. Kalenderansicht öffnen. |
| Erwartetes Ergebnis | Buchungen werden im Dashboard und Kalender angezeigt. |
| Ist-Ergebnis | Buchungen werden korrekt angezeigt. |
| Status | Bestanden |
| Bemerkung | Monats-, Wochen- und mobile Ansicht wurden visuell geprüft. |

---

## TEST-11: Admin bearbeitet Termin

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob die Praxis einen Termin bearbeiten kann. |
| Vorbedingungen | Admin ist eingeloggt. Termin existiert. |
| Testdaten | Neues Datum oder neue Uhrzeit. |
| Schritte | 1. Adminbereich öffnen. 2. Termin auswählen. 3. Bearbeiten anklicken. 4. Datum/Uhrzeit ändern. 5. Speichern. |
| Erwartetes Ergebnis | Änderung wird gespeichert und im Dashboard/Kalender sichtbar. |
| Ist-Ergebnis | Bearbeitung funktioniert. |
| Status | Bestanden |
| Bemerkung | Serverseitige Datum- und Zeitvalidierung ist aktiv. |

---

## TEST-12: Admin storniert Termin

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob die Praxis einen Termin löschen kann. |
| Vorbedingungen | Admin ist eingeloggt. Termin existiert. |
| Testdaten | Bestehende Buchung. |
| Schritte | 1. Adminbereich öffnen. 2. Termin auswählen. 3. Stornieren anklicken. 4. Bestätigen. |
| Erwartetes Ergebnis | Termin wird gelöscht und ist nicht mehr sichtbar. |
| Ist-Ergebnis | Stornierung funktioniert. |
| Status | Bestanden |
| Bemerkung | CSRF-Schutz ist für die Aktion aktiv. |

---

## TEST-13: Zugriff auf geschützte Seiten ohne Login

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob geschützte Seiten ohne Login nicht erreichbar sind. |
| Vorbedingungen | Benutzer ist ausgeloggt. |
| Testdaten | Direkter Aufruf von booking.php, my_bookings.php und admin.php. |
| Schritte | 1. Ausloggen. 2. Geschützte URLs direkt im Browser öffnen. |
| Erwartetes Ergebnis | Patientenseiten leiten zum Login weiter. Adminseiten leiten zum Praxis-Login weiter. |
| Ist-Ergebnis | Zugriff ohne Login wird verhindert. |
| Status | Bestanden |
| Bemerkung | Auth-Guards requirePatient() und requireAdmin() funktionieren. |

---

## TEST-14: Mobile Ansicht

| Feld | Inhalt |
|---|---|
| Ziel | Prüfen, ob zentrale Seiten auf mobilen Bildschirmgrössen nutzbar sind. |
| Vorbedingungen | Browser-DevTools oder Smartphone-Ansicht wird verwendet. |
| Testdaten | Mobile Bildschirmbreite. |
| Schritte | 1. Startseite mobil öffnen. 2. Login prüfen. 3. Terminbuchung prüfen. 4. Kalenderansicht prüfen. |
| Erwartetes Ergebnis | Inhalte sind lesbar, Buttons bedienbar, Kalender ist nutzbar. |
| Ist-Ergebnis | Mobile Ansicht funktioniert. |
| Status | Bestanden |
| Bemerkung | Die mobile Kalenderansicht wurde zuvor gezielt optimiert. |

---

## 4. Automatisierte Tests mit Playwright

Zusätzlich zu den manuellen Tests wurden automatisierte End-to-End-Smoke- und Validierungstests mit Playwright umgesetzt. Die Tests laufen lokal auf dem Mac und prüfen die auf der VM veröffentlichte Webapplikation unter:

- URL: http://hosting-060.wecote.ls.eee.intern/terminbuchung/
- Testtool: Playwright
- Browser-Projekte: Chromium, Firefox und WebKit

### Zweck

Die automatisierten Tests prüfen, ob zentrale Seiten der Webapplikation erreichbar sind und wichtige Inhalte sowie Validierungsverhalten korrekt funktionieren.

Automatisiert geprüft wurden:

- Startseite lädt korrekt
- Login-Seite lädt korrekt
- Login mit falschem Passwort wird abgelehnt
- Registrierung mit ungültiger E-Mail wird nicht akzeptiert
- Admin-Login-Seite lädt korrekt
- Admin-Login mit falschem Passwort wird abgelehnt

### Ausführung

Die Tests wurden mit folgendem Befehl ausgeführt:

```bash
npx playwright test
```

### Resultat

Im Terminal wurde folgendes Ergebnis ausgegeben:

```text
Running 18 tests using 5 workers
18 passed (6.9s)
```

Zusätzlich wurde die Authentifizierungs- und Validierungs-Testdatei einzeln in Chromium ausgeführt:

```bash
npx playwright test tests/auth-validierung.spec.ts --project=chromium
```

Resultat:

```text
Running 5 tests using 5 workers
5 passed (1.3s)
```

Damit wurden die automatisierten Tests erfolgreich in Chromium, Firefox und WebKit ausgeführt. Die Warnung `DEP0205 DeprecationWarning` stammt aus der lokalen Node-/Playwright-Umgebung und hat die Testergebnisse nicht beeinflusst, da alle Tests erfolgreich abgeschlossen wurden.

### Automatisierungspotenzial

Folgende Tests könnten später zusätzlich automatisiert werden:

- Login mit gültigen Daten
- Registrierung mit gültigen Daten
- Terminbuchung eines freien Slots
- Verhinderung einer Doppelbuchung mit Testdaten
- Anzeige eigener Buchungen
- Stornierung eines eigenen Termins
- Admin-Bearbeitung eines Termins

Diese Tests sind geeignet für Automatisierung, weil sie nach Änderungen regelmässig wiederholt werden können und zentrale Regressionen schnell sichtbar machen. Für vollständige Buchungsflows wäre zusätzlich ein sauberer Umgang mit Testdaten sinnvoll, zum Beispiel eindeutige Test-E-Mail-Adressen und ein Cleanup-Skript.

---

## 5. Exploratives Testing

### Explorative Session: Registrierung und Terminbuchung

| Feld | Inhalt |
|---|---|
| Charter | Teste Registrierung und Terminbuchung mit ungewöhnlichen, aber realistischen Eingaben. Achte auf Validierung, Fehlermeldungen, Weiterleitungen, mobile Darstellung und unerlaubte Zustände. |
| Timebox | 15 Minuten |
| Tester | Christian Vogel |
| Testumgebung | VM im Browser, zusätzlich mobile Ansicht über DevTools |
| Fokus | Leere Pflichtfelder, ungültige E-Mail, falsche Passwörter, lange Texte, Wochenende, Vergangenheit, Doppelbuchung, Zurück-Button, Reload |

### Beobachtungen

- Pflichtfelder werden abgefragt.
- Ungültige Login-Daten werden abgelehnt.
- Geschützte Seiten sind ohne Login nicht erreichbar.
- Terminbuchung funktioniert nur für gültige Slots.
- Doppelbuchungen werden verhindert.
- Mobile Ansicht ist nutzbar.
- Adminbereich ist nur nach Praxislogin erreichbar.

### Gefundene Fehler

Keine kritischen Fehler gefunden.

### Überraschende Reaktionen

Keine unerwarteten Reaktionen nach dem finalen Refactoring festgestellt.

### Offene Fragen

- Soll der Admin-Login zukünftig zusätzlich einen Benutzernamen verlangen?
- Sollen automatisierte Tests später auch vollständige Buchungsflows mit Testdaten ausführen?

### Ideen für spätere Testfälle

- Automatisierter Test für Registrierung mit gültigen Daten.
- Automatisierter Test für Login mit gültigen Daten.
- Automatisierter Test für Terminbuchung.
- Automatisierter Test für Adminbearbeitung.
- Testdatenbank oder Cleanup-Skript für wiederholbare E2E-Tests.

---

## 6. Fazit

Die funktionalen Tests zeigen, dass die zentralen Anforderungen der Terminbuchungs-Webapplikation erfüllt sind. Registrierung, Login, Terminbuchung, Anzeige eigener Buchungen, Stornierung und Adminverwaltung funktionieren wie erwartet.

Die wichtigsten Negativfälle wurden berücksichtigt: falsche Login-Daten, ungültige Registrierungsdaten, Doppelbuchung und Zugriff auf geschützte Seiten ohne Login. Zusätzlich wurde ein explorativer Test durchgeführt, um unerwartete Fehler und Nutzungssituationen zu prüfen.

Die automatisierten Playwright-Tests wurden erfolgreich ausgeführt. Sie ersetzen die manuellen Tests nicht vollständig, liefern aber einen zusätzlichen automatisierten Qualitätsnachweis für die Erreichbarkeit, grundlegende Darstellung und Validierung der Webapplikation.

Insgesamt ist der getestete Stand stabil und für die weitere Projektdokumentation sowie die Abgabe geeignet.

---

## Ergänzung: Vollständiger automatisierter Flow-Test

Zusätzlich zu den Smoke- und Validierungstests wurde ein vollständiger Playwright-Flow-Test erstellt.

Getestet wurden:
- Registrierung eines eindeutigen Testpatienten
- Login mit gültigen Patientendaten
- Zugriff auf geschützte Patientenseiten
- Terminbuchung eines freien Slots
- Anzeige des gebuchten Termins unter "Meine Buchungen"
- Verhinderung einer Doppelbuchung
- Stornierung eines eigenen Termins
- Schutz geschützter Seiten nach Logout
- Erreichbarkeit der Admin-Login-Seite
- Schutz des Adminbereichs ohne Login
- Admin-Login mit gültigem Testpasswort

Beim ersten vollständigen Lauf schlug der Admin-Login-Test in Chromium, Firefox und WebKit fehl, weil die lokale Umgebungsvariable PLAYWRIGHT_ADMIN_PASSWORD nicht auf das gültige Admin-Passwort gesetzt war.

Nach Korrektur der Testkonfiguration wurde zuerst der komplette Flow-Test in Chromium ausgeführt:

Running 11 tests using 4 workers
11 passed (8.8s)

Anschliessend wurde die gesamte Playwright-Test-Suite ausgeführt:

Running 51 tests using 5 workers
51 passed (15.8s)

Bewertung:
Alle automatisierten Tests sind bestanden. Es wurde kein fachlicher Fehler gefunden, der aktuell eine Anpassung an der Webseite erforderlich macht. Der zuerst aufgetretene Fehler war eine Testkonfigurationsfrage und kein Fehler der Webapplikation.

Aktueller Status:
Bestanden: 51 / 51
Fehlgeschlagen: 0 / 51
