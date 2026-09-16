
RASYA

BUSINESS REQUIREMENT DOCUMENT
(BRD)

Sistem Manajemen & Pemesanan
Warung Nasi Padang

Dokumen Kebutuhan Bisnis (BRD)
Versi 1.0 | September 2026

Dokumen ini menjadi acuan bisnis, kebutuhan fungsional, alur operasional, dan ruang lingkup pengembangan sistem.

Kontrol Dokumen
Item
Keterangan
Nama dokumen
Business Requirement Document (BRD)
Sistem
Sistem Manajemen & Pemesanan Warung Nasi Padang
Versi
1.0
Tanggal
1 September 2026
Status
Draft untuk analisis dan pengembangan
Target pengguna
Pemilik/administrator, kasir, dan pelanggan
Model operasional
Offline di warung + pemesanan digital melalui web/QR
Catatan scope
Role dapur tidak menjadi role inti; dapat diaktifkan sebagai modul opsional untuk outlet yang memang membutuhkan persiapan makanan berbasis pesanan.


Daftar Isi
1. Ringkasan Eksekutif	3
2. Latar Belakang dan Masalah Bisnis	3
2.1 Kondisi Operasional	3
2.2 Masalah yang Ingin Diselesaikan	3
3. Tujuan dan Sasaran	3
4. Ruang Lingkup	4
4.1 In Scope	4
4.2 Out of Scope / Pengembangan	4
5. Asumsi dan Prinsip Operasional	4
6. Pemangku Kepentingan dan Peran	5
7. Proses Bisnis Target	5
7.1 Skenario A — Pelanggan Datang Langsung	5
7.2 Skenario B — Customer Self-Order via QR	5
7.3 Skenario C — Customer Menunjukkan QR Order ke Kasir	6
7.4 Skenario D — Outlet Menggunakan Modul Dapur	6
8. Kebutuhan Fungsional	6
8.1 Modul Autentikasi & Hak Akses	6
8.2 Modul Menu & Paket	6
8.3 Modul POS Kasir	7
8.4 Modul Customer Ordering	7
8.5 Modul Dashboard & Laporan	7
8.6 Modul Ketersediaan/Stok	8
8.7 Modul Dapur (Opsional)	8
9. Kebutuhan Non-Fungsional	8
10. Aturan Bisnis	9
11. Data dan Entitas Utama	9
12. Hak Akses	9
13. Skenario Penggunaan Utama	10
14. Laporan dan Indikator	10
15. Prioritas Pengembangan	10
16. Risiko dan Mitigasi	11
17. Kriteria Penerimaan	11
18. Pengembangan Lanjutan	12
19. Kesimpulan	12
Lampiran A — Catatan Validasi Lapangan	12
Lampiran B — Glosarium	13
Referensi Dasar	13


1. Ringkasan Eksekutif
Sistem ini dirancang sebagai sistem operasional sederhana untuk warung makan nasi Padang yang menggabungkan fungsi kasir (POS), katalog menu digital, pemesanan melalui QR/web, pembayaran, pencatatan transaksi, pengelolaan menu, stok/ketersediaan, serta pelaporan pemilik.
Karakter operasional nasi Padang menjadi dasar desain. Berbeda dengan restoran yang seluruh makanan baru diproses setelah order, banyak warung nasi Padang menampilkan lauk yang telah tersedia di etalase dan pelanggan memilih lauk yang diinginkan. Karena itu, Kitchen Display System (KDS) dan role dapur tidak ditetapkan sebagai kebutuhan inti. Untuk outlet yang memiliki proses finishing atau produksi berdasarkan pesanan, modul dapur dapat diaktifkan sebagai pengembangan.
Sistem mendukung dua pola utama: (1) pelanggan datang langsung dan dilayani melalui kasir; serta (2) pelanggan melakukan self-order melalui QR Code/web, kemudian order dapat diverifikasi/ditindaklanjuti oleh kasir. QR Code order diposisikan sebagai identitas pesanan, bukan pengganti QRIS pembayaran. QRIS sendiri merupakan standar QR pembayaran nasional yang ditetapkan Bank Indonesia dan mendukung model merchant-presented serta customer-presented.
2. Latar Belakang dan Masalah Bisnis
2.1 Kondisi Operasional
Warung nasi Padang umumnya membutuhkan proses transaksi yang cepat karena produk utama telah tersedia dalam display/etalase. Kebutuhan digitalisasi terbesar bukan pada pengaturan produksi dapur, melainkan pada ketepatan pencatatan order, pembayaran, ketersediaan menu, pengelolaan harga, dan pelaporan.
Praktik POS F&B di Indonesia saat ini lazim menggabungkan POS, QR ordering, laporan, inventory, dan pada sebagian usaha KDS. Namun KDS lebih relevan untuk usaha yang memproses order ke dapur secara langsung. 
2.2 Masalah yang Ingin Diselesaikan
Transaksi manual berisiko salah mencatat jenis lauk, jumlah, harga, atau total.
Kasir perlu mengetahui menu yang tersedia/habis agar tidak menjual item yang sudah tidak tersedia.
Pemilik membutuhkan ringkasan omzet, transaksi, menu terlaris, dan performa penjualan tanpa merekap manual.
Pelanggan yang datang langsung tetap membutuhkan proses cepat, sedangkan pelanggan tertentu dapat memilih menu melalui perangkatnya sendiri.
Pesanan digital perlu memiliki identitas yang jelas agar mudah dicocokkan saat pembayaran di kasir.
Perubahan harga/menu harus dapat dikelola tanpa mengganti seluruh media menu secara manual.

