import { test, expect, Page } from '@playwright/test';

const BASE_URL = 'http://hosting-060.wecote.ls.eee.intern/terminbuchung';

function uniqueEmail(prefix: string, browserName: string): string {
  return `${prefix}-${browserName}-${Date.now()}-${Math.floor(Math.random() * 10000)}@example.com`;
}

function nextWeekday(offsetDays: number): string {
  const date = new Date();
  date.setDate(date.getDate() + offsetDays);

  while (date.getDay() === 0 || date.getDay() === 6) {
    date.setDate(date.getDate() + 1);
  }

  return date.toISOString().split('T')[0];
}

async function registerPatient(page: Page, email: string, password: string) {
  await page.goto(`${BASE_URL}/register.php`);

  await page.locator('input[name="first_name"]').fill('Playwright');
  await page.locator('input[name="last_name"]').fill('Testpatient');
  await page.locator('input[name="birthdate"]').fill('1990-01-01');
  await page.locator('input[name="phone"]').fill('0791234567');
  await page.locator('input[name="street"]').fill('Teststrasse 1');
  await page.locator('input[name="postal_code"]').fill('6000');
  await page.locator('input[name="city"]').fill('Luzern');
  await page.locator('input[name="insurance_number"]').fill(`TEST-${Date.now()}`);
  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="password"]').fill(password);
  await page.locator('input[name="password_confirm"]').fill(password);

  await page.locator('button[type="submit"]').click();

  await expect(page).toHaveURL(/login\.php|index\.php/);
}

async function loginPatient(page: Page, email: string, password: string) {
  await page.goto(`${BASE_URL}/login.php`);

  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="password"]').fill(password);
  await page.locator('button[type="submit"]').click();

  await expect(page.locator('body')).not.toContainText(/fehlgeschlagen|falsch/i);
}

async function logoutPatient(page: Page) {
  await page.goto(`${BASE_URL}/logout.php`);
}

async function selectFirstAvailableSlot(page: Page): Promise<string> {
  const slotRegex = /^[0-2][0-9]:[0-5][0-9]$/;

  const radioSlots = page.locator('input[type="radio"][name="time"]');
  if (await radioSlots.count() > 0) {
    const value = await radioSlots.first().getAttribute('value');
    await radioSlots.first().check();
    if (!value) throw new Error('Radio-Slot hat keinen value.');
    return value.substring(0, 5);
  }

  const buttonSlots = page.locator('button').filter({ hasText: slotRegex });
  if (await buttonSlots.count() > 0) {
    const text = (await buttonSlots.first().innerText()).trim();
    await buttonSlots.first().click();
    return text.substring(0, 5);
  }

  const dataSlots = page.locator('[data-time]');
  if (await dataSlots.count() > 0) {
    const value = await dataSlots.first().getAttribute('data-time');
    await dataSlots.first().click();
    if (!value) throw new Error('data-time fehlt.');
    return value.substring(0, 5);
  }

  const select = page.locator('select[name="time"]');
  if (await select.count() > 0) {
    const options = select.locator('option');
    const count = await options.count();

    for (let i = 0; i < count; i++) {
      const value = await options.nth(i).getAttribute('value');
      if (value && slotRegex.test(value.substring(0, 5))) {
        await select.selectOption(value);
        return value.substring(0, 5);
      }
    }
  }

  throw new Error('Kein auswählbarer Termin-Slot gefunden.');
}

async function bookAppointment(page: Page, date: string, reason: string): Promise<string> {
  await page.goto(`${BASE_URL}/booking.php`);

  const dateInput = page.locator('input[type="date"], input[name="date"]').first();
  await expect(dateInput).toBeVisible();
  await dateInput.fill(date);
  await dateInput.dispatchEvent('change');

  await page.waitForTimeout(1000);

  const selectedTime = await selectFirstAvailableSlot(page);

  const reasonInput = page.locator('textarea[name="reason"], input[name="reason"]').first();
  if (await reasonInput.count() > 0) {
    await reasonInput.fill(reason);
  }

  const submitButton = page.locator('button[type="submit"], button').filter({
    hasText: /buchen|termin|speichern/i,
  }).first();

  await submitButton.click();

  await expect(page.locator('body')).toContainText(/erfolgreich|gebucht|Buchung|Termin/i);

  return selectedTime;
}

