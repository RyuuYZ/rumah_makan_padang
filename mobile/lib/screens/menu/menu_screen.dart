import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../models/menu_item_model.dart';
import '../../providers/cart_provider.dart';
import '../../providers/menu_provider.dart';
import '../../theme/app_colors.dart';
import '../../widgets/food_card.dart';
import 'menu_detail_modal.dart';

/// Halaman Menu persis mockup Figma "menu page":
/// - Top bar: Logo RM monogram, teks Raso / RUMAH MAKAN MINANG, dan tombol keranjang berbadge merah
/// - Heading: 47 PILIHAN MENU, "Pilih laukmu.", subtitle "Semua menu disajikan sedap hari..."
/// - Search bar: Pill rounded dengan ikon kaca pembesar
/// - Category chips: Semua, Paket, Daging, Ayam, Ikan, Sayur
/// - Sort selector: Urutkan: Terlaris ▼
/// - Grid 2 kolom: Kartu menu otentik lengkap dengan badge, wishlist heart, rating, deskripsi, harga, dan tombol tambah
class MenuScreen extends StatefulWidget {
  const MenuScreen({super.key});

  @override
  State<MenuScreen> createState() => _MenuScreenState();
}

class _MenuScreenState extends State<MenuScreen> {
  final TextEditingController _searchController = TextEditingController();