3. Tujuan dan Sasaran
Tujuan
Indikator keberhasilan
Mempercepat transaksi
Kasir dapat membuat transaksi dengan langkah singkat dan item mudah dicari.
Mengurangi salah pencatatan
Order digital dan order kasir menggunakan struktur item, jumlah, harga, dan catatan yang konsisten.
Memudahkan pelanggan
Menu, harga, ketersediaan, order, dan status pesanan mudah dipahami.
Meningkatkan kontrol pemilik
Dashboard dan laporan tersedia berdasarkan periode.
Menjaga ketersediaan menu
Item dapat diberi status tersedia, hampir habis, atau habis.
Mendukung pembayaran digital
Sistem menyediakan alur pembayaran QRIS sesuai mekanisme merchant/PJP yang digunakan.
Mudah dioperasikan
UI kasir sederhana dan tidak membutuhkan proses administrasi yang tidak relevan dengan warung.


4. Ruang Lingkup
4.1 In Scope
Manajemen akun dan hak akses Admin serta Kasir.
Manajemen kategori, menu, harga, foto, status ketersediaan, dan paket.
POS untuk transaksi kasir.
Pemesanan pelanggan melalui web/mobile browser dan QR Code.
QR Order untuk meja/area atau titik pemesanan.
Pembayaran tunai dan QRIS; status pembayaran dicatat.
Struk cetak dan/atau digital.
Riwayat transaksi.
Dashboard penjualan dan laporan.
Stok/ketersediaan sederhana pada level menu; inventori bahan baku rinci bersifat pengembangan.
Shift kasir.
Audit untuk aktivitas sensitif seperti void/pembatalan/koreksi.
Status order sederhana: dibuat, dikonfirmasi, siap diambil/diserahkan, selesai/dibatalkan.
4.2 Out of Scope / Pengembangan
Role Gudang terpisah.
Role Dapur sebagai kebutuhan wajib.
Accounting/akuntansi penuh.
Payroll dan HRIS.
Delivery fleet/kurir internal.
Reservasi meja kompleks.
Multi-cabang pada versi awal.
Membership/loyalty kompleks.
Split bill kompleks.
Integrasi marketplace makanan pihak ketiga pada versi awal.
5. Asumsi dan Prinsip Operasional
Model utama adalah warung nasi Padang dengan lauk siap jual pada display/etalase.
Kasir tetap menjadi titik kontrol utama untuk transaksi offline.
Pelanggan tidak diwajibkan memasang aplikasi; pemesanan digital dapat berjalan melalui browser.
QR Order dan QRIS adalah dua fungsi berbeda: QR Order mengidentifikasi/mengambil pesanan, sedangkan QRIS memfasilitasi pembayaran.
QRIS merchant harus berasal dari PJP berizin dan mengikuti ketentuan Bank Indonesia. QRIS MPM statis cocok untuk usaha mikro/kecil, sedangkan MPM dinamis dapat digunakan untuk transaksi dengan nominal yang dihasilkan sistem. 
Sistem tidak mengasumsikan seluruh warung Padang memiliki pola operasional yang sama; konfigurasi fitur harus dapat disesuaikan dengan outlet.
Jika outlet membutuhkan dapur yang memproses pesanan, modul Dapur/KDS dapat diaktifkan tanpa mengubah model inti.
6. Pemangku Kepentingan dan Peran
Aktor
Tanggung jawab
Kebutuhan utama
Admin/Owner
Mengelola sistem dan memantau bisnis.
Dashboard, menu, harga, stok/ketersediaan, akun, laporan, promo.
Kasir
Mengelola transaksi dan pembayaran.
POS, order QR, cash, QRIS, struk, history, shift.
Customer
Melihat menu dan melakukan pemesanan.
Menu, harga, ketersediaan, keranjang, checkout, status order.
Dapur (opsional)
Menangani order yang membutuhkan proses dapur.
Daftar order dan perubahan status, bila outlet membutuhkan.
Pemilik/Manajemen
Menggunakan hasil laporan untuk keputusan bisnis.
Omzet, menu terlaris, tren, transaksi, ringkasan operasional.





