import { test as setup, expect } from '@playwright/test';

const authFile = 'playwright/.auth/admin.json';

setup('authenticate as admin', async ({ page }) => {
  // Navigate to login page
  await page.goto('/admin/login');
  await expect(page).toHaveTitle(/Login Khusus Admin/i);

  // Fill credentials
  await page.locator('input[name="email"]').fill('admin@rasomandeh.com');
  await page.locator('input[name="password"]').fill('password');

  // Submit form
  await page.getByRole('button', { name: 'Masuk' }).click();

  // Wait for redirect to admin dashboard
  await page.waitForURL('**/admin');
  await expect(page.getByText('Ringkasan Restoran')).toBeVisible();

  // Save signed-in storage state
  await page.context().storageState({ path: authFile });
});
