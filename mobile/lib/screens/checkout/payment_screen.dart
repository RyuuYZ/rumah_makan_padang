import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../models/cart_item_model.dart';
import '../../models/menu_item_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/order_provider.dart';
import '../../utils/currency_formatter.dart';

/// Halaman Checkout & Pembayaran persis mockup Figma "payment page":
/// - Top Notice Bar: PESANAN MASAK SEGAR & HANGAT | RASO MINANG
/// - Navigation Header: Tombol kembali, Checkout Pesanan, Langkah 2 dari 2 Pembayaran
/// - Card 1: Alamat Pengantaran (Nama, No HP, Alamat lengkap, Tombol Ubah, Kotak catatan satpam)
/// - Card 2: Rincian Menu Raso (Badge "3 Item Dipesan", foto masakan, deskripsi rempah, kuantitas & harga)
/// - Card 3: Metode Pembayaran (QRIS Instan aktif dengan radio tebal, Virtual Account, E-Wallet, COD)
/// - Card 4: Ringkasan Pembayaran (Subtotal, Biaya Antar Kilat Panas, Pengemasan Daun Pisang, Voucher Minang, Total Tagihan, Jaminan masakan hangat)
/// - Bottom Sticky Bar: TOTAL PEMBAYARAN, nominal tebal, status metode, tombol "Bayar Sekarang [Gembok]"
class PaymentScreen extends StatefulWidget {
  const PaymentScreen({super.key});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  String _selectedPaymentMethod = 'QRIS';
  bool _isProcessing = false;

  // Data alamat pengantaran sesuai Figma
  String _recipientName = 'Bpk. Rizal Arifin';
  String _recipientPhone = '+62 812-8901-234';
  String _deliveryAddress =
      'Jl. Kemang Raya No. 14, RT 02 / RW 07, Mampang Prapatan, Jakarta Selatan, 12730';
  String _deliveryNote =
      'Catatan: Titip di pos satpam depan pagar hitam jika gerbang tertutup.';

