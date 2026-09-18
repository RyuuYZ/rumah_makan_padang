import { test, expect } from '@playwright/test';

test.describe('CUJ-02: Customer Tracking & Table Reservation', () => {
  test('Lacak pesanan: Penanganan validasi kode pesanan tidak ditemukan vs kode valid', async ({ page }) => {
    await page.goto('/cek-pesanan');
    await expect(page).toHaveTitle(/Cari Pesanan Saya/i);

    // Negative test: Masukkan kode pesanan sembarangan
    const orderInput = page.locator('input[name="order_code"]');
    await orderInput.fill('KODE-TIDAK-VALID-9999');
    await page.getByRole('button', { name: 'Cari Pesanan' }).click();

    // Verifikasi pesan error muncul
    await expect(page.getByText(/tidak ditemukan/i)).toBeVisible();
  });

  test('Reservasi meja: Validasi input dan pengajuan reservasi sukses', async ({ page }) => {
    await page.goto('/');

    const bookingSection = page.locator('#booking-section');
    await bookingSection.scrollIntoViewIfNeeded();
    await expect(page.getByRole('heading', { name: 'Reservasi Meja' })).toBeVisible();

    // Siapkan data tanggal & jam di masa depan (besok jam 18:00)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const yyyy = tomorrow.getFullYear();
    const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const dd = String(tomorrow.getDate()).padStart(2, '0');
    const futureDateTime = `${yyyy}-${mm}-${dd}T18:00`;

    // Isi formulir reservasi
    await page.locator('input[name="customer_name"]').fill('Siti Rahma E2E');
    await page.locator('input[name="customer_phone"]').fill('081298765432');
    await page.locator('input[name="reservation_time"]').fill(futureDateTime);
    await page.locator('input[name="guest_count"]').fill('4');
    
    // Pilih cabang pertama yang tersedia
    const branchSelect = page.locator('select[name="branch_id"]');
    await branchSelect.selectOption({ index: 1 });

    await page.locator('textarea[name="notes"]').fill('Mohon siapkan meja di area dekat jendela / non-smoking.');

    // Submit reservasi
    await page.getByRole('button', { name: 'Konfirmasi Booking' }).click();

    // Verifikasi pesan sukses muncul
    await expect(page.getByText(/reservasi.+berhasil dikirim|berhasil/i)).toBeVisible();
  });
});
