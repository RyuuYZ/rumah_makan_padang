import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../models/menu_item_model.dart';
import '../../providers/cart_provider.dart';
import '../../providers/menu_provider.dart';
import '../menu/menu_detail_modal.dart';

/// Halaman Beranda (Home Page) persis sesuai mockup Figma
class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  void _showReservationDialog(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(22)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(22),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.table_restaurant_rounded, color: Color(0xFF5A1920)),
                  const SizedBox(width: 8),
                  Text(
                    'Reservasi Meja Raso Mandeh',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF331317),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Text(
                'Nikmati jamuan prasmanan Minang bersama keluarga besar atau rekan bisnis Anda.',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12.5,
                  color: const Color(0xFF7C6C64),
                ),
              ),
              const SizedBox(height: 18),
              ListTile(
                contentPadding: EdgeInsets.zero,
                leading: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFE8DD),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.people_outline, color: Color(0xFF5A1920)),
                ),
                title: Text('Jumlah Tamu', style: GoogleFonts.plusJakartaSans(fontSize: 13.5, fontWeight: FontWeight.w700)),
                subtitle: Text('2 - 20 Orang (Meja Panjang Gadang)', style: GoogleFonts.plusJakartaSans(fontSize: 11.5)),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF5A1920),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(24),
                    ),
                  ),
                  onPressed: () {
                    Navigator.pop(ctx);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Permintaan reservasi telah dikirim ke staf Raso Mandeh!'),
                        backgroundColor: Color(0xFF5A1920),
                      ),
                    );
                  },
                  child: Text(
                    'Konfirmasi Reservasi Sekarang',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 13.5,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();
    final menuProvider = context.watch<MenuProvider>();
    final allItems = menuProvider.allItems;

    // Menu Unggulan Pilihan Sesuai Mockup Figma:
    // 1. Rendang Daging
    // 2. Nasi + Ayam Pop
    // 3. Gulai Kepala Ikan
    final rendangItem = allItems.firstWhere(
      (e) => e.name.contains('Rendang Daging'),
      orElse: () => allItems.first,
    );
    final ayamPopItem = allItems.firstWhere(
      (e) => e.name.contains('Ayam Pop'),
      orElse: () => allItems[1],
    );
    final gulaiIkanItem = allItems.firstWhere(
      (e) => e.name.contains('Gulai Kepala Ikan'),
      orElse: () => allItems[2],
    );

    final featuredItems = [
      _FeaturedMenuData(
        item: rendangItem,
        displayName: 'Rendang Daging',
        tag: 'Lauk Utama',
        priceDisplay: 'Rp 28.000',
        ratingDisplay: '★ 4.9 (724 ulasan)',
      ),
      _FeaturedMenuData(
        item: ayamPopItem,
        displayName: 'Nasi + Ayam Pop',
        tag: 'Paket Nasi',
        priceDisplay: 'Rp 35.000',
        ratingDisplay: '★ 4.9',
      ),
      _FeaturedMenuData(
        item: gulaiIkanItem,
        displayName: 'Gulai Kepala Ikan',
        tag: 'Gulai',
        priceDisplay: 'Rp 38.000',
        ratingDisplay: '★ 4.8',
      ),
    ];

    const ivoryBackground = Color(0xFFFAF7F2);

    return Scaffold(
      backgroundColor: ivoryBackground,
      body: SafeArea(
        top: false,
        child: Column(
          children: [
            // 1. Top Notice Announcement Bar (Persis Mockup Figma)
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(
                top: MediaQuery.of(context).padding.top + 6,
                bottom: 8,
                left: 16,
                right: 16,
              ),
              color: const Color(0xFF4A141A),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.arrow_left_rounded,
                    color: Color(0xFFD4AF37),
                    size: 16,
                  ),
                  const SizedBox(width: 4),
                  Flexible(
                    child: Text(
                      'GRATIS SAMBAL LADO UNTUK PEMBELIAN KALI INI: PADANG SIGNATURE',
                      style: GoogleFonts.plusJakartaSans(
                        color: Colors.white,
                        fontSize: 9.5,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 0.6,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),

            // 2. Main Scrollable Content
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Top App Header
                    Padding(
                      padding: const EdgeInsets.fromLTRB(18, 12, 18, 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          // Left Logo + Brand (Official Website Logo)
                          Image.asset(
                            'assets/images/logo_rm.png',
                            height: 38,
                            fit: BoxFit.contain,
                          ),

                          // Right Actions: Search & Cart Button
                          Row(
                            children: [
                              // Search circle button
                              InkWell(
                                onTap: () => context.go('/menu'),
                                borderRadius: BorderRadius.circular(20),
                                child: Container(
                                  width: 36,
                                  height: 36,
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    shape: BoxShape.circle,
                                    border: Border.all(color: const Color(0xFFE5DFD7)),
                                    boxShadow: [
                                      BoxShadow(
                                        color: Colors.black.withAlpha(8),
                                        blurRadius: 4,
                                      ),
                                    ],
                                  ),
                                  child: const Icon(
                                    Icons.search_rounded,
                                    color: Color(0xFF4A141A),
                                    size: 18,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),

                              // Cart circle button with red notification badge
                              InkWell(
                                onTap: () => context.push('/cart'),
                                borderRadius: BorderRadius.circular(20),
                                child: Stack(
                                  clipBehavior: Clip.none,
                                  children: [
                                    Container(
                                      width: 36,
                                      height: 36,
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        shape: BoxShape.circle,
                                        border: Border.all(color: const Color(0xFFE5DFD7)),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black.withAlpha(8),
                                            blurRadius: 4,
                                          ),
                                        ],
                                      ),
                                      child: const Icon(
                                        Icons.shopping_bag_outlined,
                                        color: Color(0xFF4A141A),
                                        size: 18,
                                      ),
                                    ),
                                    if (cart.totalItemCount > 0)
                                      Positioned(
                                        top: -3,
                                        right: -3,
                                        child: Container(
                                          padding: const EdgeInsets.all(3),
                                          decoration: const BoxDecoration(
                                            color: Color(0xFFC0151E),
                                            shape: BoxShape.circle,
                                          ),
                                          constraints: const BoxConstraints(
                                            minWidth: 15,
                                            minHeight: 15,
                                          ),
                                          child: Text(
                                            '${cart.totalItemCount}',
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontSize: 8.5,
                                              fontWeight: FontWeight.w800,
                                            ),
                                            textAlign: TextAlign.center,
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),

                    // 3. Hero Featured Banner Card (Persis Mockup Figma)
                    Container(
                      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                      constraints: const BoxConstraints(minHeight: 330),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(22),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withAlpha(20),
                            blurRadius: 14,
                            offset: const Offset(0, 5),
                          ),
                        ],
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(22),
                        child: Stack(
                          children: [
                            // Background Food Image
                            Positioned.fill(
                              child: Image.network(
                                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1000',
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) => Container(
                                  color: const Color(0xFF4A141A),
                                ),
                              ),
                            ),

                            // Gradient Overlay for readability
                            Positioned.fill(
                              child: Container(
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    colors: [
                                      Colors.black.withAlpha(210),
                                      Colors.black.withAlpha(140),
                                      Colors.transparent,
                                    ],
                                    begin: Alignment.centerLeft,
                                    end: Alignment.centerRight,
                                  ),
                                ),
                              ),
                            ),

                            // Hero Content
                            Padding(
                              padding: const EdgeInsets.all(20),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  // Top Tag: MASAKAN MINANG OTENTIK
                                  Text(
                                    'MASAKAN MINANG OTENTIK',
                                    style: GoogleFonts.plusJakartaSans(
                                      color: const Color(0xFFE8D5B5),
                                      fontSize: 10,
                                      fontWeight: FontWeight.w700,
                                      letterSpacing: 1.5,
                                    ),
                                  ),
                                  const SizedBox(height: 8),

                                  // Hero Title: Rasa yang tak pulang tanpa diingat.
                                  Text(
                                    'Rasa yang\ntak pulang\ntanpa diingat.',
                                    style: GoogleFonts.playfairDisplay(
                                      fontSize: 25,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                      height: 1.15,
                                    ),
                                  ),
                                  const SizedBox(height: 8),

                                  // Hero Subtitle
                                  SizedBox(
                                    width: 230,
                                    child: Text(
                                      'Racikan rempah pilihan warisan turun-temurun. Dari rendang kayu bakar hingga gulai kepala kakap menggugah selera.',
                                      style: GoogleFonts.plusJakartaSans(
                                        color: Colors.white.withAlpha(215),
                                        fontSize: 11,
                                        height: 1.4,
                                      ),
                                      maxLines: 3,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                  const SizedBox(height: 16),

                                  // Action Buttons: [Lihat Menu →] [Reservasi Meja]
                                  Wrap(
                                    spacing: 10,
                                    runSpacing: 8,
                                    crossAxisAlignment: WrapCrossAlignment.center,
                                    children: [
                                      // Lihat Menu Button
                                      ElevatedButton(
                                        onPressed: () => context.go('/menu'),
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: const Color(0xFFDF9C36),
                                          foregroundColor: const Color(0xFF331418),
                                          elevation: 0,
                                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                                          shape: RoundedRectangleBorder(
                                            borderRadius: BorderRadius.circular(20),
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Text(
                                              'Lihat Menu',
                                              style: GoogleFonts.plusJakartaSans(
                                                fontSize: 11.5,
                                                fontWeight: FontWeight.w700,
                                              ),
                                            ),
                                            const SizedBox(width: 4),
                                            const Icon(Icons.arrow_forward_rounded, size: 14),
                                          ],
                                        ),
                                      ),

                                      // Reservasi Meja Button
                                      OutlinedButton(
                                        onPressed: () => _showReservationDialog(context),
                                        style: OutlinedButton.styleFrom(
                                          backgroundColor: Colors.black.withAlpha(60),
                                          side: const BorderSide(color: Colors.white60, width: 1),
                                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                                          shape: RoundedRectangleBorder(
                                            borderRadius: BorderRadius.circular(20),
                                          ),
                                        ),
                                        child: Text(
                                          'Reservasi Meja',
                                          style: GoogleFonts.plusJakartaSans(
                                            fontSize: 11.5,
                                            fontWeight: FontWeight.w600,
                                            color: Colors.white,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 14),

                                  // Status buka
                                  Row(
                                    children: [
                                      Container(
                                        width: 7,
                                        height: 7,
                                        decoration: const BoxDecoration(
                                          color: Color(0xFF4CAF50),
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                      const SizedBox(width: 6),
                                      Flexible(
                                        child: Text(
                                          'Buka sekarang · Tutup 22.00',
                                          style: GoogleFonts.plusJakartaSans(
                                            color: Colors.white.withAlpha(220),
                                            fontSize: 10.5,
                                            fontWeight: FontWeight.w500,
                                          ),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),

                    // 4. 2x2 Feature Cards Grid (Persis Mockup Figma)
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: _buildFeatureCard(
                                  icon: '🌶️',
                                  title: 'Rempah Segar',
                                  subtitle: 'Giling langsung tiap hari',
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildFeatureCard(
                                  icon: '🍲',
                                  title: 'Resep Tradisional',
                                  subtitle: 'Warisan 3 generasi',
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
                            children: [
                              Expanded(
                                child: _buildFeatureCard(
                                  icon: '🍚',
                                  title: 'Nasi Hangat',
                                  subtitle: 'Beras Solok pulen alami',
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildFeatureCard(
                                  icon: '⭐',
                                  title: 'Rating 4.9 / 5',
                                  subtitle: '1.200+ ulasan',
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12),

                    // 5. Section: "Yang paling dicari."
                    Padding(
                      padding: const EdgeInsets.fromLTRB(18, 12, 18, 12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'PILIHAN HARI INI',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFFB71C1C),
                              letterSpacing: 1.2,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Yang paling dicari.',
                            style: GoogleFonts.playfairDisplay(
                              fontSize: 25,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF301115),
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Tiga menu yang paling sering membuat lidah pelanggan rindu pulang.',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              color: const Color(0xFF7C6C64),
                            ),
                          ),
                        ],
                      ),
                    ),

                    // 6. Tiga Menu Unggulan Foto Besar Penuh (Persis Mockup Figma)
                    ...featuredItems.map((f) => _buildLargeFoodCard(context, f, cart)),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Feature Card Widget (2x2)
  Widget _buildFeatureCard({
    required String icon,
    required String title,
    required String subtitle,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFECE6DC)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 34,
            height: 34,
            decoration: const BoxDecoration(
              color: Color(0xFFF6EFE5),
              shape: BoxShape.circle,
            ),
            alignment: Alignment.center,
            child: Text(icon, style: const TextStyle(fontSize: 16)),
          ),
          const SizedBox(width: 10),
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
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  subtitle,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    color: const Color(0xFF8C7D75),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Large Food Card Widget Persis Mockup
  Widget _buildLargeFoodCard(
    BuildContext context,
    _FeaturedMenuData data,
    CartProvider cart,
  ) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      constraints: const BoxConstraints(minHeight: 200),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(18),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onTap: () => MenuDetailModal.show(context, data.item),
            child: Stack(
              children: [
                // Food Photo
                Positioned.fill(
                  child: Image.network(
                    data.item.imageUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => Container(
                      color: const Color(0xFF331317),
                    ),
                  ),
                ),

                // Dark Bottom Gradient
                Positioned.fill(
                  child: Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          Colors.transparent,
                          Colors.black.withAlpha(100),
                          Colors.black.withAlpha(220),
                        ],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                  ),
                ),

                // Content Overlay
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      // Badge Kategori (Mustard Gold Pill)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFFDF9C36),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          data.tag,
                          style: GoogleFonts.plusJakartaSans(
                            color: const Color(0xFF2E1114),
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                      const SizedBox(height: 6),

                      // Food Title (Playfair Display)
                      Text(
                        data.displayName,
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 21,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 6),

                      // Bottom Row: Price & Rating + Quick Add
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            data.priceDisplay,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.end,
                              children: [
                                Flexible(
                                  child: Text(
                                    data.ratingDisplay,
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11.5,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.white.withAlpha(230),
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    textAlign: TextAlign.end,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                InkWell(
                                  onTap: () {
                                    cart.addItem(data.item);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                        content: Text('${data.displayName} ditambahkan ke keranjang'),
                                        backgroundColor: const Color(0xFF5A1920),
                                        duration: const Duration(seconds: 1),
                                      ),
                                    );
                                  },
                                  borderRadius: BorderRadius.circular(15),
                                  child: Container(
                                    padding: const EdgeInsets.all(5),
                                    decoration: const BoxDecoration(
                                      color: Color(0xFFDF9C36),
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(
                                      Icons.add_rounded,
                                      size: 16,
                                      color: Color(0xFF2E1114),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _FeaturedMenuData {
  final MenuItemModel item;
  final String displayName;
  final String tag;
  final String priceDisplay;
  final String ratingDisplay;

  _FeaturedMenuData({
    required this.item,
    required this.displayName,
    required this.tag,
    required this.priceDisplay,
    required this.ratingDisplay,
  });
}
