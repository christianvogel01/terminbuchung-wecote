import { test, expect } from '@playwright/test';

test('Startseite lädt korrekt', async ({ page }) => {
  await page.goto('http://hosting-060.wecote.ls.eee.intern/terminbuchung/');

  await expect(page.locator('body')).toContainText('Praxis');
  await expect(page.locator('body')).toContainText('Jetzt registrieren');
  await expect(page.locator('body')).toContainText('Einloggen');
});
