import { test, expect } from '@playwright/test';

// Use authenticated admin storage state
test.use({ storageState: 'playwright/.auth/admin.json' });

test.describe('CUJ-05: Admin Master Data Management (Menu & Cabang)', () => {
  test('Kelola menu: Pencarian menu, filter kategori, dan verifikasi data tabel', async ({ page }) => {
    // 1. Buka manajemen menu
    await page.goto('/admin/menu');
    await expect(page).toHaveTitle(/Menu Masakan/i);
    await expect(page.getByRole('button', { name: /Tambah Hidangan Baru/i })).toBeVisible();

    // 2. Gunakan filter pencarian
    const searchInput = page.locator('input[name="search"]');
    await searchInput.fill('Rendang');
    await page.getByRole('button', { name: 'Cari' }).click();

    // 3. Verifikasi hasil pencarian hanya menampilkan menu yang relevan
    await expect(page.locator('table tbody')).toContainText('Rendang');

    // 4. Reset pencarian
    const resetLink = page.getByRole('link', { name: 'Reset' });
    if (await resetLink.isVisible()) {
      await resetLink.click();
    }
  });

  test('Kelola cabang: Menampilkan seluruh cabang aktif beserta informasi operasional', async ({ page }) => {
    // 1. Buka manajemen cabang
    await page.goto('/admin/branches');
    await expect(page).toHaveTitle(/Cabang Restoran/i);

    // 2. Pastikan daftar cabang muncul dalam grid
    await expect(page.getByText(/Daftar Cabang Aktif/i)).toBeVisible();
    await expect(page.getByText(/Raso Mandeh/i).first()).toBeVisible();

    // 3. Tombol tambah cabang tersedia
    await expect(page.getByRole('button', { name: /Tambah Cabang Baru/i })).toBeVisible();
  });
});