7. Proses Bisnis Target
7.1 Skenario A — Pelanggan Datang Langsung
Pelanggan memilih makanan/lauk dari display atau menyampaikan pilihan kepada kasir.
Kasir memasukkan item, jumlah, dan catatan bila diperlukan.
Sistem menghitung subtotal dan total.
Kasir memilih metode pembayaran: tunai atau QRIS.
Sistem mencatat transaksi setelah pembayaran dikonfirmasi sesuai mekanisme yang digunakan.
Struk diberikan/cetak atau dikirim secara digital.
Transaksi masuk ke laporan dan riwayat.
7.2 Skenario B — Customer Self-Order via QR
Customer memindai QR Order di meja/titik yang disediakan.
Browser membuka menu tanpa wajib memasang aplikasi.
Customer memilih item, jumlah, paket/tambahan, dan catatan.
Sistem membuat draft/order dengan nomor unik.
Order masuk ke sistem dan dapat dilihat kasir.
Kasir memverifikasi order dan/atau pembayaran sesuai kebijakan outlet.
Setelah order siap diserahkan, kasir mengubah status menjadi siap/selesai.
Customer melihat status order melalui halaman order.
7.3 Skenario C — Customer Menunjukkan QR Order ke Kasir
Customer menunjukkan QR/nomor order.
Kasir memindai QR menggunakan kamera perangkat atau memasukkan nomor order.
Sistem menampilkan seluruh item, jumlah, catatan, dan total.
Kasir mengonfirmasi item dan pembayaran.
Transaksi diselesaikan dan struk diterbitkan.
7.4 Skenario D — Outlet Menggunakan Modul Dapur
Order yang membutuhkan proses dapur diteruskan ke layar dapur.
Petugas dapur mengubah status menjadi diproses.
Setelah siap, status menjadi siap diserahkan.
Kasir/customer melihat status terbaru.




8. Kebutuhan Fungsional
8.1 Modul Autentikasi & Hak Akses
ID
Kebutuhan
Prioritas
AUTH-01
Login Admin/Kasir menggunakan kredensial yang aman.
P0
AUTH-02
Role menentukan menu/fungsi yang dapat diakses.
P0
AUTH-03
Admin dapat membuat, mengubah, menonaktifkan akun kasir.
P0
AUTH-04
Sistem mencatat waktu login/logout dan aktivitas sensitif.
P1
AUTH-05
Password dapat diubah/reset sesuai mekanisme keamanan.
P0

8.2 Modul Menu & Paket
ID
Kebutuhan
Prioritas
MENU-01
CRUD kategori menu.
P0
MENU-02
CRUD menu: nama, foto, deskripsi, harga, kategori.
P0
MENU-03
Status ketersediaan: tersedia/hampir habis/habis.
P0
MENU-04
Menu yang habis tidak dapat dipilih customer.
P0
MENU-05
Admin dapat membuat paket dengan komponen menu dan harga.
P0
MENU-06
Dukungan tambahan/add-on dengan harga tambahan.
P1
MENU-07
Riwayat perubahan harga penting disimpan.
P1