  // Item fallback jika keranjang dibuka langsung tanpa isi sebelumnya
  static const List<Map<String, dynamic>> _defaultFigmaItems = [
    {
      'name': 'Rendang Daging Sapi Karamel',
      'subtitle': 'Khas Payakumbuh masak 8 jam',
      'qty': 1,
      'price': 28000,
      'imageUrl': 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400',
    },
    {
      'name': 'Paket Nasi Padang Komplit',
      'subtitle': 'Gulai Cincang, Sayur Nangka & Sambal Hijau',
      'qty': 1,
      'price': 38000,
      'imageUrl': 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400',
    },
    {
      'name': 'Es Teh Talua Tradisional',
      'subtitle': 'Kocokan telur itik khas Minang & jeruk nipis',
      'qty': 1,
      'price': 12000,
      'imageUrl': 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400',
    },
  ];

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthProvider>().user;
    if (user != null && user.address.isNotEmpty) {
      _recipientName = user.name;
      _recipientPhone = user.phone;
      _deliveryAddress = user.address;
    }
  }

  void _showEditAddressDialog() {
    final nameCtrl = TextEditingController(text: _recipientName);
    final phoneCtrl = TextEditingController(text: _recipientPhone);
    final addrCtrl = TextEditingController(text: _deliveryAddress);
    final noteCtrl = TextEditingController(text: _deliveryNote);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(
          left: 20,
          right: 20,
          top: 20,
          bottom: MediaQuery.of(ctx).viewInsets.bottom + 20,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Ubah Alamat Pengantaran',
                  style: GoogleFonts.playfairDisplay(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF301115),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close_rounded, size: 20, color: Color(0xFF8C827A)),
                  onPressed: () => Navigator.pop(ctx),
                ),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: nameCtrl,
              decoration: const InputDecoration(
                labelText: 'Nama Penerima',
                border: OutlineInputBorder(),
                isDense: true,
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: phoneCtrl,
              decoration: const InputDecoration(
                labelText: 'Nomor WhatsApp / HP',
                border: OutlineInputBorder(),
                isDense: true,
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: addrCtrl,
              maxLines: 2,
              decoration: const InputDecoration(
                labelText: 'Alamat Lengkap',
                border: OutlineInputBorder(),
                isDense: true,
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: noteCtrl,
              decoration: const InputDecoration(
                labelText: 'Catatan untuk Kurir',
                border: OutlineInputBorder(),
                isDense: true,
              ),
            ),
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              height: 46,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF4A141A),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onPressed: () {
                  setState(() {
                    _recipientName = nameCtrl.text.trim();
                    _recipientPhone = phoneCtrl.text.trim();
                    _deliveryAddress = addrCtrl.text.trim();
                    _deliveryNote = noteCtrl.text.trim();
                  });
                  Navigator.pop(ctx);
                },
                child: const Text('Simpan Perubahan'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _processPayment() async {
    if (_isProcessing) return;
    setState(() => _isProcessing = true);

    try {
      final cart = context.read<CartProvider>();
      final orderProvider = context.read<OrderProvider>();

      List<CartItemModel> orderItems;

      if (cart.isEmpty) {
        // Jika keranjang kosong saat checkout langsung, gunakan default Figma
        orderItems = _defaultFigmaItems.map((f) {
          final item = MenuItemModel(
            id: f['name'].toString().toLowerCase().replaceAll(' ', '_'),
            name: f['name'] as String,
            category: 'Utama',
            description: f['subtitle'] as String,
            price: f['price'] as int,
            rating: 4.9,
            reviewCount: 300,
            imageUrl: f['imageUrl'] as String,
          );
          return CartItemModel(
            id: 'item_${item.id}',
            menuItem: item,
            quantity: f['qty'] as int,
          );
        }).toList();
      } else {
        orderItems = List.from(cart.items);
      }

      final subtotal = orderItems.fold(0, (acc, item) => acc + item.subtotal);
      const deliveryFee = 12000;
      const packagingFee = 3000;
      const discount = 15000;
      final total = subtotal + deliveryFee + packagingFee - discount;

      final order = await orderProvider.createOrder(
        items: orderItems,
        deliveryMethod: 'delivery',
        deliveryAddress: '$_deliveryAddress (Penerima: $_recipientName, $_recipientPhone)',
        paymentMethod: _selectedPaymentMethod,
        notes: _deliveryNote,
        subtotal: subtotal,
        deliveryFee: deliveryFee,
        serviceFee: packagingFee,
        discount: discount,
        totalPrice: total > 0 ? total : 0,
      );

      if (!cart.isEmpty) {
        await cart.clearCart();
      }

      if (mounted) {
        context.go('/order-loading/${order.id}');
      }
    } finally {
      if (mounted) {
        setState(() => _isProcessing = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();
    final orderProvider = context.watch<OrderProvider>();

    final bool useCartItems = !cart.isEmpty;
    final int itemCount = useCartItems ? cart.items.length : _defaultFigmaItems.length;
    final int subtotal = useCartItems
        ? cart.subtotal
        : _defaultFigmaItems.fold(0, (acc, item) => acc + (item['price'] as int) * (item['qty'] as int));

    const int deliveryFee = 12000;
    const int packagingFee = 3000;
    const int discount = 15000;
    final int grandTotal = (subtotal + deliveryFee + packagingFee - discount).clamp(0, 99999999);

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: Column(
          children: [
            // 1. TOP NOTICE MAROON BAR
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 7),
              color: const Color(0xFF4A141A),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Icon(
                        Icons.local_fire_department_rounded,
                        color: Color(0xFFDF9C36),
                        size: 14,
                      ),
                      const SizedBox(width: 6),
                      Text(
                        'PESANAN MASAK SEGAR & HANGAT',
                        style: GoogleFonts.plusJakartaSans(
                          color: Colors.white,
                          fontSize: 9.5,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 0.8,
                        ),
                      ),
                    ],
                  ),
                  Text(
                    'RASO MINANG',
                    style: GoogleFonts.playfairDisplay(
                      color: const Color(0xFFDF9C36),
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.0,
                    ),
                  ),
                ],
              ),
            ),

            // 2. APP BAR / NAVIGATION HEADER
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 10),
              child: Row(
                children: [
                  // Back Button
                  InkWell(
                    onTap: () => context.pop(),
                    borderRadius: BorderRadius.circular(20),
                    child: Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: const Color(0xFFE8DFD5), width: 1),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withAlpha(8),
                            blurRadius: 4,
                            offset: const Offset(0, 1),
                          ),
                        ],
                      ),
                      alignment: Alignment.center,
                      child: const Icon(
                        Icons.arrow_back_rounded,
                        size: 18,
                        color: Color(0xFF301115),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),

                  // Header Titles
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Checkout Pesanan',
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 18,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF301115),
                        ),
                      ),
                      Text(
                        'Langkah 2 dari 2 Pembayaran',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: const Color(0xFF7C7267),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // 3. SCROLLABLE BODY CONTENT
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(16, 4, 16, 20),
                child: Column(
                  children: [
                    // CARD 1: ALAMAT PENGANTARAN
                    _buildAddressCard(),
                    const SizedBox(height: 12),

                    // CARD 2: RINCIAN MENU RASO
                    _buildMenuDetailsCard(useCartItems, cart, itemCount),
                    const SizedBox(height: 12),

                    // CARD 3: METODE PEMBAYARAN
                    _buildPaymentMethodsCard(),
                    const SizedBox(height: 12),

                    // CARD 4: RINGKASAN PEMBAYARAN
                    _buildSummaryCard(itemCount, subtotal, deliveryFee, packagingFee, discount, grandTotal),
                  ],
                ),
              ),
            ),

            // 4. BOTTOM STICKY BAR
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(20, 10, 20, 14),
              decoration: const BoxDecoration(
                color: Color(0xFFFAF7F2),
                border: Border(top: BorderSide(color: Color(0xFFE8DFD5), width: 1)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Total Info
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        'TOTAL PEMBAYARAN',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 9,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 0.8,
                          color: const Color(0xFF8C827A),
                        ),
                      ),
                      const SizedBox(height: 1),
                      Text(
                        CurrencyFormatter.format(grandTotal),
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 18,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF2E1114),
                        ),
                      ),
                      Text(
                        '$_selectedPaymentMethod Terpilih',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10,
                          color: const Color(0xFF8C827A),
                        ),
                      ),
                    ],
                  ),

                  // Button Bayar Sekarang
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF4A141A),
                      foregroundColor: Colors.white,
                      elevation: 2,
                      padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                    onPressed: (orderProvider.isLoading || _isProcessing) ? null : _processPayment,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        if (orderProvider.isLoading || _isProcessing)
                          const SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation(Colors.white),
                            ),
                          )
                        else ...[
                          Text(
                            'Bayar Sekarang',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(width: 6),
                          const Icon(
                            Icons.lock_outline_rounded,
                            size: 15,
                            color: Colors.white,
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- WIDGET KOMPONEN CARD 1: ALAMAT PENGANTARAN ---
  Widget _buildAddressCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEDE5DC), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Row Header Alamat
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: const Color(0xFFFAF5ED),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: const Color(0xFFEFE7DD), width: 1),
                ),
                alignment: Alignment.center,
                child: const Icon(
                  Icons.location_on_rounded,
                  color: Color(0xFF4A141A),
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Alamat Pengantaran',
                      style: GoogleFonts.playfairDisplay(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: const Color(0xFF2E1114),
                      ),
                    ),
                    const SizedBox(height: 1),
                    Text(
                      '$_recipientName ($_recipientPhone)',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        color: const Color(0xFF7C7267),
                      ),
                    ),
                  ],
                ),
              ),
              InkWell(
                onTap: _showEditAddressDialog,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                  child: Text(
                    'Ubah',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 11.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF8B1E22),
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Detail Alamat
          Padding(
            padding: const EdgeInsets.only(left: 42),
            child: Text(
              _deliveryAddress,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 11.5,
                color: const Color(0xFF4A3F35),
                height: 1.35,
              ),
            ),
          ),
          const SizedBox(height: 10),

          // Kotak Catatan
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
            decoration: BoxDecoration(
              color: const Color(0xFFFAF5ED),
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: const Color(0xFFEFE7DD), width: 1),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(
                  Icons.edit_note_rounded,
                  color: Color(0xFFDF9C36),
                  size: 16,
                ),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    _deliveryNote,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 10.5,
                      color: const Color(0xFF6E6259),
                      height: 1.3,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // --- WIDGET KOMPONEN CARD 2: RINCIAN MENU RASO ---
  Widget _buildMenuDetailsCard(bool useCartItems, CartProvider cart, int itemCount) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEDE5DC), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(
                    Icons.restaurant_menu_rounded,
                    color: Color(0xFF4A141A),
                    size: 18,
                  ),
                  const SizedBox(width: 6),
                  Text(
                    'Rincian Menu Raso',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 14.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF2E1114),
                    ),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFFF4EFEA),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE8DFD5), width: 1),
                ),
                child: Text(
                  '$itemCount Item Dipesan',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF7C7267),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Daftar Item Masakan
          if (useCartItems) ...[
            ...cart.items.map((cartItem) => _buildSingleMenuItemRow(
                  name: cartItem.menuItem.name,
                  subtitle: cartItem.menuItem.description,
                  qty: cartItem.quantity,
                  price: cartItem.subtotal,
                  imageUrl: cartItem.menuItem.imageUrl,
                )),
          ] else ...[
            ..._defaultFigmaItems.map((f) => _buildSingleMenuItemRow(
                  name: f['name'] as String,
                  subtitle: f['subtitle'] as String,
                  qty: f['qty'] as int,
                  price: f['price'] as int,
                  imageUrl: f['imageUrl'] as String,
                )),
          ],
        ],
      ),
    );
  }

  Widget _buildSingleMenuItemRow({
    required String name,
    required String subtitle,
    required int qty,
    required int price,
    required String imageUrl,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Foto Menu
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: SizedBox(
              width: 48,
              height: 48,
              child: Image.network(
                imageUrl,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => Container(
                  color: const Color(0xFFFAF5ED),
                  child: const Icon(Icons.restaurant, color: Color(0xFF4A141A), size: 22),
                ),
              ),
            ),
          ),
          const SizedBox(width: 10),

          // Nama & Deskripsi
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: GoogleFonts.playfairDisplay(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF2E1114),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  subtitle,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    color: const Color(0xFF8C827A),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  '${qty}x',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10.5,
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF8C827A),
                  ),
                ),
              ],
            ),
          ),

          // Harga
          Text(
            CurrencyFormatter.format(price),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12.5,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF8B1E22),
            ),
          ),
        ],
      ),
    );
  }

  // --- WIDGET KOMPONEN CARD 3: METODE PEMBAYARAN ---
  Widget _buildPaymentMethodsCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEDE5DC), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(
                Icons.account_balance_wallet_rounded,
                color: Color(0xFF4A141A),
                size: 18,
              ),
              const SizedBox(width: 6),
              Text(
                'Metode Pembayaran',
                style: GoogleFonts.playfairDisplay(
                  fontSize: 14.5,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF2E1114),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // 1. QRIS Instan (Selected di mockup)
          _buildPaymentOptionItem(
            id: 'QRIS',
            title: 'QRIS Instan (Semua Dompet Digital)',
            subtitle: 'BCA, GoPay, OVO, ShopeePay, Dana, LinkAja',
            icon: Icons.qr_code_2_rounded,
          ),
          const SizedBox(height: 8),

          // 2. Virtual Account Bank
          _buildPaymentOptionItem(
            id: 'VA',
            title: 'Virtual Account Bank',
            subtitle: 'BCA, Mandiri, BRI, BNI (Otomatis verifikasi)',
            icon: Icons.account_balance_rounded,
          ),
          const SizedBox(height: 8),

          // 3. GoPay / ShopeePay Direct
          _buildPaymentOptionItem(
            id: 'E-Wallet',
            title: 'GoPay / ShopeePay Direct',
            subtitle: 'Saldo terhubung otomatis',
            icon: Icons.phone_android_rounded,
          ),
          const SizedBox(height: 8),

          // 4. Tunai saat Pengantaran (COD)
          _buildPaymentOptionItem(
            id: 'COD',
            title: 'Tunai saat Pengantaran (COD)',
            subtitle: 'Siapkan uang pas saat kurir tiba',
            icon: Icons.payments_outlined,
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentOptionItem({
    required String id,
    required String title,
    required String subtitle,
    required IconData icon,
  }) {
    final bool isSelected = _selectedPaymentMethod == id;

    return InkWell(
      onTap: () {
        setState(() {
          _selectedPaymentMethod = id;
        });
      },
      borderRadius: BorderRadius.circular(12),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? const Color(0xFF4A141A) : const Color(0xFFEDE5DC),
            width: isSelected ? 1.6 : 1.0,
          ),
        ),
        child: Row(
          children: [
            // Ikon Kotak
            Container(
              width: 34,
              height: 34,
              decoration: BoxDecoration(
                color: const Color(0xFFFAF5ED),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: const Color(0xFFEFE7DD), width: 0.8),
              ),
              alignment: Alignment.center,
              child: Icon(
                icon,
                color: const Color(0xFF4A141A),
                size: 18,
              ),
            ),
            const SizedBox(width: 10),

            // Judul & Keterangan
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF2E1114),
                    ),
                  ),
                  Text(
                    subtitle,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      color: const Color(0xFF7C7267),
                    ),
                  ),
                ],
              ),
            ),

            // Radio Button
            if (isSelected)
              Container(
                width: 20,
                height: 20,
                decoration: const BoxDecoration(
                  color: Color(0xFF4A141A),
                  shape: BoxShape.circle,
                ),
                alignment: Alignment.center,
                child: const Icon(
                  Icons.check_rounded,
                  color: Colors.white,
                  size: 14,
                ),
              )
            else
              Container(
                width: 20,
                height: 20,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: const Color(0xFFD4C9BD), width: 1.5),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // --- WIDGET KOMPONEN CARD 4: RINGKASAN PEMBAYARAN ---
  Widget _buildSummaryCard(
    int itemCount,
    int subtotal,
    int deliveryFee,
    int packagingFee,
    int discount,
    int grandTotal,
  ) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEDE5DC), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Ringkasan Pembayaran',
            style: GoogleFonts.playfairDisplay(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF2E1114),
            ),
          ),
          const SizedBox(height: 12),

          // Subtotal
          _buildSummaryRow('Subtotal Hidangan ($itemCount Menu)', CurrencyFormatter.format(subtotal)),
          const SizedBox(height: 6),

          // Biaya Antar
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Text(
                    'Biaya Antar Kilat Panas',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      color: const Color(0xFF4A3F35),
                    ),
                  ),
                  const SizedBox(width: 4),
                  const Icon(Icons.info_outline_rounded, size: 13, color: Color(0xFF8C827A)),
                ],
              ),
              Text(
                CurrencyFormatter.format(deliveryFee),
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  color: const Color(0xFF4A3F35),
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),

          // Pengemasan Daun Pisang
          _buildSummaryRow('Pengemasan Daun Pisang & Higienis', CurrencyFormatter.format(packagingFee)),
          const SizedBox(height: 6),

          // Potongan Voucher Minang
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Potongan Voucher Minang',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xFFDF9C36),
                ),
              ),
              Text(
                '- ${CurrencyFormatter.format(discount)}',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFFDF9C36),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(color: Color(0xFFEDE5DC), height: 1),
          const SizedBox(height: 12),

          // Total Tagihan Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Total Tagihan',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF2E1114),
                    ),
                  ),
                  Text(
                    'Termasuk pajak restoran 10%',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 9.5,
                      color: const Color(0xFF8C827A),
                    ),
                  ),
                ],
              ),
              Text(
                CurrencyFormatter.format(grandTotal),
                style: GoogleFonts.playfairDisplay(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF2E1114),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Box Jaminan Garansi Panas
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
            decoration: BoxDecoration(
              color: const Color(0xFFFAF5ED),
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: const Color(0xFFEFE7DD), width: 1),
            ),
            child: Row(
              children: [
                const Icon(
                  Icons.verified_rounded,
                  color: Color(0xFF7A6016),
                  size: 16,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'Jaminan masakan hangat sampai tujuan atau garansi ganti hidangan baru.',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 9.5,
                      color: const Color(0xFF6E6259),
                      height: 1.3,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            color: const Color(0xFF4A3F35),
          ),
        ),
        Text(
          value,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 12,
            color: const Color(0xFF4A3F35),
          ),
        ),
      ],
    );
  }
}
