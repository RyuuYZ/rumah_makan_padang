import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.describe('CUJ-01: Customer Food Ordering & QR Code Generation Flow', () => {
  test.beforeEach(() => {
    try {
      execSync('php artisan db:seed --class=TableSeeder', {
        cwd: process.cwd(),
        stdio: 'ignore',
      });
    } catch (e) {}
  });

  test('Eksplorasi menu, interaksi keranjang (Alpine.js), dan checkout berhasil ke halaman status QR', async ({ page }) => {
    page.on('console', msg => console.log('BROWSER CONSOLE:', msg.type(), msg.text()));
    page.on('response', async res => {
      if (res.url().includes('orders')) {
        console.log('API STATUS:', res.url(), res.status());
        try {
          console.log('API BODY:', await res.text());
        } catch (e) {}
      }
    });

    // 1. Akses halaman utama
    await page.goto('/');
    await expect(page).toHaveTitle(/Raso Mandeh/i);

    // 2. Pilih cabang pertama secara eksplisit
    const firstBranchBtn = page.locator('#cabang button').first();
    await firstBranchBtn.scrollIntoViewIfNeeded();
    await firstBranchBtn.click();

    // 3. Scroll ke bagian menu dan pastikan kartu menu tersedia
    const menuSection = page.locator('#menu');
    await menuSection.scrollIntoViewIfNeeded();
    await expect(page.getByRole('heading', { name: /Menu Autentik Raso Mandeh/i })).toBeVisible();

    // 4. Tambahkan menu pertama ke dalam keranjang
    const firstPesanBtn = page.getByRole('button', { name: 'Pesan' }).first();
    await expect(firstPesanBtn).toBeVisible();
    await firstPesanBtn.click();

    // 5. Verifikasi notifikasi / indikator badge keranjang bertambah
    const cartFloatingBtn = page.getByRole('button', { name: 'Keranjang Pesanan' });
    await expect(cartFloatingBtn).toBeVisible();
    const cartBadge = cartFloatingBtn.locator('span[x-text="cartCount"]');
    await expect(cartBadge).toHaveText('1');

    // 6. Buka drawer keranjang
    await cartFloatingBtn.click();
    const cartDrawer = page.locator('div[x-show="isCartOpen"]');
    await expect(cartDrawer.getByRole('heading', { name: 'Keranjang Pesanan' })).toBeVisible();

    // 7. Uji modifikasi kuantitas menu (+ dan -) di dalam drawer
    const plusBtn = cartDrawer.locator('button:text("+")').first();
    await plusBtn.click();
    await expect(cartBadge).toHaveText('2');

    const minusBtn = cartDrawer.locator('button:text("-")').first();
    await minusBtn.click();
    await expect(cartBadge).toHaveText('1');

    // 8. Isi informasi pemesan (Customer Information)
    const nameInput = cartDrawer.getByPlaceholder(/Atas Nama/i);
    await nameInput.fill('Budi Santoso E2E');

    // Pilih Makan di Tempat
    const dineInRadio = cartDrawer.locator('input[type="radio"][value="dine-in"]');
    await dineInRadio.check();

    // Isi Nomor Meja yang valid dan Catatan
    const tableInput = cartDrawer.getByPlaceholder(/Nomor Meja/i);
    await tableInput.fill('Meja 01');

    const notesInput = cartDrawer.getByPlaceholder(/Catatan pesanan/i);
    await notesInput.fill('Sambal ijo dibanyakin, gulai nangka dipisah.');

    // 8. Klik Buat QR Pesanan
    const checkoutBtn = cartDrawer.getByRole('button', { name: /Buat QR Pesanan/i });
    await expect(checkoutBtn).toBeVisible();
    await checkoutBtn.click();

    // 9. Verifikasi navigasi otomatis ke halaman /pesanan/{token}
    await page.waitForURL(/\/pesanan\/.+/);
    await expect(page.getByText('Pesanan Diterima!')).toBeVisible();
    await expect(page.getByText(/Nomor Pesanan/i)).toBeVisible();
    await expect(page.getByRole('img', { name: 'QR Order' })).toBeVisible();
    await expect(page.getByText('Budi Santoso E2E')).toBeVisible();
  });

  test('Validasi keranjang kosong menampilkan panduan yang informatif', async ({ page }) => {
    await page.goto('/');

    // Buka keranjang saat masih kosong
    const cartFloatingBtn = page.getByRole('button', { name: 'Keranjang Pesanan' });
    await cartFloatingBtn.click();

    // Verifikasi pesan empty state
    await expect(page.getByText('Keranjang Masih Kosong')).toBeVisible();
    await expect(page.getByText('Pilih hidangan masakan Padang autentik favorit Anda untuk mulai memesan.')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Lihat Pilihan Menu' })).toBeVisible();
  });
});
