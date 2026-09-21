import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:rasa_mandeh/models/menu_item_model.dart';
import 'package:rasa_mandeh/models/order_model.dart';
import 'package:rasa_mandeh/providers/auth_provider.dart';
import 'package:rasa_mandeh/providers/cart_provider.dart';
import 'package:rasa_mandeh/providers/menu_provider.dart';
import 'package:rasa_mandeh/providers/order_provider.dart';
import 'package:rasa_mandeh/screens/auth/login_screen.dart';
import 'package:rasa_mandeh/screens/auth/register_screen.dart';
import 'package:rasa_mandeh/screens/cart/cart_screen.dart';
import 'package:rasa_mandeh/screens/checkout/order_loading_screen.dart';
import 'package:rasa_mandeh/screens/checkout/payment_screen.dart';
import 'package:rasa_mandeh/screens/home/home_screen.dart';
import 'package:rasa_mandeh/routes/app_router.dart';
import 'package:rasa_mandeh/screens/menu/menu_detail_modal.dart';
import 'package:rasa_mandeh/screens/menu/menu_screen.dart';
import 'package:rasa_mandeh/screens/order/order_detail_screen.dart';
import 'package:rasa_mandeh/screens/order/order_history_screen.dart';
import 'package:rasa_mandeh/screens/splash_screen.dart';
import 'package:rasa_mandeh/services/auth_service.dart';
import 'package:rasa_mandeh/services/storage_service.dart';
import 'package:rasa_mandeh/utils/currency_formatter.dart';
import 'package:rasa_mandeh/utils/snackbar_helper.dart';
import 'package:rasa_mandeh/widgets/food_card.dart';
import 'package:rasa_mandeh/widgets/rm_logo.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  group('CurrencyFormatter Tests', () {
    test('format rupiah correctly without decimals', () {
      expect(CurrencyFormatter.format(28000), contains('28.000'));
      expect(CurrencyFormatter.format(0), contains('0'));
      expect(CurrencyFormatter.format(150000), contains('150.000'));
    });
  });

  group('CartProvider Business Logic Tests', () {
    late StorageService storageService;
    late CartProvider cartProvider;

    setUp(() async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      storageService = StorageService(prefs);
      cartProvider = CartProvider(storageService);
    });

    const testItem = MenuItemModel(
      id: 'test_1',
      name: 'Rendang Daging Sapi',
      category: 'Lauk Utama',
      description: 'Rendang empuk otentik',
      price: 28000,
      rating: 4.9,
      reviewCount: 100,
      imageUrl: 'https://example.com/rendang.jpg',
    );

    test('Initial cart is empty', () {
      expect(cartProvider.isEmpty, isTrue);
      expect(cartProvider.totalItemCount, 0);
      expect(cartProvider.subtotal, 0);
    });

    test('Add item to cart updates quantity and subtotal', () async {
      await cartProvider.addItem(testItem, quantity: 2);
      expect(cartProvider.items.length, 1);
      expect(cartProvider.totalItemCount, 2);
      expect(cartProvider.subtotal, 56000);
      expect(cartProvider.deliveryFee, 10000);
    });

    test('Applying voucher MANDEMURAH gives 15000 discount', () async {
      await cartProvider.addItem(testItem, quantity: 2); // 56000
      final applied = cartProvider.applyVoucher('MANDEMURAH');
      expect(applied, isTrue);
      expect(cartProvider.discount, 15000);
      expect(cartProvider.grandTotal, 56000 + 10000 + 2000 - 15000); // 53000
    });

    test('Free delivery applies when subtotal >= 75000', () async {
      await cartProvider.addItem(testItem, quantity: 3); // 84000
      expect(cartProvider.deliveryFee, 0);
    });

    test('Clearing cart resets all values', () async {
      await cartProvider.addItem(testItem, quantity: 1);
      await cartProvider.clearCart();
      expect(cartProvider.isEmpty, isTrue);
      expect(cartProvider.grandTotal, 0);
    });
  });

  group('OrderProvider Logic Tests', () {
    late StorageService storageService;
    late OrderProvider orderProvider;

    setUp(() async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      storageService = StorageService(prefs);
      orderProvider = OrderProvider(storageService);
    });

    test('Create new order successfully and updates status', () async {
      final order = await orderProvider.createOrder(
        items: [],
        deliveryMethod: 'delivery',
        deliveryAddress: 'Jl. Sudirman No. 1, Padang',
        paymentMethod: 'QRIS',
        notes: 'Sambal banyak',
        subtotal: 50000,
        deliveryFee: 10000,
        serviceFee: 2000,
        discount: 0,
        totalPrice: 62000,
      );

      expect(order.status, OrderStatus.confirmed);
      expect(orderProvider.orders.first.id, order.id);
      expect(order.timeline.isNotEmpty, true);
      expect(order.timeline.first.status, OrderStatus.confirmed);
      expect(order.timeline.first.title, 'Pesanan Diterima Dapur');

      // Kalkulasi dinamis ETA
      expect(order.estimatedArrivalTime.isAfter(order.createdAt), true);
      expect(order.remainingMinutes >= 0, true);
      expect(order.formattedEstimatedArrivalTime.contains('WIB'), true);

      // Advance stage to process
      await orderProvider.updateOrderStatus(order.id, OrderStatus.process);
      final updated = orderProvider.getOrderById(order.id);
      expect(updated?.status, OrderStatus.process);
      expect(updated?.timeline.length, 2);
      expect(updated?.timeline.last.status, OrderStatus.process);

      // Batalkan pesanan dengan alasan
      const reason = 'Ingin menambah atau mengubah pilihan menu';
      await orderProvider.cancelOrder(order.id, reason: reason);
      final cancelled = orderProvider.getOrderById(order.id);
      expect(cancelled?.status, OrderStatus.cancelled);
      expect(cancelled?.cancelReason, reason);
      expect(cancelled?.timeline.last.status, OrderStatus.cancelled);
      expect(cancelled?.timeline.last.description.contains(reason), true);
    });
  });

  group('Widget UI Tests', () {
    testWidgets('RmLogo renders brand name and monogram', (tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: RmLogo(
              size: 80,
              showText: true,
              showTagline: true,
            ),
          ),
        ),
      );

      expect(find.byType(Image), findsOneWidget);
      expect(find.text('Raso Mandeh'), findsOneWidget);
      expect(find.text('RUMAH MAKAN PADANG'), findsOneWidget);
      expect(find.text('“Rasa yang tak pulang tanpa diingat.”'), findsOneWidget);
    });

    testWidgets('SplashScreen renders Figma mockup design elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final auth = AuthService(storage);

      await tester.pumpWidget(
        ChangeNotifierProvider(
          create: (_) => AuthProvider(auth),
          child: const MaterialApp(
            home: SplashScreen(),
          ),
        ),
      );

      await tester.pump(const Duration(milliseconds: 200));

      expect(find.byType(Image), findsOneWidget);
      expect(find.text('Raso Mandeh'), findsOneWidget);
      expect(find.text('RUMAH MAKAN PADANG'), findsOneWidget);
      expect(find.text('“Rasa yang tak pulang tanpa diingat.”'), findsOneWidget);
    });

    testWidgets('FoodCard grid does not overflow on small constraints', (tester) async {
      const item = MenuItemModel(
        id: 'c1',
        name: 'Gulai Kepala Ikan Kakap Merah Jumbo',
        category: 'Ikan & Seafood',
        description: 'Kepala kakap asam pedas ruku-ruku',
        price: 48000,
        rating: 4.9,
        reviewCount: 310,
        imageUrl: 'https://example.com/fish.jpg',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: Center(
              child: SizedBox(
                width: 151.0,
                height: 204.8,
                child: FoodCard(
                  item: item,
                  onTap: () {},
                  onAddToCart: () {},
                ),
              ),
            ),
          ),
        ),
      );

      expect(find.text(item.name), findsOneWidget);
      expect(tester.takeException(), isNull); // No RenderFlex overflow!
    });

    testWidgets('RegisterScreen renders Figma mockup elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final auth = AuthService(storage);

      await tester.pumpWidget(
        ChangeNotifierProvider(
          create: (_) => AuthProvider(auth),
          child: const MaterialApp(
            home: RegisterScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('GRATIS SAMBAL LADO UNTUK PENDAFTARAN PERTAMA'), findsOneWidget);
      expect(find.text('GABUNG KELUARGA RASO'), findsOneWidget);
      expect(find.text('Mari bergabung, nikmati\nkelezatan autentik.'), findsOneWidget);
      expect(find.text('+62'), findsOneWidget);
      expect(find.text('Daftar Sekarang'), findsOneWidget);
      expect(find.text('Citarasa Asli Sabana Raso'), findsOneWidget);
    });

    testWidgets('LoginScreen renders Figma mockup elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final auth = AuthService(storage);

      await tester.pumpWidget(
        ChangeNotifierProvider(
          create: (_) => AuthProvider(auth),
          child: const MaterialApp(
            home: LoginScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('SELAMAT DATANG KEMBALI'), findsOneWidget);
      expect(find.text('Masuk & Nikmati Kelezatan\nNan Sabana Raso.'), findsOneWidget);
      expect(find.text('Nomor WhatsApp'), findsOneWidget);
      expect(find.text('Email'), findsOneWidget);
      expect(find.text('+62'), findsOneWidget);
      expect(find.text('Ingat Saya'), findsOneWidget);
      expect(find.text('Lupa Kata Sandi?'), findsOneWidget);
      expect(find.text('Masuk Sekarang'), findsOneWidget);
      expect(find.text('Masuk dengan Akun Google'), findsOneWidget);
      expect(find.text('“Citarasa Nan Sabana Raso”'), findsOneWidget);
    });

    testWidgets('HomeScreen renders Figma mockup elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider(create: (_) => CartProvider(storage)),
            ChangeNotifierProvider(create: (_) => MenuProvider()),
          ],
          child: const MaterialApp(
            home: HomeScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();
      expect(find.byType(Image), findsWidgets);
      expect(find.text('MASAKAN MINANG OTENTIK'), findsOneWidget);
      expect(find.text('Rasa yang\ntak pulang\ntanpa diingat.'), findsOneWidget);
      expect(find.text('Lihat Menu'), findsOneWidget);
      expect(find.text('Reservasi Meja'), findsOneWidget);
      expect(find.text('Rempah Segar'), findsOneWidget);
      expect(find.text('Resep Tradisional'), findsOneWidget);
      expect(find.text('Nasi Hangat'), findsOneWidget);
      expect(find.text('Rating 4.9 / 5'), findsOneWidget);
      expect(find.text('Yang paling dicari.'), findsOneWidget);
      expect(find.text('Rendang Daging'), findsOneWidget);
      expect(find.text('Nasi + Ayam Pop'), findsOneWidget);
      expect(find.text('Gulai Kepala Ikan'), findsOneWidget);
    });

    testWidgets('MenuScreen renders Figma mockup elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider(create: (_) => CartProvider(storage)),
            ChangeNotifierProvider(create: (_) => MenuProvider()),
          ],
          child: const MaterialApp(
            home: MenuScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('47 PILIHAN MENU'), findsOneWidget);
      expect(find.text('Pilih laukmu.'), findsOneWidget);
      expect(
        find.text('Semua menu disajikan sedap hari. Klik menu untuk melihat detail.'),
        findsOneWidget,
      );
      expect(find.text('Cari rendang, ayam pop, gulai cincang...'), findsOneWidget);
      expect(find.text('Semua'), findsOneWidget);
      expect(find.text('Paket'), findsOneWidget);
      expect(find.text('Daging'), findsOneWidget);
      expect(find.text('Ayam'), findsOneWidget);
      expect(find.text('Ikan'), findsOneWidget);
      expect(find.text('Sayur'), findsOneWidget);
      expect(find.text('Urutkan: Terlaris'), findsOneWidget);
      expect(find.text('Rendang Daging'), findsWidgets);
      expect(find.text('Paket Nasi Kapau'), findsWidgets);
    });

    testWidgets('CartScreen renders Figma mockup elements and handles quantity stepper', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final cartProvider = CartProvider(storage);

      // Tambahkan Ayam Goreng Padang (Rp 33.000) persis di mockup Figma
      const item = MenuItemModel(
        id: 'c_ayam',
        name: 'Ayam Goreng Padang',
        category: 'Ayam',
        description: 'Ayam goreng rempah bumbu lengkuas sangrai renyah',
        price: 33000,
        rating: 4.9,
        reviewCount: 520,
        imageUrl: 'https://example.com/ayam.jpg',
      );
      cartProvider.addItem(item);

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: cartProvider),
            ChangeNotifierProvider(create: (_) => MenuProvider()),
          ],
          child: const MaterialApp(
            home: CartScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      // Verifikasi Header persis Figma
      expect(find.text('Keranjang Pesanan'), findsOneWidget);
      expect(find.text('Cabang: Jakarta Selatan'), findsOneWidget);
      expect(find.byIcon(Icons.close_rounded), findsOneWidget);

      // Verifikasi Item Card persis Figma
      expect(find.text('Ayam Goreng Padang'), findsOneWidget);
      expect(find.text('Rp 33.000'), findsWidgets);
      expect(find.text('1'), findsOneWidget);
      expect(find.byIcon(Icons.delete_outline_rounded), findsOneWidget);

      // Verifikasi Ringkasan Bawah persis Figma
      expect(find.text('Jumlah Menu:'), findsOneWidget);
      expect(find.text('1 Porsi'), findsOneWidget);
      expect(find.text('Total Tagihan:'), findsOneWidget);
      expect(find.text('Konfirmasi Order Lanjut ke Pembayaran'), findsOneWidget);
      expect(find.text('Kosongkan Keranjang'), findsOneWidget);

      // Test stepper tombol tambah [+]
      await tester.tap(find.byIcon(Icons.add_rounded).first);
      await tester.pumpAndSettle();

      expect(cartProvider.totalItemCount, 2);
      expect(find.text('2 Porsi'), findsOneWidget);
      expect(find.text('Rp 66.000'), findsWidgets);
    });

    testWidgets('PaymentScreen renders Figma mockup elements and selects payment methods', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final cartProvider = CartProvider(storage);
      final authProvider = AuthProvider(AuthService(storage));
      final orderProvider = OrderProvider(storage);

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: cartProvider),
            ChangeNotifierProvider.value(value: authProvider),
            ChangeNotifierProvider.value(value: orderProvider),
            ChangeNotifierProvider(create: (_) => MenuProvider()),
          ],
          child: const MaterialApp(
            home: PaymentScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      // 1. Top Notice Bar & Header
      expect(find.text('PESANAN MASAK SEGAR & HANGAT'), findsOneWidget);
      expect(find.text('RASO MINANG'), findsOneWidget);
      expect(find.text('Checkout Pesanan'), findsOneWidget);
      expect(find.text('Langkah 2 dari 2 Pembayaran'), findsOneWidget);

      // 2. Card Alamat Pengantaran
      expect(find.text('Alamat Pengantaran'), findsOneWidget);
      expect(find.text('Ubah'), findsOneWidget);
      expect(find.textContaining('Kemang Raya'), findsOneWidget);
      expect(find.textContaining('pos satpam'), findsOneWidget);

      // 3. Card Rincian Menu Raso
      expect(find.text('Rincian Menu Raso'), findsOneWidget);
      expect(find.text('3 Item Dipesan'), findsOneWidget);
      expect(find.text('Rendang Daging Sapi Karamel'), findsOneWidget);
      expect(find.text('Paket Nasi Padang Komplit'), findsOneWidget);
      expect(find.text('Es Teh Talua Tradisional'), findsOneWidget);

      // 4. Card Metode Pembayaran
      expect(find.text('Metode Pembayaran'), findsOneWidget);
      expect(find.text('QRIS Instan (Semua Dompet Digital)'), findsOneWidget);
      expect(find.text('Virtual Account Bank'), findsOneWidget);
      expect(find.text('GoPay / ShopeePay Direct'), findsOneWidget);
      expect(find.text('Tunai saat Pengantaran (COD)'), findsOneWidget);

      // 5. Card Ringkasan Pembayaran
      expect(find.text('Ringkasan Pembayaran'), findsOneWidget);
      expect(find.text('Biaya Antar Kilat Panas'), findsOneWidget);
      expect(find.text('Pengemasan Daun Pisang & Higienis'), findsOneWidget);
      expect(find.text('Potongan Voucher Minang'), findsOneWidget);
      expect(find.text('Total Tagihan'), findsOneWidget);

      // 6. Bottom Sticky Bar
      expect(find.text('TOTAL PEMBAYARAN'), findsOneWidget);
      expect(find.text('Bayar Sekarang'), findsOneWidget);

      // Test memilih metode pembayaran Virtual Account Bank
      await tester.scrollUntilVisible(find.text('Virtual Account Bank'), 100);
      await tester.tap(find.text('Virtual Account Bank'));
      await tester.pumpAndSettle();

      expect(find.text('VA Terpilih'), findsOneWidget);
    });

    testWidgets('OrderLoadingScreen renders Figma mockup elements and progress bar', (tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: OrderLoadingScreen(orderId: 'ORD-12345'),
        ),
      );

      // Verifikasi status pill badge
      expect(find.text('SEDANG MEMPROSES ORDER'), findsOneWidget);

      // Verifikasi ilustrasi custom paint kurir motor
      expect(find.byType(CustomPaint), findsWidgets);

      // Pompa frame animasi beberapa milidetik
      await tester.pump(const Duration(milliseconds: 500));
      expect(find.text('SEDANG MEMPROSES ORDER'), findsOneWidget);
    });

    testWidgets('OrderDetailScreen renders Figma mockup elements', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final orderProvider = OrderProvider(storage);

      await tester.pumpWidget(
        ChangeNotifierProvider.value(
          value: orderProvider,
          child: const MaterialApp(
            home: OrderDetailScreen(orderId: '#RSO-88429'),
          ),
        ),
      );

      await tester.pumpAndSettle();

      // 1. Header & Order Number
      expect(find.text('Status Pesanan'), findsOneWidget);
      expect(find.text('#RSO-88429'), findsWidgets);
      expect(find.text('Salin'), findsOneWidget);

      // 2. Card Status Stepper & Estimasi Dinamis
      final sampleOrder = orderProvider.getOrderById('#RSO-88429')!;
      expect(find.text('SEDANG DISIAPKAN'), findsOneWidget);
      expect(find.text('ESTIMASI TIBA'), findsOneWidget);
      expect(find.text(sampleOrder.formattedRemainingTime), findsOneWidget);
      expect(find.text(sampleOrder.formattedEstimatedArrivalTime), findsOneWidget);
      expect(find.text('Pesanan Diterima Dapur'), findsOneWidget);
      expect(find.text('Sedang Dimasak & Dibungkus'), findsOneWidget);
      expect(find.text('Proses'), findsOneWidget);
      expect(find.text('Kurir Menjemput & Mengantar'), findsOneWidget);
      expect(find.text('Pesanan Tiba & Siap Disajikan'), findsOneWidget);
      expect(find.text('Batalkan Pesanan'), findsOneWidget);

      // 3. Card Live Tracking Map & Kurir
      expect(find.text('Pelacakan Langsung'), findsOneWidget);
      expect(find.text('Uda Hendra Kurniawan'), findsOneWidget);
      expect(find.text('Raso Express • Honda Vario B 4821 SOX'), findsOneWidget);
      expect(find.text('ALAMAT ANTAR'), findsOneWidget);
      expect(find.text('Jl. Kemang Raya No. 14, Jakarta Selatan'), findsOneWidget);

      // 4. Card Rincian Menu Dipesan
      expect(find.text('Rincian Menu Dipesan'), findsOneWidget);
      expect(find.text('Rendang Daging Sapi Karamel'), findsOneWidget);
      expect(find.text('Paket Nasi Padang Komplit'), findsOneWidget);
      expect(find.text('Es Teh Talua Tradisional'), findsOneWidget);
      expect(find.text('Rp 28.000'), findsOneWidget);
      expect(find.text('Rp 38.000'), findsOneWidget);
      expect(find.text('Rp 12.000'), findsOneWidget);

      // 5. Rincian Biaya & Total Pembayaran
      expect(find.text('Subtotal Makanan'), findsOneWidget);
      expect(find.text('Gratis (Promo Raso)'), findsOneWidget);
      expect(find.text('Biaya Layanan & Pengemasan'), findsOneWidget);
      expect(find.text('Total Pembayaran'), findsOneWidget);
      expect(find.text('QRIS BCA (Lunas • 12.18 WIB)'), findsOneWidget);
      expect(find.text('Rp 78.000'), findsWidgets);

      // 6. Action Buttons & Footer
      expect(find.text('Pesan Menu Lainnya'), findsOneWidget);
      expect(find.text('Struk Digital'), findsOneWidget);
      expect(find.text('“Rasa yang tak pulang tanpa diingat.”'), findsOneWidget);
    });

    testWidgets('RegisterScreen validates password length and confirmation match', (tester) async {
      tester.view.physicalSize = const Size(800, 1200);
      tester.view.devicePixelRatio = 1.0;
      addTearDown(() {
        tester.view.resetPhysicalSize();
        tester.view.resetDevicePixelRatio();
      });

      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final authService = AuthService(storage);
      final authProvider = AuthProvider(authService);

      await tester.pumpWidget(
        ChangeNotifierProvider.value(
          value: authProvider,
          child: const MaterialApp(
            home: RegisterScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Minimal 8 karakter'), findsOneWidget);
      expect(find.text('Ulangi kata sandi akun'), findsOneWidget);

      // Isi data dengan password pendek (< 8 karakter)
      final textFields = find.byType(TextFormField);
      await tester.enterText(textFields.at(0), 'Uda Syahril');
      await tester.enterText(textFields.at(1), '81234567890');
      await tester.enterText(textFields.at(2), 'syahril@gmail.com');
      await tester.enterText(textFields.at(3), 'Jl. Khatib Sulaiman No. 10 Padang');
      await tester.enterText(textFields.at(4), 'pendek'); // < 8 char
      await tester.enterText(textFields.at(5), 'pendek');

      await tester.tap(find.text('Daftar Sekarang'));
      await tester.pumpAndSettle();

      expect(find.text('Kata sandi minimal 8 karakter'), findsOneWidget);

      // Ubah password tidak cocok
      await tester.enterText(textFields.at(4), 'password123');
      await tester.enterText(textFields.at(5), 'passwordBeda');
      await tester.tap(find.text('Daftar Sekarang'));
      await tester.pumpAndSettle();

      expect(find.text('Kata sandi tidak cocok'), findsOneWidget);
    });

    testWidgets('OrderDetailScreen safely handles null or non-existent order', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final orderProvider = OrderProvider(storage);

      await tester.pumpWidget(
        ChangeNotifierProvider.value(
          value: orderProvider,
          child: const MaterialApp(
            home: OrderDetailScreen(orderId: 'ORDER_TIDAK_ADA'),
          ),
        ),
      );

      await tester.pumpAndSettle();
      expect(find.text('Status Pesanan'), findsOneWidget);
    });

    testWidgets('Responsive UI testing across Mobile Small, Large, Tablet, and Desktop', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final authService = AuthService(storage);
      final authProvider = AuthProvider(authService);
      final menuProvider = MenuProvider();
      final cartProvider = CartProvider(storage);
      final orderProvider = OrderProvider(storage);

      Widget createTestApp() {
        return MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: authProvider),
            ChangeNotifierProvider.value(value: menuProvider),
            ChangeNotifierProvider.value(value: cartProvider),
            ChangeNotifierProvider.value(value: orderProvider),
          ],
          child: const MaterialApp(
            home: HomeScreen(),
          ),
        );
      }

      // 1. Mobile Kecil: 360 x 640
      tester.view.physicalSize = const Size(360, 640);
      tester.view.devicePixelRatio = 1.0;
      await tester.pumpWidget(createTestApp());
      await tester.pumpAndSettle();
      expect(tester.takeException(), isNull);

      // 2. Mobile Besar: 414 x 896
      tester.view.physicalSize = const Size(414, 896);
      tester.view.devicePixelRatio = 1.0;
      await tester.pumpWidget(createTestApp());
      await tester.pumpAndSettle();
      expect(tester.takeException(), isNull);

      // 3. Tablet: 768 x 1024
      tester.view.physicalSize = const Size(768, 1024);
      tester.view.devicePixelRatio = 1.0;
      await tester.pumpWidget(createTestApp());
      await tester.pumpAndSettle();
      expect(tester.takeException(), isNull);

      // 4. Desktop / Landscape: 1280 x 800
      tester.view.physicalSize = const Size(1280, 800);
      tester.view.devicePixelRatio = 1.0;
      await tester.pumpWidget(createTestApp());
      await tester.pumpAndSettle();
      expect(tester.takeException(), isNull);

      // Reset tester view size
      addTearDown(() {
        tester.view.resetPhysicalSize();
        tester.view.resetDevicePixelRatio();
      });
    });

    testWidgets('OrderHistoryScreen renders active and completed tabs properly', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final orderProvider = OrderProvider(storage);
      final cartProvider = CartProvider(storage);

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: orderProvider),
            ChangeNotifierProvider.value(value: cartProvider),
          ],
          child: const MaterialApp(
            home: OrderHistoryScreen(),
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Riwayat Pesanan'), findsOneWidget);
      expect(find.textContaining('Pesanan Aktif'), findsOneWidget);
      expect(find.textContaining('Selesai'), findsOneWidget);

      // Tap tab Selesai
      await tester.tap(find.textContaining('Selesai'));
      await tester.pumpAndSettle();

      expect(find.text('Belum Ada Riwayat Pesanan'), findsOneWidget);
    });

    testWidgets('PaymentScreen prevents race-condition on rapid double clicks', (tester) async {
      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final orderProvider = OrderProvider(storage);
      final cartProvider = CartProvider(storage);
      final authService = AuthService(storage);
      final authProvider = AuthProvider(authService);

      final router = GoRouter(
        initialLocation: '/payment',
        routes: [
          GoRoute(
            path: '/payment',
            builder: (context, state) => const PaymentScreen(),
          ),
          GoRoute(
            path: '/order-loading/:id',
            builder: (context, state) => const Scaffold(body: Text('Order Loading')),
          ),
        ],
      );

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: authProvider),
            ChangeNotifierProvider.value(value: orderProvider),
            ChangeNotifierProvider.value(value: cartProvider),
          ],
          child: MaterialApp.router(
            routerConfig: router,
          ),
        ),
      );

      await tester.pumpAndSettle();

      // Tombol Bayar Sekarang
      final payButton = find.text('Bayar Sekarang');
      expect(payButton, findsOneWidget);

      final initialOrderCount = orderProvider.orders.length;

      // Double-tap cepat
      await tester.tap(payButton);
      await tester.tap(payButton);
      await tester.pump(const Duration(milliseconds: 100));

      // Tunggu hingga selesai
      await tester.pumpAndSettle();

      // Jumlah order bertambah tepat 1, tidak terjadi duplikasi ganda
      expect(orderProvider.orders.length, initialOrderCount + 1);
    });

    testWidgets('MenuDetailModal adds item and shows cart snackbar with functioning action and auto-dismiss', (tester) async {
      tester.view.physicalSize = const Size(1080, 2400);
      tester.view.devicePixelRatio = 1.0;
      addTearDown(tester.view.resetPhysicalSize);

      SharedPreferences.setMockInitialValues({});
      final prefs = await SharedPreferences.getInstance();
      final storage = StorageService(prefs);
      final cartProvider = CartProvider(storage);
      final authProvider = AuthProvider(AuthService(storage));
      final menuProvider = MenuProvider();
      final orderProvider = OrderProvider(storage);

      const testItem = MenuItemModel(
        id: 'r1',
        name: 'Rendang Daging',
        category: 'Lauk Utama',
        description: 'Daging sapi pilihan empuk gurih',
        price: 28000,
        rating: 4.9,
        reviewCount: 1240,
        imageUrl: 'https://example.com/rendang.jpg',
      );

      await tester.pumpWidget(
        MultiProvider(
          providers: [
            ChangeNotifierProvider.value(value: authProvider),
            ChangeNotifierProvider.value(value: menuProvider),
            ChangeNotifierProvider.value(value: orderProvider),
            ChangeNotifierProvider.value(value: cartProvider),
          ],
          child: MaterialApp.router(
            routerConfig: appRouter,
            scaffoldMessengerKey: rootScaffoldMessengerKey,
          ),
        ),
      );
      await tester.pumpAndSettle();

      final context = rootNavigatorKey.currentContext!;
      MenuDetailModal.show(context, testItem);
      await tester.pumpAndSettle();

      expect(find.text('Rendang Daging'), findsOneWidget);
      expect(find.text('Tambah • Rp 28.000'), findsOneWidget);

      // Tekan tombol Tambah
      await tester.tap(find.text('Tambah • Rp 28.000'));
      await tester.pumpAndSettle();

      // Modal harus tertutup dan item bertambah di keranjang
      expect(cartProvider.totalItemCount, 1);

      // SnackBar harus muncul dengan pesan dan tombol 'Lihat Keranjang'
      expect(find.text('1 x Rendang Daging berhasil ditambahkan!'), findsOneWidget);
      final actionButton = find.text('Lihat Keranjang');
      expect(actionButton, findsOneWidget);

      // Tekan tombol 'Lihat Keranjang', tidak boleh melempar error dan membuka CartScreen
      await tester.tap(actionButton);
      await tester.pumpAndSettle();

      expect(tester.takeException(), isNull);
      expect(find.text('Keranjang Pesanan'), findsOneWidget);
    });

    testWidgets('SnackBarHelper cart snackbar auto-dismisses after duration', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          scaffoldMessengerKey: rootScaffoldMessengerKey,
          home: const Scaffold(
            body: Center(child: Text('Test Page')),
          ),
        ),
      );
      await tester.pumpAndSettle();

      SnackBarHelper.showCartSnackBar(
        message: '1 x Rendang Daging berhasil ditambahkan!',
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      expect(find.text('1 x Rendang Daging berhasil ditambahkan!'), findsOneWidget);
      expect(find.text('Lihat Keranjang'), findsOneWidget);

      // Tunggu durasi berakhir
      await tester.pump(const Duration(seconds: 4));
      await tester.pumpAndSettle();

      // SnackBar harus otomatis hilang karena persist: false
      expect(find.text('1 x Rendang Daging berhasil ditambahkan!'), findsNothing);
      expect(find.text('Lihat Keranjang'), findsNothing);
    });
  });
}