8.3 Modul POS Kasir
ID
Kebutuhan
Prioritas
POS-01
Menampilkan katalog menu, kategori, foto, harga, dan status.
P0
POS-02
Menambah/mengurangi jumlah item.
P0
POS-03
Mencatat dine-in/takeaway.
P0
POS-04
Mencatat nomor meja atau titik order bila digunakan.
P1
POS-05
Mencatat catatan order.
P0
POS-06
Menghasilkan nomor transaksi/order unik.
P0
POS-07
Pembayaran cash dengan perhitungan kembalian.
P0
POS-08
Pembayaran QRIS dan pencatatan status pembayaran.
P0
POS-09
Cetak/download/share struk.
P0
POS-10
Riwayat dan pencarian transaksi.
P0
POS-11
Void/refund/koreksi dibatasi sesuai hak akses dan tercatat.
P1
POS-12
Buka/tutup shift dan ringkasan kas.
P1


8.4 Modul Customer Ordering
ID
Kebutuhan
Prioritas
CUS-01
Customer dapat membuka katalog dari browser.
P0
CUS-02
Customer dapat melihat foto, harga, kategori, dan ketersediaan.
P0
CUS-03
Customer dapat memilih item dan jumlah.
P0
CUS-04
Customer dapat memilih paket/tambahan yang tersedia.
P0
CUS-05
Customer dapat menambahkan catatan.
P0
CUS-06
Customer dapat memilih dine-in/takeaway bila outlet mengaktifkan.
P0
CUS-07
Sistem menghasilkan order ID/QR order.
P0
CUS-08
Customer dapat melihat status order.
P0
CUS-09
Customer dapat melihat ringkasan dan total sebelum konfirmasi.
P0


8.5 Modul Dashboard & Laporan
ID
Kebutuhan
Prioritas
RPT-01
Dashboard omzet hari ini dan periode.
P0
RPT-02
Jumlah transaksi.
P0
RPT-03
Menu terlaris.
P0
RPT-04
Penjualan berdasarkan metode pembayaran.
P0
RPT-05
Laporan per kasir/shift.
P1
RPT-06
Filter tanggal/periode.
P0
RPT-07
Export laporan ke PDF/Excel.
P1
RPT-08
Ringkasan pembatalan/void.
P1



8.6 Modul Ketersediaan/Stok
ID
Kebutuhan
Prioritas
INV-01
Admin dapat mengubah status ketersediaan menu.
P0
INV-02
Sistem dapat memberi indikator stok/ketersediaan rendah.
P1
INV-03
Admin dapat mencatat stok sederhana per item.
P1
INV-04
Inventori bahan baku dan resep/BOM rinci merupakan fase pengembangan.
P2


8.7 Modul Dapur (Opsional)
Modul ini tidak menjadi bagian wajib dari operasional inti. KDS lazim digunakan pada restoran yang mengirim order ke dapur secara real-time, tetapi tidak perlu dipaksakan pada warung Padang yang menjual lauk siap display. Sistem F&B modern di Indonesia memang menyediakan KDS sebagai modul terpisah, sehingga pendekatan modular ini sesuai praktik produk saat ini. 
ID
Kebutuhan
Prioritas
KDS-01
Menampilkan order yang membutuhkan persiapan.
P2
KDS-02
Status diproses/siap.
P2
KDS-03
Timer order dan prioritas.
P2


9. Kebutuhan Non-Fungsional
Kategori
Kebutuhan
Usability
Kasir dapat menyelesaikan transaksi dengan jumlah langkah minimal; tombol penting mudah dijangkau pada tablet/PC.
Performance
Halaman menu dan POS harus responsif pada jaringan normal; operasi lokal yang kritis perlu meminimalkan request tidak perlu.
Availability
Sistem perlu memiliki strategi ketika koneksi terganggu; mode offline kasir dapat menjadi fase P1/P2 sesuai arsitektur.
Security
Password disimpan dengan hashing; session aman; akses berdasarkan role; aktivitas sensitif dicatat.
Data integrity
Transaksi yang telah dibayar tidak boleh berubah tanpa mekanisme koreksi/void yang tercatat.
Auditability
Perubahan harga, void, pembatalan, dan perubahan status penting dapat ditelusuri.
Scalability
Struktur data memungkinkan penambahan outlet di masa depan tanpa wajib mengaktifkan multi-cabang pada MVP.
Compatibility
Customer ordering berjalan pada browser mobile modern; kasir dapat berjalan pada desktop/tablet.
Maintainability
Modul dipisahkan berdasarkan domain: auth, menu, order, payment, report, inventory.
Privacy
Data customer dikumpulkan seminimal mungkin; akun customer tidak wajib jika tidak dibutuhkan.


