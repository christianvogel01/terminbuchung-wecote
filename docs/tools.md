# Eingesetzte Tools

## Playwright

Verwendungszweck:
Playwright wird für erste automatisierte End-to-End-Smoke-Tests verwendet. Damit wird geprüft, ob zentrale Seiten der Webapplikation auf der VM erreichbar sind und wichtige Texte/Buttons angezeigt werden.

Begründung:
Playwright eignet sich für browserbasierte Tests echter Nutzerflüsse. Es kann mehrere Browser testen und ist für Regressionstests nach Änderungen geeignet.

Aktueller Stand:
Ein erster Smoke-Test prüft die Startseite der Webapplikation auf der VM. Der Test wurde erfolgreich in Chromium, Firefox und WebKit ausgeführt.
