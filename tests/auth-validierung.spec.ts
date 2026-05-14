import { test, expect } from '@playwright/test';

test('Login-Seite lädt korrekt', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/login.php');

  await expect(page.locator('body')).toContainText('Einloggen');
  await expect(page.locator('input[name="email"]')).toBeVisible();
  await expect(page.locator('input[name="password"]')).toBeVisible();
});

test('Login mit falschem Passwort wird abgelehnt', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/login.php');

  await page.locator('input[name="email"]').fill('nichtvorhanden@example.com');
  await page.locator('input[name="password"]').fill('FalschesPasswort123');
  await page.locator('button[type="submit"]').click();

  await expect(page.locator('body')).toContainText(/fehlgeschlagen|falsch|prüfen/i);
});

test('Registrierung mit ungültiger E-Mail wird nicht akzeptiert', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/register.php');

  await page.locator('input[name="first_name"]').fill('Test');
  await page.locator('input[name="last_name"]').fill('Ungueltig');
  await page.locator('input[name="birthdate"]').fill('1990-01-01');
  await page.locator('input[name="phone"]').fill('0791234567');
  await page.locator('input[name="street"]').fill('Teststrasse 1');
  await page.locator('input[name="postal_code"]').fill('6000');
  await page.locator('input[name="city"]').fill('Luzern');
  await page.locator('input[name="insurance_number"]').fill('TEST-123');
  await page.locator('input[name="email"]').fill('keine-gueltige-email');
  await page.locator('input[name="password"]').fill('Test1234');
  await page.locator('input[name="password_confirm"]').fill('Test1234');

  await page.locator('button[type="submit"]').click();

  await expect(page).toHaveURL(/register\.php/);
});

test('Admin-Login-Seite lädt korrekt', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/admin_login.php');

  await expect(page.locator('body')).toContainText(/Praxis|Login/i);
  await expect(page.locator('input[name="password"]')).toBeVisible();
});

test('Admin-Login mit falschem Passwort wird abgelehnt', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/admin_login.php');

  await page.locator('input[name="password"]').fill('FalschesPraxisPasswort');
  await page.locator('button[type="submit"]').click();

  await expect(page.locator('body')).toContainText(/falsch|fehlgeschlagen|Passwort/i);
});
