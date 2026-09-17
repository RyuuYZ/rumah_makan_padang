import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../models/user_model.dart';
import '../../providers/auth_provider.dart';

/// Halaman Profil Pengguna Rasa Mandeh (100% Persis Mockup Figma "profile page"):
/// 1. Top Card Profil: Avatar Monogram "SN" marun border emas + badge verified, nama Siti Nurhaliza, telepon, email, tombol edit.
/// 2. Card Alamat Pengantaran Utama: Ikon pin marun, label "Alamat Pengantaran Utama", tombol "Kelola Alamat",
///    box dalam krem berbadge "Utama", checklist circle, alamat Kemang & patokan apotek.
/// 3. Card Menu Group (3 menu):
///    - Metode Pembayaran Tersimpan (QRIS, BCA Virtual, GoPay)
///    - Pusat Bantuan & Layanan Uda CS (Chat langsung siap melayani)
///    - Syarat, Ketentuan & Kebijakan Privasi (Jaminan privasi data pelanggan)
/// 4. Outlined Button: "Keluar dari Akun" border marun rounded pill.
class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  String _getInitials(String name) {
    final parts = name.trim().split(RegExp(r'\s+'));
    if (parts.isEmpty || parts[0].isEmpty) return 'SN';
    if (parts.length == 1) {
      return parts[0].substring(0, math.min(2, parts[0].length)).toUpperCase();
    }
    return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Text(
          'Keluar dari Akun?',
          style: GoogleFonts.playfairDisplay(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: const Color(0xFF301115),
          ),
        ),
        content: Text(
          'Anda akan keluar dari akun Rasa Mandeh. Sesi pesanan Anda akan tersimpan dengan aman.',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            color: const Color(0xFF6E6258),
            height: 1.4,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(
              'Batal',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w600,
                color: const Color(0xFF867A6E),
              ),
            ),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF5D1720),
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            onPressed: () async {
              Navigator.pop(ctx);
              await context.read<AuthProvider>().logout();
              if (context.mounted) {
                context.go('/login');
              }
            },
            child: Text(
              'Ya, Keluar',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showEditProfileDialog(BuildContext context, UserModel? user) {
    final authProvider = context.read<AuthProvider>();
    final nameController = TextEditingController(
      text: user?.name.isNotEmpty == true ? user!.name : 'Siti Nurhaliza',
    );
    final phoneController = TextEditingController(
      text: user?.phone.isNotEmpty == true ? user!.phone : '+62 812-3456-7890',
    );
    final emailController = TextEditingController(
      text: user?.email.isNotEmpty == true ? user!.email : 'siti.nurhaliza@email.com',
    );

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Row(
          children: [
            const Icon(Icons.edit_note_rounded, color: Color(0xFF5D1720), size: 22),
            const SizedBox(width: 8),
            Text(
              'Ubah Profil',
              style: GoogleFonts.playfairDisplay(
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF301115),
              ),
            ),
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: 'Nama Lengkap',
                  labelStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: phoneController,
                decoration: InputDecoration(
                  labelText: 'Nomor Telepon',
                  labelStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: emailController,
                decoration: InputDecoration(
                  labelText: 'Alamat Email',
                  labelStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(
              'Batal',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w600,
                color: const Color(0xFF867A6E),
              ),
            ),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF5D1720),
              elevation: 0,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () async {
              await authProvider.updateProfile(
                name: nameController.text.trim(),
                phone: phoneController.text.trim(),
                email: emailController.text.trim(),
              );
              if (ctx.mounted) Navigator.pop(ctx);
              if (context.mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Profil berhasil diperbarui!'),
                    backgroundColor: Color(0xFF5D1720),
                  ),
                );
              }
            },
            child: Text(
              'Simpan',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showAddressDialog(BuildContext context, UserModel? user) {
    final authProvider = context.read<AuthProvider>();
    final controller = TextEditingController(
      text: user?.address.isNotEmpty == true
          ? user!.address
          : 'Jl. Kemang Raya No. 14, Mampang Prapatan',
    );

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Row(
          children: [
            const Icon(Icons.location_on_rounded, color: Color(0xFF5D1720), size: 22),
            const SizedBox(width: 8),
            Text(
              'Kelola Alamat',
              style: GoogleFonts.playfairDisplay(
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF301115),
              ),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Alamat Utama:',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: const Color(0xFF5A2219),
              ),
            ),
            const SizedBox(height: 6),
            TextField(
              controller: controller,
              maxLines: 3,
              style: GoogleFonts.plusJakartaSans(fontSize: 13),
              decoration: InputDecoration(
                hintText: 'Tulis alamat lengkap pengantaran...',
                hintStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                contentPadding: const EdgeInsets.all(12),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text(
              'Batal',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w600,
                color: const Color(0xFF867A6E),
              ),
            ),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF5D1720),
              elevation: 0,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () {
              if (controller.text.trim().isNotEmpty) {
                authProvider.updateDeliveryAddress(controller.text.trim());
              }
              Navigator.pop(ctx);
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Alamat pengantaran berhasil diperbarui!'),
                  backgroundColor: Color(0xFF5D1720),
                ),
              );
            },
            child: Text(
              'Simpan',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showPaymentMethodsSheet(BuildContext context) {
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
                    'Metode Pembayaran Tersimpan',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF301115),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded, size: 20, color: Color(0xFF867A6E)),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              _buildPaymentOptionTile(
                icon: Icons.qr_code_2_rounded,
                title: 'QRIS Dinamis',
                subtitle: 'GoPay, OVO, Dana, BCA Mobile',
                isActive: true,
              ),
              const Divider(height: 1),
              _buildPaymentOptionTile(
                icon: Icons.account_balance_rounded,
                title: 'BCA Virtual Account',
                subtitle: 'Transfer otomatis terverifikasi instan',
                isActive: true,
              ),
              const Divider(height: 1),
              _buildPaymentOptionTile(
                icon: Icons.account_balance_wallet_outlined,
                title: 'GoPay Direct Debit',
                subtitle: 'Terhubung • Saldo siap pakai',
                isActive: true,
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPaymentOptionTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required bool isActive,
  }) {
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(vertical: 4),
      leading: Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          color: const Color(0xFFF7F2EB),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Icon(icon, color: const Color(0xFF5B3829), size: 20),
      ),
      title: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 13,
          fontWeight: FontWeight.w600,
          color: const Color(0xFF301115),
        ),
      ),
      subtitle: Text(
        subtitle,
        style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF85796E)),
      ),
      trailing: const Icon(Icons.check_circle_rounded, color: Color(0xFF5D1720), size: 20),
    );
  }

  void _showHelpSupportSheet(BuildContext context) {
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
                    'Pusat Bantuan & Uda CS',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF301115),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded, size: 20, color: Color(0xFF867A6E)),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                'Uda CS siap melayani pertanyaan seputar pesanan, katering adat Minang, dan menu spesial harian.',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  color: const Color(0xFF6E6258),
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 16),
              ListTile(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                tileColor: const Color(0xFFFAF5EE),
                leading: const Icon(Icons.chat_bubble_outline_rounded, color: Color(0xFF5D1720)),
                title: Text(
                  'Chat WhatsApp Uda CS',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF301115),
                  ),
                ),
                subtitle: Text(
                  '+62 812-3456-7890 (Aktif 08.00 - 22.00 WIB)',
                  style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF85796E)),
                ),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Color(0xFF5D1720)),
                onTap: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Menghubungi Uda CS WhatsApp: +62 812-3456-7890'),
                      backgroundColor: Color(0xFF5D1720),
                    ),
                  );
                },
              ),
              const SizedBox(height: 14),
            ],
          ),
        ),
      ),
    );
  }

  void _showPrivacyTermsDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Row(
          children: [
            const Icon(Icons.shield_outlined, color: Color(0xFF5D1720), size: 22),
            const SizedBox(width: 8),
            Text(
              'Syarat & Kebijakan',
              style: GoogleFonts.playfairDisplay(
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF301115),
              ),
            ),
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Jaminan Privasi & Kualitas Rasa Mandeh:',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12.5,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF301115),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                '1. Seluruh data identitas, nomor telepon, dan riwayat alamat pengantaran terlindungi dengan enkripsi standar industri.\n\n'
                '2. Informasi pelanggan semata-mata digunakan untuk akurasi pengantaran kurir dan verifikasi transaksi pembayaran.\n\n'
                '3. Kami menjamin kehalalan dan keotentikan setiap masakan Minangkabau yang disajikan.',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  color: const Color(0xFF6E6258),
                  height: 1.45,
                ),
              ),
            ],
          ),
        ),
        actions: [
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF5D1720),
              elevation: 0,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () => Navigator.pop(ctx),
            child: Text(
              'Saya Mengerti',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = context.watch<AuthProvider>();
    final user = authProvider.user;

    // Nilai fallback persis mockup Figma jika data sesi kosong
    final userName = user?.name.isNotEmpty == true ? user!.name : 'Siti Nurhaliza';
    final userPhone = user?.phone.isNotEmpty == true ? user!.phone : '+62 812–3456–7890';
    final userEmail = user?.email.isNotEmpty == true ? user!.email : 'siti.nurhaliza@email.com';
    final userAddress = user?.address.isNotEmpty == true
        ? user!.address
        : 'Jl. Kemang Raya No. 14, Mampang Prapatan';

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ============================================================
              // 1. TOP CARD PROFIL USER (Persis Figma)
              // ============================================================
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: const Color(0xFFF0EAE1), width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withAlpha(8),
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                child: Row(
                  children: [
                    // Avatar Monogram SN (Marun pekat + Border Emas + Badge Verified)
                    Stack(
                      clipBehavior: Clip.none,
                      children: [
                        Container(
                          width: 62,
                          height: 62,
                          decoration: BoxDecoration(
                            color: const Color(0xFF4A141A),
                            shape: BoxShape.circle,
                            border: Border.all(color: const Color(0xFFDF9C36), width: 2.2),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF4A141A).withAlpha(40),
                                blurRadius: 8,
                                offset: const Offset(0, 2),
                              ),
                            ],
                          ),
                          alignment: Alignment.center,
                          child: Text(
                            _getInitials(userName),
                            style: GoogleFonts.playfairDisplay(
                              fontSize: 22,
                              fontWeight: FontWeight.w700,
                              color: const Color(0xFFFAF5ED),
                              letterSpacing: -0.5,
                            ),
                          ),
                        ),
                        // Badge Ikon Verified Kecil di Sudut Bawah Avatar
                        Positioned(
                          bottom: 0,
                          right: 0,
                          child: Container(
                            width: 18,
                            height: 18,
                            decoration: BoxDecoration(
                              color: const Color(0xFF8A5A23),
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 1.5),
                            ),
                            alignment: Alignment.center,
                            child: const Icon(
                              Icons.check,
                              size: 11,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(width: 14),

                    // Nama, Telepon & Email
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            userName,
                            style: GoogleFonts.playfairDisplay(
                              fontSize: 18,
                              fontWeight: FontWeight.w700,
                              color: const Color(0xFF301115),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(
                                Icons.phone_outlined,
                                size: 12.5,
                                color: Color(0xFF7A6E63),
                              ),
                              const SizedBox(width: 5),
                              Expanded(
                                child: Text(
                                  userPhone,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 11.5,
                                    color: const Color(0xFF6E6258),
                                    fontWeight: FontWeight.w500,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 2.5),
                          Row(
                            children: [
                              const Icon(
                                Icons.mail_outline_rounded,
                                size: 12.5,
                                color: Color(0xFF7A6E63),
                              ),
                              const SizedBox(width: 5),
                              Expanded(
                                child: Text(
                                  userEmail,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 11.5,
                                    color: const Color(0xFF6E6258),
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

                    // Tombol Edit Profil
                    InkWell(
                      onTap: () => _showEditProfileDialog(context, user),
                      borderRadius: BorderRadius.circular(8),
                      child: const Padding(
                        padding: EdgeInsets.all(6),
                        child: Icon(
                          Icons.edit_square,
                          size: 18,
                          color: Color(0xFF301115),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 14),

              // ============================================================
              // 2. CARD ALAMAT PENGANTARAN UTAMA (Persis Figma)
              // ============================================================
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: const Color(0xFFF0EAE1), width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withAlpha(8),
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Bar
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            const Icon(
                              Icons.location_on_rounded,
                              size: 16,
                              color: Color(0xFF6E1821),
                            ),
                            const SizedBox(width: 6),
                            Text(
                              'Alamat Pengantaran Utama',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 13,
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF301115),
                              ),
                            ),
                          ],
                        ),
                        InkWell(
                          onTap: () => _showAddressDialog(context, user),
                          borderRadius: BorderRadius.circular(6),
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                            child: Text(
                              'Kelola Alamat',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 11.5,
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF6E1821),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Box Alamat Warna Krem Gading
                    Container(
                      width: double.infinity,
                      decoration: BoxDecoration(
                        color: const Color(0xFFFAF5EE),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFECE2D5), width: 1),
                      ),
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Text(
                                'Rumah Utama',
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: const Color(0xFF5A2219),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFE8DFC0).withAlpha(120),
                                  borderRadius: BorderRadius.circular(5),
                                ),
                                child: Text(
                                  'Utama',
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF6B5E52),
                                  ),
                                ),
                              ),
                              const Spacer(),
                              const Icon(
                                Icons.check_circle_outline_rounded,
                                size: 18,
                                color: Color(0xFF6E6259),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            userAddress,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12.5,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF301115),
                              height: 1.3,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            'Jakarta Selatan • Patokan: Pagar hitam depan Apotek',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 11,
                              color: const Color(0xFF867A6E),
                              height: 1.3,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 14),

              // ============================================================
              // 3. CARD MENU LIST GROUP (Persis Figma)
              // ============================================================
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: const Color(0xFFF0EAE1), width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withAlpha(8),
                      blurRadius: 10,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    // Item 1: Metode Pembayaran Tersimpan
                    _buildProfileMenuItem(
                      icon: Icons.account_balance_wallet_outlined,
                      title: 'Metode Pembayaran Tersimpan',
                      subtitle: 'QRIS, BCA Virtual, GoPay',
                      onTap: () => _showPaymentMethodsSheet(context),
                    ),
                    const Divider(
                      height: 1,
                      thickness: 0.8,
                      color: Color(0xFFF3ECE2),
                      indent: 14,
                      endIndent: 14,
                    ),

                    // Item 2: Pusat Bantuan & Layanan Uda CS
                    _buildProfileMenuItem(
                      icon: Icons.support_agent_rounded,
                      title: 'Pusat Bantuan & Layanan Uda CS',
                      subtitle: 'Chat langsung siap melayani',
                      onTap: () => _showHelpSupportSheet(context),
                    ),
                    const Divider(
                      height: 1,
                      thickness: 0.8,
                      color: Color(0xFFF3ECE2),
                      indent: 14,
                      endIndent: 14,
                    ),

                    // Item 3: Syarat, Ketentuan & Kebijakan Privasi
                    _buildProfileMenuItem(
                      icon: Icons.shield_outlined,
                      title: 'Syarat, Ketentuan & Kebijakan Privasi',
                      subtitle: 'Jaminan privasi data pelanggan',
                      onTap: () => _showPrivacyTermsDialog(context),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 18),

              // ============================================================
              // 4. TOMBOL KELUAR DARI AKUN (Persis Figma)
              // ============================================================
              InkWell(
                onTap: () => _showLogoutDialog(context),
                borderRadius: BorderRadius.circular(24),
                child: Container(
                  width: double.infinity,
                  height: 46,
                  decoration: BoxDecoration(
                    color: Colors.transparent,
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(
                      color: const Color(0xFF5D1720),
                      width: 1.2,
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(
                        Icons.logout_rounded,
                        size: 18,
                        color: Color(0xFF5D1720),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Keluar dari Akun',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF5D1720),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildProfileMenuItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        child: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: const Color(0xFFF7F2EB),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(
                icon,
                size: 18,
                color: const Color(0xFF5B3829),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: const Color(0xFF301115),
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    subtitle,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 11,
                      color: const Color(0xFF85796E),
                    ),
                  ),
                ],
              ),
            ),
            const Icon(
              Icons.chevron_right_rounded,
              size: 20,
              color: Color(0xFF8E8378),
            ),
          ],
        ),
      ),
    );
  }
}
