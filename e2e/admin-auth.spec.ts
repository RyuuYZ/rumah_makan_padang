import { test, expect } from '@playwright/test';

// Reset auth storage state to test login from guest state
test.use({ storageState: { cookies: [], origins: [] } });

test.describe('CUJ-03: Admin Authentication & Security Guard', () => {
  test('Proteksi rute: Guest yang membuka rute admin langsung dialihkan ke login', async ({ page }) => {
    await page.goto('/admin');
    await page.waitForURL('**/admin/login');
    await expect(page).toHaveTitle(/Login Khusus Admin/i);
  });

  test('Validasi login gagal: Menampilkan pesan kesalahan jika kredensial salah', async ({ page }) => {
    await page.goto('/admin/login');

    await page.locator('input[name="email"]').fill('salah@rasomandeh.com');
    await page.locator('input[name="password"]').fill('passwordsalah123');
    await page.getByRole('button', { name: 'Masuk' }).click();

    // Pastikan tetap di halaman login dan terdapat pesan error
    await expect(page).toHaveURL(/.*admin\/login/);
    await expect(page.getByText(/Email atau kata sandi yang Anda masukkan salah|tidak cocok|error|gagal/i)).toBeVisible();
  });

  test('Login berhasil & proses logout', async ({ page }) => {
    await page.goto('/admin/login');

    await page.locator('input[name="email"]').fill('admin@rasomandeh.com');
    await page.locator('input[name="password"]').fill('password');
    await page.getByRole('button', { name: 'Masuk' }).click();

    // Diarahkan ke dashboard admin
    await page.waitForURL('**/admin');
    await expect(page.getByText('Ringkasan Restoran')).toBeVisible();

    // Buka profile dropdown di header
    const profileBtn = page.locator('header button:has(div:text("A"))').or(page.locator('header button:has-text("Administrator")')).first();
    await profileBtn.click();

    // Klik tombol Logout
    const logoutBtn = page.getByRole('button', { name: /Keluar|Logout/i }).or(page.locator('button:has-text("Keluar")')).or(page.locator('form[action*="logout"] button')).first();
    await logoutBtn.click();

    // Verifikasi kembali diarahkan ke login
    await page.waitForURL('**/admin/login');
    await expect(page).toHaveTitle(/Login Khusus Admin/i);
  });
});
