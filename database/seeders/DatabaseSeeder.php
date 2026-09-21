<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@rasomandeh.com'],
            [
                'name' => 'Administrator Raso Mandeh',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 1. Seed Branches
        $branchesData = [
            [
                'nama' => 'Raso Mandeh - Jakarta Selatan',
                'kota' => 'Jakarta Selatan',
                'alamat' => 'Jl. Senopati No. 45, Kebayoran Baru, Jakarta Selatan',
                'jam_buka' => '09:00 - 22:00',
                'kontak_whatsapp' => '6281234567890',
                'is_active' => true,
            ],
            [
                'nama' => 'Raso Mandeh - Bandung',
                'kota' => 'Bandung',
                'alamat' => 'Jl. R.E. Martadinata (Riau) No. 112, Bandung',
                'jam_buka' => '09:00 - 22:00',
                'kontak_whatsapp' => '6281234567891',
                'is_active' => true,
            ],
            [
                'nama' => 'Raso Mandeh - Surabaya',
                'kota' => 'Surabaya',
                'alamat' => 'Jl. Manyar Kertoarjo No. 78, Gubeng, Surabaya',
                'jam_buka' => '09:00 - 22:00',
                'kontak_whatsapp' => '6281234567892',
                'is_active' => true,
            ],
            [
                'nama' => 'Raso Mandeh - Medan',
                'kota' => 'Medan',
                'alamat' => 'Jl. S. Parman No. 21, Petisah Tengah, Medan',
                'jam_buka' => '09:00 - 22:00',
                'kontak_whatsapp' => '6281234567893',
                'is_active' => true,
            ],
            [
                'nama' => 'Raso Mandeh - Palembang',
                'kota' => 'Palembang',
                'alamat' => 'Jl. Jend. Sudirman No. 15, Ilir Timur I, Palembang',
                'jam_buka' => '09:00 - 22:00',
                'kontak_whatsapp' => '6281234567894',
                'is_active' => true,
            ],
            [
                'nama' => 'Raso Mandeh - Bukittinggi',
                'kota' => 'Bukittinggi',
                'alamat' => 'Jl. Ahmad Yani No. 8, Pasar Atas, Bukittinggi',
                'jam_buka' => '08:00 - 22:00',
                'kontak_whatsapp' => '6281234567895',
                'is_active' => true,
            ],
        ];

        $branches = [];
        foreach ($branchesData as $bData) {
            $branches[] = Branch::firstOrCreate(
                ['nama' => $bData['nama']],
                $bData
            );
        }

        // Seed Tables for all created branches
        $this->call([
            TableSeeder::class,
        ]);

        // 2. Seed Menu Categories
        $categoriesMap = [
            'ayam' => 'Ayam & Olahan',
            'daging' => 'Daging Sapi',
            'ikan' => 'Ikan & Seafood',
            'sayur' => 'Sayur & Kuah',
            'topping' => 'Menu Tambahan',
            'minuman' => 'Minuman Tradisional',
            'nasi-padang' => 'Paket Nasi Padang',
        ];

        $categoryIds = [];
        foreach ($categoriesMap as $slug => $nama) {
            $cat = MenuCategory::firstOrCreate(
                ['slug' => $slug],
                ['nama' => $nama]
            );
            $categoryIds[$slug] = $cat->id;
        }

        // 3. Seed Menu Items
        $menuData = [
            // === AYAM ===
            [
                'nama' => 'Ayam Pop Istimewa',
                'kategori' => 'ayam',
                'deskripsi' => 'Ayam kampung muda dimasak air kelapa, digoreng kilat mentega, disajikan dengan sambal tomat cabe merah khas Bukittinggi.',
                'foto' => '/menu/nasi-padang-ayam-pop.webp',
                'badge' => 'Favorit',
                'rating' => 4.8,
                'base_price' => 32000,
            ],
            [
                'nama' => 'Ayam Bakar Bumbu Padang',
                'kategori' => 'ayam',
                'deskripsi' => 'Ayam bakar dengan lumuran bumbu rempah padang yang karamelisasi wangi arang batok kelapa asli.',
                'foto' => '/menu/nasi-padang-ayam-bakar.webp',
                'badge' => 'Favorit',
                'rating' => 4.8,
                'base_price' => 31000,
            ],
            [
                'nama' => 'Ayam Gulai Santan',
                'kategori' => 'ayam',
                'deskripsi' => 'Ayam kampung dimasak dalam kuah gulai santan kelapa murni dengan rempah kuning, serai, dan daun salam.',
                'foto' => '/menu/nasi-padang-ayam-gulai.webp',
                'badge' => null,
                'rating' => 4.7,
                'base_price' => 31000,
            ],
            [
                'nama' => 'Ayam Goreng Padang',
                'kategori' => 'ayam',
                'deskripsi' => 'Ayam goreng bumbu rempah Padang, renyah di luar dan juicy di dalam, cocok dengan nasi putih hangat.',
                'foto' => '/menu/nasi-padang-ayam-goreng.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 31000,
            ],
            // === IKAN ===
            [
                'nama' => 'Gulai Kepala Ikan Kakap',
                'kategori' => 'ikan',
                'deskripsi' => 'Kepala kakap merah segar dengan kuah gulai rempah kuning pekat, daun ruku-ruku, dan sensasi asam belimbing wuluh.',
                'foto' => '/menu/nasi-padang-gulai-kepala-kakap.webp',
                'badge' => 'Signature',
                'rating' => 4.9,
                'base_price' => 85000,
            ],
            [
                'nama' => 'Ikan Asam Padeh',
                'kategori' => 'ikan',
                'deskripsi' => 'Ikan segar bumbu asam padeh merah pedas tanpa santan, sangat kaya rempah serai dan daun kunyit.',
                'foto' => '/menu/nasi-pasang-ikan-asam-padeh.webp',
                'badge' => 'Baru',
                'rating' => 4.7,
                'base_price' => 32000,
            ],
            // === DAGING SAPI ===
            [
                'nama' => 'Rendang Daging Sapi',
                'kategori' => 'daging',
                'deskripsi' => 'Rendang daging sapi khas Payakumbuh dimasak perlahan selama 8 jam dengan kelapa kental & rempah warisan 1950.',
                'foto' => '/menu/nasi-padang-rendang.webp',
                'badge' => 'Signature',
                'rating' => 4.9,
                'base_price' => 35000,
            ],
            [
                'nama' => 'Dendeng Balado',
                'kategori' => 'daging',
                'deskripsi' => 'Daging sapi iris tipis digoreng kering lalu disiram sambal balado merah pedas manis yang menggugah selera.',
                'foto' => '/menu/nasi-padang-dendeng-balado.webp',
                'badge' => 'Favorit',
                'rating' => 4.8,
                'base_price' => 35000,
            ],
            [
                'nama' => 'Dendeng Batokok Lado Mudo',
                'kategori' => 'daging',
                'deskripsi' => 'Daging sapi iris tipis dipipihkan lalu dipanggang arang batok dan disiram sambal lado mudo hijau harum menggiurkan.',
                'foto' => '/menu/nasi-padang-dendeng-batokok.webp',
                'badge' => 'Signature',
                'rating' => 4.9,
                'base_price' => 35000,
            ],
            [
                'nama' => 'Paru Sapi Goreng',
                'kategori' => 'daging',
                'deskripsi' => 'Paru sapi empuk dimasak bumbu rempah Minang lalu digoreng kering, gurih dan kriuk sempurna.',
                'foto' => '/menu/nasi-padang-paru-sapi-goreng.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 30000,
            ],
            [
                'nama' => 'Gulai Tambusu',
                'kategori' => 'daging',
                'deskripsi' => 'Usus sapi diisi tahu dan telur, dimasak dalam kuah gulai santan kental rempah autentik khas Minang.',
                'foto' => '/menu/nasi-padang-gulai-tambusu.webp',
                'badge' => null,
                'rating' => 4.7,
                'base_price' => 32000,
            ],
            [
                'nama' => 'Gulai Tunjang Kikil',
                'kategori' => 'daging',
                'deskripsi' => 'Kikil sapi dimasak lunak dalam kuah gulai santan kuning dengan rempah lengkap dan cita rasa gurih mendalam.',
                'foto' => '/menu/nasi-padang-gulai-tunjang-kikil.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 35000,
            ],
            // === MENU TAMBAHAN (LAUK TAMBAHAN & EKSTRA) ===
            [
                'nama' => 'Tambahan Ayam Pop',
                'kategori' => 'topping',
                'deskripsi' => 'Sepotong ayam pop ekstra tanpa nasi.',
                'foto' => '/items/ayam-pop.webp',
                'badge' => null,
                'rating' => 4.8,
                'base_price' => 16000,
            ],
            [
                'nama' => 'Tambahan Ayam Bakar',
                'kategori' => 'topping',
                'deskripsi' => 'Sepotong ayam bakar bumbu padang ekstra tanpa nasi.',
                'foto' => '/items/ayam-bakar.webp',
                'badge' => null,
                'rating' => 4.8,
                'base_price' => 15000,
            ],
            [
                'nama' => 'Tambahan Ayam Gulai',
                'kategori' => 'topping',
                'deskripsi' => 'Sepotong ayam gulai ekstra tanpa nasi.',
                'foto' => '/items/ayam-gulai.webp',
                'badge' => null,
                'rating' => 4.7,
                'base_price' => 15000,
            ],
            [
                'nama' => 'Tambahan Ayam Goreng',
                'kategori' => 'topping',
                'deskripsi' => 'Sepotong ayam goreng ekstra tanpa nasi.',
                'foto' => '/items/ayam-goreng.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 15000,
            ],
            [
                'nama' => 'Tambahan Rendang',
                'kategori' => 'topping',
                'deskripsi' => 'Potongan rendang daging sapi ekstra tanpa nasi.',
                'foto' => '/items/rendang.webp',
                'badge' => 'Favorit',
                'rating' => 4.9,
                'base_price' => 17000,
            ],
            [
                'nama' => 'Tambahan Dendeng Balado',
                'kategori' => 'topping',
                'deskripsi' => 'Dendeng balado merah ekstra tanpa nasi.',
                'foto' => '/items/dendeng-balado.webp',
                'badge' => null,
                'rating' => 4.8,
                'base_price' => 17000,
            ],
            [
                'nama' => 'Tambahan Dendeng Batokok',
                'kategori' => 'topping',
                'deskripsi' => 'Dendeng batokok lado mudo ekstra tanpa nasi.',
                'foto' => '/items/dendeng-batokok.webp',
                'badge' => null,
                'rating' => 4.9,
                'base_price' => 17000,
            ],
            [
                'nama' => 'Tambahan Gulai Kepala Kakap',
                'kategori' => 'topping',
                'deskripsi' => 'Kepala kakap merah utuh tanpa nasi.',
                'foto' => '/items/kepala-ikan-kakap.webp',
                'badge' => 'Signature',
                'rating' => 4.9,
                'base_price' => 45000,
            ],
            [
                'nama' => 'Tambahan Ikan Asam Padeh',
                'kategori' => 'topping',
                'deskripsi' => 'Ikan asam padeh ekstra tanpa nasi.',
                'foto' => '/items/ikan-asam-padeh.webp',
                'badge' => null,
                'rating' => 4.7,
                'base_price' => 15000,
            ],
            [
                'nama' => 'Tambahan Gulai Tunjang/Kikil',
                'kategori' => 'topping',
                'deskripsi' => 'Kikil sapi ekstra tanpa nasi.',
                'foto' => '/items/gulai-tunjang-kikil.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 17000,
            ],
            [
                'nama' => 'Tambahan Gulai Tambusu',
                'kategori' => 'topping',
                'deskripsi' => 'Usus isi tahu telur ekstra tanpa nasi.',
                'foto' => '/items/gulai-tambusu.webp',
                'badge' => null,
                'rating' => 4.7,
                'base_price' => 16000,
            ],
            [
                'nama' => 'Tambahan Paru Sapi Goreng',
                'kategori' => 'topping',
                'deskripsi' => 'Paru sapi goreng ekstra tanpa nasi.',
                'foto' => '/items/paru-sapi.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 14000,
            ],
            [
                'nama' => 'Perkedel Kentang',
                'kategori' => 'topping',
                'deskripsi' => 'Perkedel kentang goreng renyah.',
                'foto' => '/items/perkedel.webp',
                'badge' => 'Favorit',
                'rating' => 4.7,
                'base_price' => 4000,
            ],
            [
                'nama' => 'Telur Dadar Padang',
                'kategori' => 'topping',
                'deskripsi' => 'Telur dadar barendo renyah khas Payakumbuh.',
                'foto' => '/items/telur-dadar.webp',
                'badge' => null,
                'rating' => 4.5,
                'base_price' => 6000,
            ],
            [
                'nama' => 'Sambal Ijo & Merah',
                'kategori' => 'sayur',
                'deskripsi' => 'Sambal cabe hijau dan merah ulek kasar.',
                'foto' => '/items/sambel-ijo-merah.webp',
                'badge' => 'Favorit',
                'rating' => 4.8,
                'base_price' => 3000,
            ],
            [
                'nama' => 'Daun Singkong Rebus',
                'kategori' => 'sayur',
                'deskripsi' => 'Pucuk daun singkong muda rebus ekstra.',
                'foto' => '/items/daun-singkong-rebus.webp',
                'badge' => null,
                'rating' => 4.5,
                'base_price' => 3000,
            ],
            [
                'nama' => 'Nangka Cubadak Gulai',
                'kategori' => 'sayur',
                'deskripsi' => 'Gulai nangka muda ekstra.',
                'foto' => '/items/nangka-cubadak.webp',
                'badge' => null,
                'rating' => 4.6,
                'base_price' => 3000,
            ],
            [
                'nama' => 'Teh Plastik Khas Minang',
                'kategori' => 'minuman',
                'deskripsi' => 'Teh manis dingin di plastik es.',
                'foto' => '/items/teh-plastik.webp',
                'badge' => null,
                'rating' => 4.8,
                'base_price' => 2000,
            ],
        ];

        $createdMenuItems = [];
        foreach ($menuData as $mData) {
            $basePrice = $mData['base_price'];
            unset($mData['base_price']);

            $kategoriSlug = $mData['kategori'] ?? null;
            unset($mData['kategori']);

            $item = MenuItem::firstOrCreate(
                ['nama' => $mData['nama']],
                array_merge($mData, [
                    'menu_category_id' => $categoryIds[$kategoriSlug] ?? null,
                    'is_active' => true,
                ])
            );
            $createdMenuItems[] = $item;

            // Seed pricing for all branches
            $allBranches = Branch::all();
            foreach ($allBranches as $branch) {
                $adj = ($branch->kota === 'Jakarta Selatan') ? 2000 : 0;
                BranchMenuPrice::firstOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'menu_item_id' => $item->id,
                    ],
                    [
                        'harga' => $basePrice + $adj,
                        'is_available' => true,
                    ]
                );
            }
        }

        // Backfill prices for any existing menu items and branches
        $allBranches = Branch::all();
        $allItems = MenuItem::all();
        foreach ($allBranches as $branch) {
            foreach ($allItems as $item) {
                BranchMenuPrice::firstOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'menu_item_id' => $item->id,
                    ],
                    [
                        'harga' => 30000,
                        'is_available' => true,
                    ]
                );
            }
        }

        // 3. Seed Reviews
        $reviewsData = [
            [
                'nama_pelanggan' => 'H. Ahmad Syukri, S.H.',
                'rating' => 5,
                'komentar' => 'Rendang dagingnya luar biasa otentik! Bumbu hitam meresap sampai ke serat terdalam daging. Mengingatkan masakan nenek di Bukittinggi.',
                'is_approved' => true,
                'branch_id' => $branches[0]->id,
            ],
            [
                'nama_pelanggan' => 'Siti Nurhaliza Putri',
                'rating' => 5,
                'komentar' => 'Ayam pop lembut luar biasa, sambal lado merah tomatnya juara. Suasana restorannya bersih dan nuansa Minang-nya terasa sangat elegan.',
                'is_approved' => true,
                'branch_id' => $branches[1]->id,
            ],
            [
                'nama_pelanggan' => 'Budi Santoso & Keluarga',
                'rating' => 5,
                'komentar' => 'Gulai kepala ikan kakapnya porsi mantap, kuahnya kental gurih asam segar. Wajib pesan Es Tebak untuk penutup. Pelayanan sangat cepat!',
                'is_approved' => true,
                'branch_id' => $branches[2]->id,
            ],
            [
                'nama_pelanggan' => 'dr. Dian Pratama',
                'rating' => 5,
                'komentar' => 'Dendeng batokok lado mudo terbaik di kota ini! Dagingnya empuk tidak keras, aroma asapnya harum. Langganan tetap setiap akhir pekan.',
                'is_approved' => true,
                'branch_id' => $branches[3]->id,
            ],
            [
                'nama_pelanggan' => 'Rendra Gunawan',
                'rating' => 4,
                'komentar' => 'Makanannya enak-enak, sambal ijonya mantap. Tempatnya nyaman untuk makan bersama keluarga.',
                'is_approved' => false,
                'branch_id' => $branches[0]->id,
            ],
        ];

        foreach ($reviewsData as $rData) {
            Review::create($rData);
        }

        // 4. Seed Sample Orders with Items
        $ordersData = [
            [
                'branch_id' => $branches[0]->id,
                'customer_name' => 'Faris Pratama',
                'customer_phone' => '081298765432',
                'method' => 'dine-in',
                'status' => 'process',
                'notes' => 'Meja 04, sambal ijo dipisah',
                'items' => [
                    ['item_index' => 0, 'qty' => 2, 'price' => 37000], // Rendang
                    ['item_index' => 1, 'qty' => 1, 'price' => 30000], // Ayam Pop
                    ['item_index' => 9, 'qty' => 2, 'price' => 20000], // Es Tebak
                ],
            ],
            [
                'branch_id' => $branches[0]->id,
                'customer_name' => 'Anisa Rahmawati',
                'customer_phone' => '081311223344',
                'method' => 'delivery',
                'status' => 'pending',
                'notes' => 'Gedung Menara Mandiri Lt. 12, mohon sambal merah ekstra',
                'items' => [
                    ['item_index' => 0, 'qty' => 3, 'price' => 37000], // Rendang
                    ['item_index' => 2, 'qty' => 2, 'price' => 36000], // Dendeng
                    ['item_index' => 10, 'qty' => 3, 'price' => 22000], // Es Teh Talua
                ],
            ],
            [
                'branch_id' => $branches[1]->id,
                'customer_name' => 'Hendro Wijaya',
                'customer_phone' => '085788990011',
                'method' => 'online',
                'status' => 'ready',
                'notes' => 'Akan diambil jam 12:30 WIB',
                'items' => [
                    ['item_index' => 3, 'qty' => 1, 'price' => 55000], // Gulai Kepala Kakap
                    ['item_index' => 7, 'qty' => 2, 'price' => 18000], // Sayur Kapau
                    ['item_index' => 11, 'qty' => 2, 'price' => 15000], // Es Kelapa
                ],
            ],
            [
                'branch_id' => $branches[2]->id,
                'customer_name' => 'Maya Indira',
                'customer_phone' => '082144556677',
                'method' => 'dine-in',
                'status' => 'confirmed',
                'notes' => 'Meja VIP 01, acara ulang tahun keluarga',
                'items' => [
                    ['item_index' => 0, 'qty' => 5, 'price' => 35000],
                    ['item_index' => 1, 'qty' => 5, 'price' => 28000],
                    ['item_index' => 6, 'qty' => 4, 'price' => 22000],
                    ['item_index' => 9, 'qty' => 8, 'price' => 18000],
                ],
            ],
            [
                'branch_id' => $branches[5]->id,
                'customer_name' => 'Bapak Zulkifli',
                'customer_phone' => '081900112233',
                'method' => 'dine-in',
                'status' => 'completed',
                'notes' => 'Rombongan wisata Bukittinggi',
                'items' => [
                    ['item_index' => 2, 'qty' => 4, 'price' => 34000],
                    ['item_index' => 4, 'qty' => 4, 'price' => 29000],
                    ['item_index' => 7, 'qty' => 4, 'price' => 18000],
                ],
            ],
            [
                'branch_id' => $branches[3]->id,
                'customer_name' => 'drg. Ratna Sari',
                'customer_phone' => '081277889900',
                'method' => 'delivery',
                'status' => 'completed',
                'notes' => 'Klinik Sehat Medika',
                'items' => [
                    ['item_index' => 0, 'qty' => 2, 'price' => 35000],
                    ['item_index' => 5, 'qty' => 2, 'price' => 32000],
                ],
            ],
        ];

        foreach ($ordersData as $oData) {
            $items = $oData['items'];
            unset($oData['items']);

            $total = 0;
            foreach ($items as $item) {
                $total += ($item['qty'] * $item['price']);
            }

            $order = Order::create(array_merge($oData, ['total' => $total]));

            foreach ($items as $item) {
                $menuItem = $createdMenuItems[$item['item_index']];
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);
            }
        }
    }
}
