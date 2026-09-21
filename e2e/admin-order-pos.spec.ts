import { test, expect } from '@playwright/test';

// Reusable authenticated admin storage state
test.use({ storageState: 'playwright/.auth/admin.json' });

test.describe('CUJ-04: Admin Orders & POS Kasir Workflow', () => {
  test('Admin dapat melihat daftar pesanan, memfilter status, dan memperbarui status dapur', async ({ page }) => {
    // 1. Akses halaman Pesanan Masuk (filter pending agar dapat diubah ke process sesuai state machine)
    await page.goto('/admin/orders?status=pending');
    await expect(page).toHaveTitle(/Pesanan Masuk/i);

    // 2. Pastikan filter status dan tabel pesanan ter-render
    await expect(page.locator('select[name="status"]')).toBeVisible();
    await expect(page.locator('table')).toBeVisible();

    // 3. Verifikasi baris pesanan ada di tabel
    const firstRow = page.locator('tbody tr').first();
    await expect(firstRow).toBeVisible();

    // 4. Ubah status pesanan pending menjadi 'process'
    const statusSelect = firstRow.locator('select').first();
    await expect(statusSelect).toBeVisible();
    await statusSelect.selectOption('process');

    // Beri jeda sejenak untuk autosave fetch API
    await page.waitForTimeout(1000);

    // Buka filter process dan pastikan pesanan telah berpindah ke process
    await page.goto('/admin/orders?status=process');
    const updatedStatusSelect = page.locator('tbody tr').first().locator('select').first();
    await expect(updatedStatusSelect).toHaveValue('process');
  });

  test('POS Kasir: Cari order via input manual dan selesaikan transaksi (Lunas)', async ({ page }) => {
    // 1. Dapatkan kode pesanan aktif dari pesanan berstatus process atau pending
    await page.goto('/admin/orders?status=process');
    let orderCodeEl = page.locator('span[title="Kode Pesanan"]').first();
    if (!await orderCodeEl.isVisible()) {
      await page.goto('/admin/orders');
      orderCodeEl = page.locator('span[title="Kode Pesanan"]').first();
    }
    const orderCode = (await orderCodeEl.innerText()).trim();
    expect(orderCode).toBeTruthy();

    // 2. Buka halaman POS Scanner
    await page.goto('/admin/pos');
    await expect(page.getByRole('heading', { name: 'Cari Pesanan' })).toBeVisible();

    // 3. Beralih ke tab 'Ketik Manual'
    const manualTabBtn = page.getByRole('button', { name: 'Ketik Manual' });
    await manualTabBtn.click();

    // 4. Masukkan kode pesanan dan klik Cari
    const manualCodeInput = page.locator('input[placeholder*="Contoh: RM-"]');
    await manualCodeInput.fill(orderCode);
    await page.getByRole('button', { name: 'Cari' }).click();

    // 5. Verifikasi detail pesanan tampil di panel kanan
    await expect(page.getByText(orderCode)).toBeVisible();
    await expect(page.getByText(/Total Tagihan/i)).toBeVisible();
    await expect(page.getByText(/Rincian Menu/i)).toBeVisible();

    // 6. Klik tombol 'Tandai Selesai (Lunas)' jika status masih pending/process/ready
    const payBtn = page.locator('button:has-text("Tandai Selesai (Lunas)")');
    if (await payBtn.isVisible()) {
      await payBtn.click();
      // Verifikasi status berubah menjadi 'completed'
      await expect(page.locator('span:text("completed")')).toBeVisible();
    }
  });
});
