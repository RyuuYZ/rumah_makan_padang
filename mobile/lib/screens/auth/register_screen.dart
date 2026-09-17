import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_colors.dart';

/// Halaman Register Rasa / Raso Mandeh persis sesuai mockup Figma
class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _emailController = TextEditingController();
  final _addressController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _isSubmitting = false;

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _addressController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleRegister() async {
    if (_isSubmitting) return;
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    final authProvider = context.read<AuthProvider>();
    final cleanPhone = _phoneController.text.trim().replaceAll(RegExp(r'\s+|-'), '');

    try {
      final success = await authProvider.register(
        name: _nameController.text.trim(),
        phone: cleanPhone,
        email: _emailController.text.trim(),
        address: _addressController.text.trim(),
        password: _passwordController.text,
      );

      if (mounted && success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pendaftaran berhasil! Selamat datang di Raso Mandeh.'),
            backgroundColor: Color(0xFF5A1920),
          ),
        );
        context.go('/');
      } else if (mounted && !success && authProvider.errorMessage != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(authProvider.errorMessage!),
            backgroundColor: AppColors.error,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = context.watch<AuthProvider>();
    const ivoryBackground = Color(0xFFFAF7F2);
    const darkMaroon = Color(0xFF5A1920);
    const borderGray = Color(0xFFE4DDD4);

    return Scaffold(
      backgroundColor: ivoryBackground,
      body: SafeArea(
        top: false,
        child: Column(
          children: [
            // 1. Top Promo Bar (Persis Mockup Figma)
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(
                top: MediaQuery.of(context).padding.top + 8,
                bottom: 10,
                left: 16,
                right: 16,
              ),
              decoration: const BoxDecoration(
                color: Color(0xFF4A141A),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    width: 3.5,
                    height: 12,
                    decoration: BoxDecoration(
                      color: const Color(0xFFD4AF37),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'GRATIS SAMBAL LADO UNTUK PENDAFTARAN PERTAMA',
                    style: GoogleFonts.plusJakartaSans(
                      color: Colors.white,
                      fontSize: 10,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 0.8,
                    ),
                  ),
                ],
              ),
            ),

            // 2. Form Content (Scrollable)
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Tombol Kembali
                      Align(
                        alignment: Alignment.centerLeft,
                        child: InkWell(
                          onTap: () {
                            if (context.canPop()) {
                              context.pop();
                            } else {
                              context.go('/login');
                            }
                          },
                          borderRadius: BorderRadius.circular(8),
                          child: Container(
                            padding: const EdgeInsets.all(6),
                            child: const Icon(
                              Icons.arrow_back_ios_new_rounded,
                              size: 18,
                              color: Color(0xFF331317),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 8),

                      // Pill Badge: • GABUNG KELUARGA RASO
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEFE8DD),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              width: 6,
                              height: 6,
                              decoration: const BoxDecoration(
                                color: Color(0xFF836754),
                                shape: BoxShape.circle,
                              ),
                            ),
                            const SizedBox(width: 6),
                            Text(
                              'GABUNG KELUARGA RASO',
                              style: GoogleFonts.plusJakartaSans(
                                color: const Color(0xFF836754),
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                                letterSpacing: 1.2,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Headline
                      Text(
                        'Mari bergabung, nikmati\nkelezatan autentik.',
                        style: GoogleFonts.playfairDisplay(
                          fontSize: 27,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF301115),
                          height: 1.22,
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Error message jika ada
                      if (authProvider.errorMessage != null) ...[
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppColors.errorBg,
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: AppColors.error.withAlpha(60)),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.error_outline,
                                  color: AppColors.error, size: 18),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  authProvider.errorMessage!,
                                  style: GoogleFonts.plusJakartaSans(
                                    color: AppColors.error,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 16),
                      ],

                      // Field 1: Nama Lengkap *
                      _buildLabel('Nama Lengkap'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _nameController,
                        style: GoogleFonts.plusJakartaSans(fontSize: 14),
                        validator: (val) {
                          if (val == null || val.trim().isEmpty) {
                            return 'Nama lengkap wajib diisi';
                          }
                          if (val.trim().length < 3) {
                            return 'Nama lengkap minimal 3 karakter';
                          }
                          return null;
                        },
                        decoration: _buildInputDecoration(
                          hintText: 'cth. Siti Nurhaliza',
                          prefixIcon: Icons.person_outline_rounded,
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Field 2: Nomor Handphone *
                      _buildLabel('Nomor Handphone'),
                      const SizedBox(height: 6),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // +62 Box
                          Container(
                            height: 50,
                            padding: const EdgeInsets.symmetric(horizontal: 12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFEFEAE2),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: borderGray),
                            ),
                            child: Row(
                              children: [
                                // Bendera Merah Putih Mini
                                Container(
                                  width: 18,
                                  height: 12,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(2),
                                    border: Border.all(color: Colors.black12, width: 0.5),
                                  ),
                                  child: Column(
                                    children: [
                                      Expanded(child: Container(color: Colors.red)),
                                      Expanded(child: Container(color: Colors.white)),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  '+62',
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 13.5,
                                    fontWeight: FontWeight.w700,
                                    color: const Color(0xFF331317),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(width: 10),

                          // Phone Number Input
                          Expanded(
                            child: TextFormField(
                              controller: _phoneController,
                              keyboardType: TextInputType.phone,
                              style: GoogleFonts.plusJakartaSans(fontSize: 14),
                              validator: (val) {
                                if (val == null || val.trim().isEmpty) {
                                  return 'Nomor HP wajib diisi';
                                }
                                final clean = val.trim().replaceAll(RegExp(r'\s+|-'), '');
                                if (!RegExp(r'^[0-9]+$').hasMatch(clean)) {
                                  return 'Nomor HP hanya boleh berisi angka';
                                }
                                if (clean.length < 8 || clean.length > 13) {
                                  return 'Nomor HP harus 8-13 digit';
                                }
                                return null;
                              },
                              decoration: _buildInputDecoration(
                                hintText: '812-3456-7890',
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Field 3: Alamat Email *
                      _buildLabel('Alamat Email'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        style: GoogleFonts.plusJakartaSans(fontSize: 14),
                        validator: (val) {
                          if (val == null || val.trim().isEmpty) {
                            return 'Alamat email wajib diisi';
                          }
                          final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
                          if (!emailRegex.hasMatch(val.trim())) {
                            return 'Format email tidak valid (cth: nama@email.com)';
                          }
                          return null;
                        },
                        decoration: _buildInputDecoration(
                          hintText: 'nama@email.com',
                          prefixIcon: Icons.mail_outline_rounded,
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Field 4: Alamat Pengiriman Utama *
                      _buildLabel('Alamat Pengiriman Utama'),
                      const SizedBox(height: 6),
                      Stack(
                        alignment: Alignment.bottomRight,
                        children: [
                          TextFormField(
                            controller: _addressController,
                            maxLines: 3,
                            style: GoogleFonts.plusJakartaSans(fontSize: 13.5, height: 1.4),
                            validator: (val) {
                              if (val == null || val.trim().isEmpty) {
                                return 'Alamat pengiriman wajib diisi';
                              }
                              if (val.trim().length < 10) {
                                return 'Alamat terlalu singkat (minimal 10 karakter)';
                              }
                              return null;
                            },
                            decoration: InputDecoration(
                              hintText:
                                  'Nama jalan, nomor rumah, RT/RW, kelurahan, dan patokan tujuan...',
                              hintStyle: GoogleFonts.plusJakartaSans(
                                color: const Color(0xFFBBB0A6),
                                fontSize: 12.5,
                              ),
                              filled: true,
                              fillColor: Colors.white,
                              contentPadding: const EdgeInsets.fromLTRB(14, 14, 36, 14),
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: const BorderSide(color: borderGray),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: const BorderSide(color: darkMaroon, width: 1.5),
                              ),
                              errorBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: const BorderSide(color: AppColors.error),
                              ),
                              focusedErrorBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: const BorderSide(color: AppColors.error, width: 1.5),
                              ),
                            ),
                          ),
                          Positioned(
                            right: 12,
                            bottom: 12,
                            child: Icon(
                              Icons.location_on_outlined,
                              size: 18,
                              color: const Color(0xFFC2B7AC),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Field 5: Kata Sandi (Minimal 8 Karakter) *
                      _buildLabel('Kata Sandi Akun'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _passwordController,
                        obscureText: _obscurePassword,
                        style: GoogleFonts.plusJakartaSans(fontSize: 14),
                        validator: (val) {
                          if (val == null || val.isEmpty) {
                            return 'Kata sandi wajib diisi';
                          }
                          if (val.length < 8) {
                            return 'Kata sandi minimal 8 karakter';
                          }
                          return null;
                        },
                        decoration: _buildInputDecoration(
                          hintText: 'Minimal 8 karakter',
                          prefixIcon: Icons.lock_outline_rounded,
                        ).copyWith(
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                              color: const Color(0xFF9E938A),
                              size: 20,
                            ),
                            onPressed: () {
                              setState(() => _obscurePassword = !_obscurePassword);
                            },
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Field 6: Konfirmasi Kata Sandi *
                      _buildLabel('Konfirmasi Kata Sandi'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _confirmPasswordController,
                        obscureText: _obscureConfirmPassword,
                        style: GoogleFonts.plusJakartaSans(fontSize: 14),
                        validator: (val) {
                          if (val == null || val.isEmpty) {
                            return 'Konfirmasi kata sandi wajib diisi';
                          }
                          if (val != _passwordController.text) {
                            return 'Kata sandi tidak cocok';
                          }
                          return null;
                        },
                        decoration: _buildInputDecoration(
                          hintText: 'Ulangi kata sandi akun',
                          prefixIcon: Icons.lock_reset_rounded,
                        ).copyWith(
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscureConfirmPassword ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                              color: const Color(0xFF9E938A),
                              size: 20,
                            ),
                            onPressed: () {
                              setState(() => _obscureConfirmPassword = !_obscureConfirmPassword);
                            },
                          ),
                        ),
                      ),
                      const SizedBox(height: 28),

                      // 3. Tombol Daftar Sekarang →
                      SizedBox(
                        width: double.infinity,
                        height: 52,
                        child: ElevatedButton(
                          onPressed: (authProvider.isLoading || _isSubmitting) ? null : _handleRegister,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: darkMaroon,
                            foregroundColor: Colors.white,
                            elevation: 2,
                            shadowColor: darkMaroon.withAlpha(90),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(26),
                            ),
                          ),
                          child: authProvider.isLoading
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  ),
                                )
                              : Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      'Daftar Sekarang',
                                      style: GoogleFonts.plusJakartaSans(
                                        fontSize: 14.5,
                                        fontWeight: FontWeight.w700,
                                        color: Colors.white,
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    const Icon(
                                      Icons.arrow_forward_rounded,
                                      size: 18,
                                      color: Colors.white,
                                    ),
                                  ],
                                ),
                        ),
                      ),
                      const SizedBox(height: 18),

                      // 4. Link Sudah Punya Akun
                      Center(
                        child: GestureDetector(
                          onTap: () {
                            authProvider.clearError();
                            if (context.canPop()) {
                              context.pop();
                            } else {
                              context.go('/login');
                            }
                          },
                          child: RichText(
                            text: TextSpan(
                              text: 'Sudah punya akun Raso? ',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 13,
                                color: const Color(0xFF6B5D55),
                              ),
                              children: [
                                TextSpan(
                                  text: 'Masuk di sini',
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w700,
                                    color: darkMaroon,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 36),

                      // 5. Footer Motto Pembatas
                      Row(
                        children: [
                          Expanded(
                            child: Container(
                              height: 0.8,
                              color: const Color(0xFFDFD7CE),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 12),
                            child: Text(
                              'Citarasa Asli Sabana Raso',
                              style: GoogleFonts.playfairDisplay(
                                fontSize: 11,
                                fontStyle: FontStyle.italic,
                                color: const Color(0xFF8E7E74),
                              ),
                            ),
                          ),
                          Expanded(
                            child: Container(
                              height: 0.8,
                              color: const Color(0xFFDFD7CE),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return RichText(
      text: TextSpan(
        text: text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 13,
          fontWeight: FontWeight.w700,
          color: const Color(0xFF331317),
        ),
        children: const [
          TextSpan(
            text: ' *',
            style: TextStyle(
              color: Color(0xFFC0151E),
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }

  InputDecoration _buildInputDecoration({
    required String hintText,
    IconData? prefixIcon,
  }) {
    const borderGray = Color(0xFFE4DDD4);
    const darkMaroon = Color(0xFF5A1920);

    return InputDecoration(
      hintText: hintText,
      hintStyle: GoogleFonts.plusJakartaSans(
        color: const Color(0xFFBBB0A6),
        fontSize: 13.5,
      ),
      prefixIcon: prefixIcon != null
          ? Icon(prefixIcon, color: const Color(0xFF9E938A), size: 19)
          : null,
      filled: true,
      fillColor: Colors.white,
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: borderGray),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: darkMaroon, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.error),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.error, width: 1.5),
      ),
    );
  }
}