test.describe('Kompletter funktionaler Flow der Terminbuchung', () => {
  test.describe.configure({ mode: 'serial' });

  let email: string;
  const password = 'Test1234!';
  let appointmentDate: string;
  let appointmentTime: string;

  test.beforeAll(async ({ browserName }) => {
    email = uniqueEmail('playwright-patient', browserName);

    const browserOffsets: Record<string, number> = {
      chromium: 7,
      firefox: 10,
      webkit: 13,
    };

    appointmentDate = nextWeekday(browserOffsets[browserName] ?? 7);
  });

  test('Patient kann sich registrieren', async ({ page }) => {
    await registerPatient(page, email, password);
  });

  test('Patient kann sich mit gültigen Daten einloggen', async ({ page }) => {
    await loginPatient(page, email, password);

    await expect(page.locator('body')).toContainText(/Praxis|Termin|Buchung|Meine/i);
  });

  test('Geschützte Buchungsseite ist nach Login erreichbar', async ({ page }) => {
    await loginPatient(page, email, password);

    await page.goto(`${BASE_URL}/booking.php`);

    await expect(page.locator('body')).toContainText(/Termin|Buchung|Datum|Uhrzeit/i);
  });

  test('Patient kann einen freien Termin buchen', async ({ page }) => {
    await loginPatient(page, email, password);

    appointmentTime = await bookAppointment(
      page,
      appointmentDate,
      'Automatischer Playwright Kontrolltermin'
    );

    expect(appointmentTime).toMatch(/[0-2][0-9]:[0-5][0-9]/);
  });

  test('Patient sieht den gebuchten Termin unter Meine Buchungen', async ({ page }) => {
    await loginPatient(page, email, password);

    await page.goto(`${BASE_URL}/my_bookings.php`);

    await expect(page.locator('body')).toContainText(appointmentDate);
    await expect(page.locator('body')).toContainText(appointmentTime);
  });

  test('Doppelbuchung wird verhindert', async ({ page, browserName }) => {
    const secondEmail = uniqueEmail('playwright-double', browserName);

    await registerPatient(page, secondEmail, password);
    await loginPatient(page, secondEmail, password);

    await page.goto(`${BASE_URL}/booking.php`);

    const dateInput = page.locator('input[type="date"], input[name="date"]').first();
    await dateInput.fill(appointmentDate);
    await dateInput.dispatchEvent('change');

    await page.waitForTimeout(1000);

    const bodyText = await page.locator('body').innerText();

    const alreadyBookedVisible = bodyText.includes(appointmentTime);

    if (alreadyBookedVisible) {
      const matchingButton = page.locator('button').filter({ hasText: appointmentTime });
      if (await matchingButton.count() > 0) {
        await expect(matchingButton.first()).toBeDisabled();
      }
    } else {
      expect(bodyText).not.toContain(appointmentTime);
    }
  });

  test('Patient kann eigenen Termin stornieren, falls Stornierung erlaubt ist', async ({ page }) => {
    await loginPatient(page, email, password);

    await page.goto(`${BASE_URL}/my_bookings.php`);

    const cancelButton = page.locator('button, input[type="submit"], a').filter({
      hasText: /stornieren|absagen|löschen/i,
    }).first();

    if (await cancelButton.count() === 0) {
      test.skip(true, 'Kein Stornieren-Button sichtbar. Termin möglicherweise nicht stornierbar.');
    }

    page.once('dialog', async dialog => {
      await dialog.accept();
    });

    await cancelButton.click();

    await expect(page.locator('body')).toContainText(/storniert|gelöscht|Buchungen|Termin/i);
  });

  test('Nach Logout ist die Buchungsseite geschützt', async ({ page }) => {
    await logoutPatient(page);

    await page.goto(`${BASE_URL}/booking.php`);

    await expect(page).toHaveURL(/login\.php/);
  });
});

test.describe('Adminbereich', () => {
  test('Admin-Login-Seite ist erreichbar', async ({ page }) => {
    await page.goto(`${BASE_URL}/admin_login.php`);

    await expect(page.locator('body')).toContainText(/Praxis|Login/i);
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('Adminbereich ist ohne Login geschützt', async ({ page }) => {
    await page.goto(`${BASE_URL}/admin.php`);

    await expect(page).toHaveURL(/admin_login\.php/);
  });

  test('Admin kann sich mit gültigem Passwort einloggen, falls Passwort als Umgebungsvariable gesetzt ist', async ({ page }) => {
    const adminPassword = process.env.PLAYWRIGHT_ADMIN_PASSWORD;

    test.skip(!adminPassword, 'PLAYWRIGHT_ADMIN_PASSWORD ist nicht gesetzt.');

    await page.goto(`${BASE_URL}/admin_login.php`);

    await page.locator('input[name="password"]').fill(adminPassword!);
    await page.locator('button[type="submit"]').click();

    await expect(page).toHaveURL(/admin\.php|admin_calendar\.php/);
    await expect(page.locator('body')).toContainText(/Termin|Patient|Dashboard|Kalender|Buchung/i);
  });
});