10. Aturan Bisnis
BR-01: Setiap transaksi memiliki nomor unik.
BR-02: Menu berstatus habis tidak dapat dipilih pada order baru.
BR-03: Harga pada transaksi disimpan sebagai snapshot agar perubahan harga berikutnya tidak mengubah transaksi lama.
BR-04: Total transaksi = subtotal item + biaya/komponen yang dikonfigurasi - diskon yang sah.
BR-05: Transaksi cash harus mencatat nominal diterima dan kembalian.
BR-06: Pembayaran QRIS harus dianggap lunas hanya setelah status pembayaran dikonfirmasi sesuai mekanisme merchant/PJP; screenshot bukti pembayaran tidak otomatis dianggap valid.
BR-07: QR Order mengidentifikasi pesanan dan tidak boleh disamakan dengan QRIS pembayaran.
BR-08: Pembatalan/void setelah pembayaran membutuhkan otorisasi sesuai kebijakan outlet dan tercatat dalam audit log.
BR-09: Customer dapat memesan tanpa akun pada MVP; jika akun diterapkan, data yang dikumpulkan harus dibatasi pada kebutuhan sistem.
BR-10: Status order mengikuti alur yang dikonfigurasi outlet; alur inti tidak mewajibkan status 'diproses dapur'.
BR-11: Paket dapat memiliki komponen tetap dan pilihan/add-on sesuai konfigurasi.
BR-12: Admin adalah pemegang kontrol utama terhadap menu, harga, akun, laporan, dan konfigurasi.
BR-13: Role Dapur bersifat opsional dan hanya aktif jika outlet membutuhkan KDS.




11. Data dan Entitas Utama
Entitas
Data inti
User
id, nama, username/email, password_hash, role, status, created_at
Category
id, nama, status
Menu
id, category_id, nama, deskripsi, foto, harga, availability_status
Package
id, nama, harga, deskripsi, status
PackageItem
package_id, menu_id, qty/opsi
Order
id, order_number, source, order_type, table/location, customer_reference, status, payment_status, total, timestamps
OrderItem
order_id, menu_id/package_id, qty, unit_price, note, subtotal
Payment
order_id, method, amount, reference, status, paid_at
Shift
kasir_id, opening_cash, opened_at, closing_cash, closed_at, status
Stock (opsional)
menu_id/item_id, quantity, minimum_level, status
AuditLog
user_id, action, entity, entity_id, old_value, new_value, timestamp

12. Hak Akses
Fungsi
Admin
Kasir
Customer
Dapur*
Dashboard
✓
Ringkas
—
—
Kelola menu/harga
✓
—
—
—
Kelola paket
✓
—
—
—
Kelola akun kasir
✓
—
—
—
POS transaksi
✓
✓
—
—
QR order
Lihat
Kelola/verifikasi
Buat
—
Pembayaran
Lihat/kelola
✓
Jika diaktifkan
—
Struk
✓
✓
—
—
History transaksi
✓
✓ terbatas
Pesanan sendiri
—
Laporan
✓
Shift sendiri
—
—
Ketersediaan menu
✓
Lihat
Lihat
—
KDS
Opsional
Opsional
Status
✓ jika modul aktif


