import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';

/// Scaffold utama dengan Bottom Navigation Bar persis mockup Figma:
/// - Beranda (Ikon atap Gonjong Rumah Gadang warna emas)
/// - Menu (Ikon sendok garpu)
/// - Pesanan (Ikon pesanan dengan badge counter merah)
/// - Profil (Ikon profil)
class MainScaffold extends StatelessWidget {
  final Widget child;

  const MainScaffold({super.key, required this.child});

  int _calculateSelectedIndex(BuildContext context) {
    final location = GoRouterState.of(context).uri.toString();
    if (location.startsWith('/menu')) return 1;
    if (location.startsWith('/orders')) return 2;
    if (location.startsWith('/profile')) return 3;
    return 0; // Home
  }

  void _onItemTapped(int index, BuildContext context) {
    switch (index) {
      case 0:
        context.go('/');
        break;
      case 1:
        context.go('/menu');
        break;
      case 2:
        context.go('/orders');
        break;
      case 3:
        context.go('/profile');
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    final selectedIndex = _calculateSelectedIndex(context);

    return Scaffold(
      body: child,
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(12),
              blurRadius: 10,
              offset: const Offset(0, -3),
            ),
          ],
          border: const Border(
            top: BorderSide(color: Color(0xFFEFE8DD), width: 1),
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildCustomNavItem(
                  context,
                  isSelected: selectedIndex == 0,
                  iconAsset: 'assets/icons/ic_beranda.png',
                  label: 'Beranda',
                  onTap: () => _onItemTapped(0, context),
                ),
                _buildCustomNavItem(
                  context,
                  isSelected: selectedIndex == 1,
                  iconAsset: 'assets/icons/ic_menu.png',
                  label: 'Menu',
                  onTap: () => _onItemTapped(1, context),
                ),
                _buildCustomNavItem(
                  context,
                  isSelected: selectedIndex == 2,
                  iconAsset: 'assets/icons/ic_pesanan.png',
                  label: 'Pesanan',
                  showDot: true,
                  onTap: () => _onItemTapped(2, context),
                ),
                _buildCustomNavItem(
                  context,
                  isSelected: selectedIndex == 3,
                  iconAsset: 'assets/icons/ic_profil.png',
                  label: 'Profil',
                  onTap: () => _onItemTapped(3, context),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCustomNavItem(
    BuildContext context, {
    required bool isSelected,
    required String iconAsset,
    required String label,
    required VoidCallback onTap,
    int badgeCount = 0,
    bool showDot = false,
  }) {
    const goldColor = Color(0xFFDF9C36);
    const inactiveColor = Color(0xFF7C6C64);
    final color = isSelected ? goldColor : inactiveColor;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Stack(
              clipBehavior: Clip.none,
              children: [
                ColorFiltered(
                  colorFilter: ColorFilter.mode(
                    color,
                    BlendMode.srcIn,
                  ),
                  child: Image.asset(
                    iconAsset,
                    width: 25,
                    height: 25,
                    fit: BoxFit.contain,
                  ),
                ),
                if (badgeCount > 0)
                  Positioned(
                    top: -4,
                    right: -8,
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
                        badgeCount > 99 ? '99+' : '$badgeCount',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 8.5,
                          fontWeight: FontWeight.w800,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  )
                else if (showDot)
                  Positioned(
                    top: -2,
                    right: -3,
                    child: Container(
                      width: 7,
                      height: 7,
                      decoration: const BoxDecoration(
                        color: Color(0xFFC0151E),
                        shape: BoxShape.circle,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: GoogleFonts.plusJakartaSans(
                color: color,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                fontSize: 11,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