  static const List<String> _categories = [
    'Semua',
    'Paket',
    'Daging',
    'Ayam',
    'Ikan',
    'Sayur',
    'Minuman',
  ];

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _showSortSheet(BuildContext context) {
    final menuProvider = context.read<MenuProvider>();

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Urutkan Sajian',
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
              const SizedBox(height: 8),
              ...SortOption.values.map(
                (opt) => ListTile(
                  title: Text(
                    opt.label,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14,
                      fontWeight: menuProvider.selectedSort == opt ? FontWeight.w700 : FontWeight.w500,
                      color: menuProvider.selectedSort == opt
                          ? const Color(0xFF8B1E22)
                          : const Color(0xFF301115),
                    ),
                  ),
                  trailing: menuProvider.selectedSort == opt
                      ? const Icon(Icons.check_circle_rounded, color: Color(0xFF8B1E22))
                      : null,
                  onTap: () {
                    menuProvider.setSortOption(opt);
                    Navigator.pop(ctx);
                  },
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
    final menuProvider = context.watch<MenuProvider>();
    final cartProvider = context.watch<CartProvider>();
    final items = menuProvider.filteredItems;
    final cartCount = cartProvider.totalItemCount;

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. TOP APP BAR (Persis Figma)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 4, 16, 4),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Logo RM & Brand Name (Official Website Logo)
                  Image.asset(
                    'assets/images/logo_rm.png',
                    height: 34,
                    fit: BoxFit.contain,
                  ),

                  // Shopping Cart Icon Button with Red Badge
                  InkWell(
                    onTap: () => context.push('/cart'),
                    borderRadius: BorderRadius.circular(18),
                    child: Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: const Color(0xFFE8DFD5), width: 1),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withAlpha(10),
                            blurRadius: 4,
                            offset: const Offset(0, 1),
                          ),
                        ],
                      ),
                      child: Stack(
                        alignment: Alignment.center,
                        children: [
                          const Icon(
                            Icons.shopping_bag_outlined,
                            color: Color(0xFF301115),
                            size: 18,
                          ),
                          if (cartCount > 0)
                            Positioned(
                              top: 3,
                              right: 3,
                              child: Container(
                                padding: const EdgeInsets.all(2),
                                decoration: const BoxDecoration(
                                  color: Color(0xFFBD141D),
                                  shape: BoxShape.circle,
                                ),
                                constraints: const BoxConstraints(minWidth: 15, minHeight: 15),
                                alignment: Alignment.center,
                                child: Text(
                                  '$cartCount',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 8.5,
                                    fontWeight: FontWeight.bold,
                                    height: 1,
                                  ),
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // 2. HEADING SECTION
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 2, 16, 6),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '47 PILIHAN MENU',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 9.5,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.1,
                      color: const Color(0xFF8B1E22),
                    ),
                  ),
                  const SizedBox(height: 1),
                  Text(
                    'Pilih laukmu.',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 21,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF2E1114),
                      letterSpacing: -0.4,
                    ),
                  ),
                  const SizedBox(height: 1),
                  Text(
                    'Semua menu disajikan sedap hari. Klik menu untuk melihat detail.',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 11,
                      color: const Color(0xFF7C7267),
                      height: 1.25,
                    ),
                  ),
                ],
              ),
            ),

            // 3. SEARCH BAR
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Container(
                height: 38,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFE8DFD5), width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withAlpha(6),
                      blurRadius: 4,
                      offset: const Offset(0, 1),
                    ),
                  ],
                ),
                child: TextField(
                  controller: _searchController,
                  onChanged: (val) => menuProvider.setSearchQuery(val),
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    color: const Color(0xFF301115),
                  ),
                  decoration: InputDecoration(
                    border: InputBorder.none,
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(vertical: 9),
                    prefixIcon: const Icon(
                      Icons.search_rounded,
                      color: Color(0xFF9E9186),
                      size: 18,
                    ),
                    hintText: 'Cari rendang, ayam pop, gulai cincang...',
                    hintStyle: GoogleFonts.plusJakartaSans(
                      fontSize: 11.5,
                      color: const Color(0xFF9E9186),
                    ),
                    suffixIcon: _searchController.text.isNotEmpty
                        ? IconButton(
                            padding: EdgeInsets.zero,
                            icon: const Icon(Icons.close_rounded, size: 16, color: Color(0xFF9E9186)),
                            onPressed: () {
                              _searchController.clear();
                              menuProvider.setSearchQuery('');
                            },
                          )
                        : null,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 6),

            // 4. CATEGORY CHIPS (Horizontal Scroll)
            SizedBox(
              height: 32,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: _categories.length,
                separatorBuilder: (context, index) => const SizedBox(width: 6),
                itemBuilder: (context, index) {
                  final cat = _categories[index];
                  final isSelected = menuProvider.selectedCategory.toLowerCase() == cat.toLowerCase();

                  return GestureDetector(
                    onTap: () => menuProvider.setCategory(cat),
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
                      decoration: BoxDecoration(
                        color: isSelected ? const Color(0xFF4A141A) : const Color(0xFFF4EFEA),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: isSelected ? const Color(0xFF4A141A) : const Color(0xFFE8DFD5),
                          width: 1,
                        ),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        cat,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11.5,
                          fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                          color: isSelected ? Colors.white : const Color(0xFF4A3F35),
                        ),
                      ),
                    ),
                  );
                },
              ),
            ),

            const SizedBox(height: 6),

            // 5. SORT DROPDOWN SELECTOR BAR (Persis Figma)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: InkWell(
                onTap: () => _showSortSheet(context),
                borderRadius: BorderRadius.circular(10),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFE8DFD5), width: 1),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withAlpha(5),
                        blurRadius: 3,
                        offset: const Offset(0, 1),
                      ),
                    ],
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Urutkan: ${menuProvider.selectedSort.label}',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11.5,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF301115),
                        ),
                      ),
                      const Icon(
                        Icons.arrow_drop_down,
                        color: Color(0xFF301115),
                        size: 18,
                      ),
                    ],
                  ),
                ),
              ),
            ),

            const SizedBox(height: 6),

            // 6. 2-COLUMN MENU GRID
            Expanded(
              child: items.isEmpty
                  ? Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24.0),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.search_off_rounded, size: 56, color: Color(0xFF9E9186)),
                            const SizedBox(height: 12),
                            Text(
                              'Menu tidak ditemukan',
                              style: GoogleFonts.playfairDisplay(
                                fontSize: 18,
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF301115),
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              'Coba gunakan kata kunci lain atau ubah kategori menu',
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 12.5,
                                color: const Color(0xFF7C7267),
                              ),
                            ),
                            const SizedBox(height: 16),
                            ElevatedButton(
                              onPressed: () {
                                _searchController.clear();
                                menuProvider.setSearchQuery('');
                                menuProvider.setCategory('Semua');
                              },
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF4A141A),
                                foregroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                              ),
                              child: const Text('Reset Pencarian'),
                            ),
                          ],
                        ),
                      ),
                    )
                  : GridView.builder(
                      padding: const EdgeInsets.fromLTRB(16, 4, 16, 20),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 0.69,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                      ),
                      itemCount: items.length,
                      itemBuilder: (context, index) {
                        final item = items[index];
                        return FoodCard(
                          item: item,
                          isFavorite: menuProvider.isFavorite(item.id),
                          onToggleFavorite: () => menuProvider.toggleFavorite(item.id),
                          onTap: () => MenuDetailModal.show(context, item),
                          onAddToCart: () => _addItem(item, cartProvider),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }

  void _addItem(MenuItemModel item, CartProvider cartProvider) {
    cartProvider.addItem(item);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.check_circle_rounded, color: AppColors.gold, size: 20),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                '${item.name} ditambahkan ke keranjang!',
                style: const TextStyle(fontWeight: FontWeight.w600),
              ),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF4A141A),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(milliseconds: 1500),
      ),
    );
  }
}