* Dapur adalah role opsional, bukan role inti.
13. Skenario Penggunaan Utama
Use Case
Aktor
Hasil
Login
Admin/Kasir
Masuk sesuai hak akses.
Kelola Menu
Admin
Menu dan harga siap digunakan.
Buat Paket
Admin
Paket dapat dipilih customer/kasir.
Buat Order Offline
Kasir
Order tercatat sebagai transaksi.
Self-Order
Customer
Order tercatat dengan nomor unik.
Scan QR Order
Kasir
Detail order tampil dan dapat diverifikasi.
Bayar Cash
Kasir
Transaksi lunas dan kembalian tercatat.
Bayar QRIS
Kasir/Customer
Status pembayaran tercatat sesuai konfirmasi.
Cetak Struk
Kasir
Bukti transaksi tersedia.
Update Ketersediaan
Admin
Menu habis tidak dapat dipesan.
Lihat Dashboard
Admin
Pemilik melihat kondisi penjualan.
Tutup Shift
Kasir/Admin
Ringkasan kas dan transaksi shift tersimpan.


14. Laporan dan Indikator
Omzet per hari/minggu/bulan.
Jumlah transaksi per periode.
Top menu berdasarkan jumlah terjual dan nilai penjualan.
Penjualan per metode pembayaran.
Penjualan per kasir/shift.
Daftar transaksi batal/void.
Menu habis/hampir habis.
Ringkasan order online vs kasir.
Opsional: margin/laba setelah data biaya tersedia.



15. Prioritas Pengembangan
Prioritas
Isi
Keputusan
P0 — Must Have
Auth, menu, paket, POS, order customer, QR Order, cash, QRIS recording, struk, history, dashboard dasar, laporan dasar, availability.
Wajib MVP.
P1 — Should Have
Shift, audit log, promo sederhana, stok sederhana, export, riwayat customer, offline buffer.
Fase kedua / setelah MVP stabil.
P2 — Could Have
KDS, inventory bahan/BOM, supplier, loyalty, membership, delivery, multi-cabang, accounting, reservasi, split bill.
Pengembangan sesuai kebutuhan outlet.


16. Risiko dan Mitigasi
Risiko
Dampak
Mitigasi
Internet putus
Order digital/POS terganggu.
Rancang offline buffer untuk transaksi kritis; sediakan prosedur manual sementara.
Pembayaran QRIS belum terkonfirmasi
Order dianggap lunas padahal belum.
Gunakan status pembayaran; jangan mengandalkan screenshot saja.
Menu habis tetapi masih muncul
Komplain/customer kecewa.
Status availability harus dapat diperbarui cepat.
Harga berubah
Transaksi lama ikut berubah.
Simpan unit_price sebagai snapshot pada OrderItem.
Kasir salah input
Kerugian/komplain.
Konfirmasi order, fitur koreksi/void terotorisasi, audit log.
Customer tidak paham QR Order
Hambatan adopsi.
Sediakan instruksi singkat dan opsi kasir manual.
Sistem terlalu kompleks
Sulit dipakai pekerja.
Prioritaskan POS, menu, order, pembayaran, laporan; modul tambahan bersifat modular.





