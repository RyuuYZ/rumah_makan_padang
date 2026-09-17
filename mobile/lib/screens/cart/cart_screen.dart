import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../models/cart_item_model.dart';
import '../../providers/cart_provider.dart';
import '../../utils/currency_formatter.dart';

/// Halaman Keranjang Belanja persis mockup Figma "cart page":
/// - Header Marun: Ikon tas belanja emas, judul "Keranjang Pesanan", subjudul "Cabang: Jakarta Selatan", tombol tutup [X]
/// - Canvas putih dengan kartu item bernuansa krem lembut (#FAF5ED)
/// - Thumbnail makanan bulat melengkung, judul, harga merah, total harga, stepper minus/plus putih, dan ikon tempat sampah merah
/// - Bagian Bawah: Jumlah Menu (Porsi), Garis pembatas, Total Tagihan tebal, tombol utama "Konfirmasi Order Lanjut ke Pembayaran", dan tombol teks "Kosongkan Keranjang"
class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  void _handleClose(BuildContext context) {
    if (Navigator.of(context).canPop()) {
      context.pop();
    } else {
      context.go('/');
    }
  }

  void _showClearDialog(BuildContext context, CartProvider cart) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text(
          'Kosongkan Keranjang?',
          style: GoogleFonts.playfairDisplay(
            fontWeight: FontWeight.w700,
            fontSize: 18,
            color: const Color(0xFF301115),
          ),
        ),
        content: Text(
          'Seluruh pesanan makanan yang telah dipilih akan dihapus dari keranjang belanja.',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            color: const Color(0xFF7C7267),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(
              'Batal',
              style: GoogleFonts.plusJakartaSans(
                color: const Color(0xFF8C827A),
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFFBD141D),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            onPressed: () {
              cart.clearCart();
              Navigator.pop(ctx);
            },
            child: const Text('Hapus Semua'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();
    final items = cart.items;

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            // 1. TOP MAROON HEADER (Persis Figma)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
              color: const Color(0xFF4A141A),
              child: Row(
                children: [
                  // Gold Shopping Bag Icon Circle
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: const Color(0xFF5A1F25),
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: const Color(0xFFDF9C36).withAlpha(100),
                        width: 1,
                      ),
                    ),
                    alignment: Alignment.center,
                    child: const Icon(
                      Icons.shopping_bag_outlined,
                      color: Color(0xFFDF9C36),
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),

                  // Header Titles
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Keranjang Pesanan',
                          style: GoogleFonts.playfairDisplay(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Cabang: Jakarta Selatan',
                          style: GoogleFonts.plusJakartaSans(
                            color: const Color(0xFFD9CCC1),
                            fontSize: 11.5,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Close Button [X]
                  InkWell(
                    onTap: () => _handleClose(context),
                    borderRadius: BorderRadius.circular(20),
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      child: const Icon(
                        Icons.close_rounded,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // 2. BODY CONTENT (Cart Items or Empty State)
            Expanded(
              child: cart.isEmpty
                  ? _buildEmptyState(context)
                  : ListView.separated(
                      padding: const EdgeInsets.all(16),
                      itemCount: items.length,
                      separatorBuilder: (context, index) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final cartItem = items[index];
                        return _buildCartItemCard(context, cartItem, cart);
                      },
                    ),
            ),

            // 3. BOTTOM SUMMARY & CHECKOUT SECTION (Persis Figma)
            if (!cart.isEmpty)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 20),
                decoration: const BoxDecoration(
                  color: Color(0xFFF4EFEA),
                  border: Border(
                    top: BorderSide(color: Color(0xFFE8DFD5), width: 1),
                  ),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Jumlah Menu
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Jumlah Menu:',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12.5,
                            color: const Color(0xFF7C7267),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                        Text(
                          '${cart.totalItemCount} Porsi',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12.5,
                            color: const Color(0xFF2E1114),
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 10),
                    const Divider(color: Color(0xFFE5DDD3), height: 1, thickness: 1),
                    const SizedBox(height: 10),

                    // Total Tagihan
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Total Tagihan:',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF2E1114),
                          ),
                        ),
                        Text(
                          CurrencyFormatter.format(cart.subtotal),
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 17,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF8B1E22),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 16),

                    // Tombol Konfirmasi Order Lanjut ke Pembayaran
                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF4A141A),
                          foregroundColor: Colors.white,
                          elevation: 2,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onPressed: () => context.push('/payment'),
                        child: Text(
                          'Konfirmasi Order Lanjut ke Pembayaran',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 13.5,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),

                    const SizedBox(height: 12),

                    // Text Button: Kosongkan Keranjang
                    GestureDetector(
                      onTap: () => _showClearDialog(context, cart),
                      child: Text(
                        'Kosongkan Keranjang',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          color: const Color(0xFF8C827A),
                          fontWeight: FontWeight.w500,
                        ),
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

  // Card Item Keranjang (Persis Figma)
  Widget _buildCartItemCard(BuildContext context, CartItemModel cartItem, CartProvider cart) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFFAF5ED),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEFE7DD), width: 1),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Foto Makanan
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: SizedBox(
              width: 62,
              height: 62,
              child: Image.network(
                cartItem.menuItem.imageUrl,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => Container(
                  color: const Color(0xFFF0E5D8),
                  child: const Icon(Icons.restaurant, color: Color(0xFF8B1E22)),
                ),
              ),
            ),
          ),
          const SizedBox(width: 12),

          // Detail & Kontrol Item
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Judul & Total Item
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        cartItem.menuItem.name,
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w700,
                          fontSize: 13.5,
                          color: const Color(0xFF2E1114),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      CurrencyFormatter.format(cartItem.subtotal),
                      style: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w700,
                        fontSize: 12.5,
                        color: const Color(0xFF2E1114),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 2),

                // Harga Satuan
                Text(
                  CurrencyFormatter.format(cartItem.menuItem.price),
                  style: GoogleFonts.plusJakartaSans(
                    color: const Color(0xFF8B1E22),
                    fontSize: 11.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 8),

                // Stepper [- 1 +] & Tombol Hapus Sampah
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    // Stepper Kontrol
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.transparent,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Row(
                        children: [
                          // Minus Button
                          InkWell(
                            onTap: () => cart.decrementItem(cartItem.id),
                            borderRadius: BorderRadius.circular(6),
                            child: Container(
                              width: 26,
                              height: 26,
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(color: const Color(0xFFDFD5C8), width: 0.8),
                              ),
                              alignment: Alignment.center,
                              child: const Icon(
                                Icons.remove_rounded,
                                size: 14,
                                color: Color(0xFF4A3525),
                              ),
                            ),
                          ),

                          // Quantity Text
                          SizedBox(
                            width: 28,
                            child: Text(
                              '${cartItem.quantity}',
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w700,
                                fontSize: 13,
                                color: const Color(0xFF2E1114),
                              ),
                            ),
                          ),

                          // Plus Button
                          InkWell(
                            onTap: () => cart.incrementItem(cartItem.id),
                            borderRadius: BorderRadius.circular(6),
                            child: Container(
                              width: 26,
                              height: 26,
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(color: const Color(0xFFDFD5C8), width: 0.8),
                              ),
                              alignment: Alignment.center,
                              child: const Icon(
                                Icons.add_rounded,
                                size: 14,
                                color: Color(0xFF4A3525),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    // Trash Can Icon
                    InkWell(
                      onTap: () => cart.removeItem(cartItem.id),
                      borderRadius: BorderRadius.circular(16),
                      child: const Padding(
                        padding: EdgeInsets.all(4.0),
                        child: Icon(
                          Icons.delete_outline_rounded,
                          size: 19,
                          color: Color(0xFFE53935),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Tampilan Ketika Keranjang Masih Kosong
  Widget _buildEmptyState(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFFFAF5ED),
                border: Border.all(color: const Color(0xFFDF9C36), width: 1.5),
              ),
              child: const Icon(
                Icons.shopping_bag_outlined,
                size: 42,
                color: Color(0xFF4A141A),
              ),
            ),
            const SizedBox(height: 18),
            Text(
              'Keranjang Masih Kosong',
              style: GoogleFonts.playfairDisplay(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF301115),
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Aroma rendang dan gulai lezat menanti Anda.\nPilih menu favorit Anda sekarang!',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12.5,
                color: const Color(0xFF7C7267),
                height: 1.4,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4A141A),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              ),
              onPressed: () => context.go('/menu'),
              child: Text(
                'Pilih Menu Sekarang →',
                style: GoogleFonts.plusJakartaSans(
                  fontWeight: FontWeight.w700,
                  fontSize: 13,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