17. Kriteria Penerimaan
Admin dapat menambahkan menu, harga, foto, dan status ketersediaan.
Customer tidak dapat memilih menu yang berstatus habis.
Kasir dapat membuat transaksi dan menghitung total/kembalian dengan benar.
QR Order dapat membuka order yang tepat ketika dipindai kasir.
Order memiliki nomor unik dan dapat dilacak statusnya.
Cash dan QRIS dapat dicatat dengan status pembayaran yang jelas.
Struk menampilkan nomor transaksi, waktu, item, qty, harga, total, dan metode pembayaran.
History dapat difilter berdasarkan tanggal/periode.
Dashboard menampilkan omzet dan menu terlaris berdasarkan transaksi yang tersimpan.
Void/cancel yang diizinkan tercatat dan tidak menghapus jejak transaksi.
Hak akses Customer, Kasir, dan Admin tidak saling melampaui.
Modul Dapur tidak diperlukan agar MVP dapat beroperasi.
Jika KDS diaktifkan, status order dapat berubah dari masuk → diproses → siap → selesai.
18. Pengembangan Lanjutan
Offline-first POS dengan sinkronisasi ketika koneksi pulih.
QRIS dinamis/payment gateway dengan callback/webhook jika dibutuhkan.
Inventori bahan baku berbasis resep/BOM dan food cost.
Supplier dan purchase order.
Loyalty, membership, dan voucher.
Multi-outlet/cabang.
Integrasi layanan pesan-antar pihak ketiga.
Accounting/laba rugi lebih lengkap.
KDS untuk outlet dengan proses dapur.
Analitik jam ramai dan prediksi kebutuhan stok.
Fitur pembayaran digital harus mempertimbangkan integrasi PJP/payment gateway dan status transaksi. Bank Indonesia menjelaskan QRIS sebagai standar QR pembayaran nasional, dengan MPM statis dan dinamis yang memiliki karakter penggunaan berbeda. 
19. Kesimpulan
BRD ini menetapkan sistem inti dengan tiga role utama: Admin/Owner, Kasir, dan Customer. Role Dapur tidak dijadikan kebutuhan wajib karena rancangan berangkat dari karakter operasional warung nasi Padang dengan lauk siap tersedia pada display. KDS tetap disediakan sebagai modul opsional untuk outlet yang memiliki kebutuhan persiapan makanan berbasis order.
Fokus MVP adalah memastikan siklus bisnis utama berjalan tanpa friksi: menu dan ketersediaan dikelola Admin → Customer atau Kasir membuat order → order memiliki identitas → pembayaran diselesaikan → struk dan history tercatat → data masuk ke dashboard/laporan. Pendekatan ini menjaga sistem tetap sederhana bagi pekerja, mudah dipahami pelanggan, dan tetap memiliki fondasi untuk pengembangan bisnis yang lebih besar.
Lampiran A — Catatan Validasi Lapangan
BRD ini menggunakan prinsip desain berbasis kebutuhan operasional F&B Indonesia dan membedakan antara kebutuhan yang relevan secara umum dengan asumsi yang harus divalidasi pada outlet target. Sistem POS, QR self-order, QRIS, laporan, inventory, dan KDS memang tersedia sebagai pola fitur pada produk F&B Indonesia saat ini; namun keberadaan suatu fitur pada produk komersial tidak berarti fitur tersebut wajib untuk setiap warung nasi Padang.
Sebelum implementasi final, lakukan observasi langsung minimal pada 2–3 warung nasi Padang dengan skala berbeda. Validasi khusus: cara kasir mencatat lauk, apakah pelanggan mengambil sendiri atau dilayani, apakah ada nomor meja, bagaimana takeaway diproses, apakah menu ditampilkan dengan harga, bagaimana pembayaran QRIS diverifikasi, dan apakah ada proses finishing makanan setelah order.
Dengan demikian, kita harus mepahami sebagai prinsip bahwa kebutuhan sistem tidak boleh diasumsikan seragam untuk semua warung. Variasi operasional antarwarung harus dikonfirmasi melalui observasi/interview langsung pada outlet sasaran.
Lampiran B — Glosarium
Istilah
Definisi
POS
Point of Sale; sistem untuk mencatat transaksi dan pembayaran.
QR Order
QR Code yang membuka/menautkan ke sesi atau pesanan customer.
QRIS
Quick Response Code Indonesian Standard, standar QR pembayaran yang ditetapkan Bank Indonesia. 
MPM
Merchant Presented Mode pada QRIS; merchant menampilkan QR untuk dipindai customer.
KDS
Kitchen Display System; layar yang menampilkan order untuk proses dapur.
Dine-in
Pesanan untuk makan di tempat.
Takeaway
Pesanan untuk dibawa pulang.
Void
Pembatalan/koreksi transaksi sesuai kewenangan yang ditetapkan.
Shift
Periode kerja kasir yang memiliki saldo awal dan rekonsiliasi akhir.
MVP
Minimum Viable Product; versi minimum yang memenuhi kebutuhan inti.





Referensi Dasar
Bank Indonesia — QRIS: standar QR Code pembayaran nasional dan model MPM/CPM. 
Bank Indonesia — informasi pendaftaran QRIS dan perkembangan penggunaan merchant. 
Restro — contoh modul POS, self-ordering, KDS, QRIS, role staff, dan laporan pada sistem F&B Indonesia. 
DapurOS — contoh integrasi POS, QR order, KDS, inventory, dan laporan
Tabia — contoh POS, offline-first, QR ordering, inventory, loyalty, dan dashboard. 

Sekian terimakasih ©R45YA
