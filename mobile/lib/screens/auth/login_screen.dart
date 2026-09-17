import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_colors.dart';

/// Halaman Login Pengguna Rasa / Raso Mandeh persis sesuai mockup Figma
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  int _selectedTab = 0; // 0: Nomor WhatsApp, 1: Email

  final _phoneController = TextEditingController(text: '812 3456 7890');
  final _emailController = TextEditingController(text: 'budi@rasamandeh.com');
  final _passwordController = TextEditingController(text: 'password123');

  bool _rememberMe = true;
  bool _obscurePassword = true;
  bool _isSubmitting = false;

  @override
  void dispose() {
    _phoneController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (_isSubmitting) return;
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    final authProvider = context.read<AuthProvider>();
    final cleanPhone =
        _phoneController.text.trim().replaceAll(RegExp(r'\s+|-'), '');
    final account = _selectedTab == 0
        ? '+62 $cleanPhone'
        : _emailController.text.trim();

    try {
      final success = await authProvider.login(
        account,
        _passwordController.text,
      );

      if (mounted) {
        if (success) {
          context.go('/');
        } else if (authProvider.errorMessage != null) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(authProvider.errorMessage!),
              backgroundColor: const Color(0xFF5A1920),
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
      }
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  Future<void> _handleGoogleLogin() async {
    if (_isSubmitting) return;
    setState(() => _isSubmitting = true);
    try {
      final authProvider = context.read<AuthProvider>();
      final success = await authProvider.loginWithGoogle();

      if (mounted) {
        if (success) {
          context.go('/');
        } else if (authProvider.errorMessage != null) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(authProvider.errorMessage!),
              backgroundColor: const Color(0xFF5A1920),
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
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
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  const SizedBox(height: 10),

                  // 1. Badge: • SELAMAT DATANG KEMBALI
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
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
                          'SELAMAT DATANG KEMBALI',
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
                  const SizedBox(height: 16),

                  // 2. Headline Serif (Playfair Display)
                  Text(
                    'Masuk & Nikmati Kelezatan\nNan Sabana Raso.',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 25,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF301115),
                      height: 1.25,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),

                  // 3. Tab Switcher: [Nomor WhatsApp] | [Email]
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFECE5DA),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Row(
                      children: [
                        // Tab WhatsApp
                        Expanded(
                          child: InkWell(
                            onTap: () {
                              context.read<AuthProvider>().clearError();
                              _formKey.currentState?.reset();
                              setState(() => _selectedTab = 0);
                            },
                            borderRadius: BorderRadius.circular(10),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              padding: const EdgeInsets.symmetric(vertical: 10),
                              decoration: BoxDecoration(
                                color: _selectedTab == 0 ? Colors.white : Colors.transparent,
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: _selectedTab == 0
                                    ? [
                                        BoxShadow(
                                          color: Colors.black.withAlpha(10),
                                          blurRadius: 4,
                                          offset: const Offset(0, 2),
                                        ),
                                      ]
                                    : null,
                              ),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.smartphone_rounded,
                                    size: 17,
                                    color: _selectedTab == 0
                                        ? darkMaroon
                                        : const Color(0xFF776961),
                                  ),
                                  const SizedBox(width: 6),
                                  Text(
                                    'Nomor WhatsApp',
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 12.5,
                                      fontWeight: _selectedTab == 0
                                          ? FontWeight.w700
                                          : FontWeight.w500,
                                      color: _selectedTab == 0
                                          ? darkMaroon
                                          : const Color(0xFF776961),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),

                        // Tab Email
                        Expanded(
                          child: InkWell(
                            onTap: () {
                              context.read<AuthProvider>().clearError();
                              _formKey.currentState?.reset();
                              setState(() => _selectedTab = 1);
                            },
                            borderRadius: BorderRadius.circular(10),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              padding: const EdgeInsets.symmetric(vertical: 10),
                              decoration: BoxDecoration(
                                color: _selectedTab == 1 ? Colors.white : Colors.transparent,
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: _selectedTab == 1
                                    ? [
                                        BoxShadow(
                                          color: Colors.black.withAlpha(10),
                                          blurRadius: 4,
                                          offset: const Offset(0, 2),
                                        ),
                                      ]
                                    : null,
                              ),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.mail_outline_rounded,
                                    size: 17,
                                    color: _selectedTab == 1
                                        ? darkMaroon
                                        : const Color(0xFF776961),
                                  ),
                                  const SizedBox(width: 6),
                                  Text(
                                    'Email',
                                    style: GoogleFonts.plusJakartaSans(
                                      fontSize: 12.5,
                                      fontWeight: _selectedTab == 1
                                          ? FontWeight.w700
                                          : FontWeight.w500,
                                      color: _selectedTab == 1
                                          ? darkMaroon
                                          : const Color(0xFF776961),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Error Banner jika ada
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
                          const Icon(Icons.error_outline, color: AppColors.error, size: 18),
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

                  // 4. Input Nomor WhatsApp atau Email
                  if (_selectedTab == 0) ...[
                    // Tab WhatsApp
                    Align(
                      alignment: Alignment.centerLeft,
                      child: Text(
                        'Nomor WhatsApp / HP Aktif',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF331317),
                        ),
                      ),
                    ),
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

                        // Phone Field
                        Expanded(
                          child: TextFormField(
                            controller: _phoneController,
                            keyboardType: TextInputType.phone,
                            style: GoogleFonts.plusJakartaSans(fontSize: 14),
                            validator: (val) {
                              if (val == null || val.trim().isEmpty) {
                                return 'Nomor WhatsApp wajib diisi';
                              }
                              final digits =
                                  val.replaceAll(RegExp(r'\s+|-'), '');
                              if (!RegExp(r'^[0-9]+$').hasMatch(digits)) {
                                return 'Nomor HP hanya boleh berisi angka';
                              }
                              if (digits.length < 8 || digits.length > 13) {
                                return 'Nomor HP harus antara 8-13 digit';
                              }
                              return null;
                            },
                            decoration: InputDecoration(
                              hintText: '812 3456 7890',
                              hintStyle: GoogleFonts.plusJakartaSans(
                                color: const Color(0xFFBBB0A6),
                                fontSize: 13.5,
                              ),
                              suffixIcon: const Padding(
                                padding: EdgeInsets.all(12),
                                child: Icon(
                                  Icons.check_circle_rounded,
                                  color: Color(0xFF7A5C1E),
                                  size: 20,
                                ),
                              ),
                              filled: true,
                              fillColor: Colors.white,
                              contentPadding:
                                  const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
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
                        ),
                      ],
                    ),
                    const SizedBox(height: 5),
                    Align(
                      alignment: Alignment.centerLeft,
                      child: Text(
                        'Kami akan kirim kode verifikasi instan via WhatsApp.',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          color: const Color(0xFF8C7D75),
                        ),
                      ),
                    ),
                  ] else ...[
                    // Tab Email
                    Align(
                      alignment: Alignment.centerLeft,
                      child: Text(
                        'Alamat Email',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF331317),
                        ),
                      ),
                    ),
                    const SizedBox(height: 6),
                    TextFormField(
                      controller: _emailController,
                      keyboardType: TextInputType.emailAddress,
                      style: GoogleFonts.plusJakartaSans(fontSize: 14),
                      validator: (val) {
                        if (val == null || val.trim().isEmpty) {
                          return 'Alamat email wajib diisi';
                        }
                        if (!val.contains('@') || !val.contains('.')) {
                          return 'Format email tidak valid';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        hintText: 'nama@email.com',
                        hintStyle: GoogleFonts.plusJakartaSans(
                          color: const Color(0xFFBBB0A6),
                          fontSize: 13.5,
                        ),
                        prefixIcon: const Icon(
                          Icons.mail_outline_rounded,
                          color: Color(0xFF9E938A),
                          size: 19,
                        ),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding:
                            const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
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
                  ],
                  const SizedBox(height: 16),

                  // 5. Input Kata Sandi
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      'Kata Sandi',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: const Color(0xFF331317),
                      ),
                    ),
                  ),
                  const SizedBox(height: 6),
                  TextFormField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    style: GoogleFonts.plusJakartaSans(fontSize: 14),
                    validator: (val) {
                      if (val == null || val.isEmpty) {
                        return 'Kata sandi wajib diisi';
                      }
                      if (val.length < 6) {
                        return 'Kata sandi minimal 6 karakter';
                      }
                      return null;
                    },
                    decoration: InputDecoration(
                      hintText: 'Masukkan kata sandi akun',
                      hintStyle: GoogleFonts.plusJakartaSans(
                        color: const Color(0xFFBBB0A6),
                        fontSize: 13.5,
                      ),
                      prefixIcon: const Icon(
                        Icons.lock_outline_rounded,
                        color: Color(0xFF9E938A),
                        size: 19,
                      ),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined,
                          color: const Color(0xFF9E938A),
                          size: 20,
                        ),
                        onPressed: () {
                          setState(() {
                            _obscurePassword = !_obscurePassword;
                          });
                        },
                      ),
                      filled: true,
                      fillColor: Colors.white,
                      contentPadding:
                          const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
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
                  const SizedBox(height: 14),

                  // 6. Ingat Saya & Lupa Kata Sandi?
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Ingat Saya Checkbox
                      InkWell(
                        onTap: () {
                          setState(() {
                            _rememberMe = !_rememberMe;
                          });
                        },
                        borderRadius: BorderRadius.circular(6),
                        child: Row(
                          children: [
                            Container(
                              width: 18,
                              height: 18,
                              decoration: BoxDecoration(
                                color: _rememberMe ? darkMaroon : Colors.white,
                                borderRadius: BorderRadius.circular(5),
                                border: Border.all(
                                  color: _rememberMe ? darkMaroon : borderGray,
                                  width: 1.5,
                                ),
                              ),
                              child: _rememberMe
                                  ? const Icon(
                                      Icons.check_rounded,
                                      size: 13,
                                      color: Colors.white,
                                    )
                                  : null,
                            ),
                            const SizedBox(width: 8),
                            Text(
                              'Ingat Saya',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 12.5,
                                fontWeight: FontWeight.w600,
                                color: const Color(0xFF4A3D36),
                              ),
                            ),
                          ],
                        ),
                      ),

                      // Lupa Kata Sandi?
                      GestureDetector(
                        onTap: () {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Kata sandi demo: password123'),
                              backgroundColor: darkMaroon,
                            ),
                          );
                        },
                        child: Text(
                          'Lupa Kata Sandi?',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF5A1920),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // 7. Tombol Masuk Sekarang →
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton(
                      onPressed: (authProvider.isLoading || _isSubmitting) ? null : _handleLogin,
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
                                  'Masuk Sekarang',
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
                  const SizedBox(height: 24),

                  // 8. Divider: ATAU MASUK LEBIH CEPAT DENGAN
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          height: 0.8,
                          color: const Color(0xFFDFD7CE),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 10),
                        child: Text(
                          'ATAU MASUK LEBIH CEPAT DENGAN',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 9.5,
                            fontWeight: FontWeight.w700,
                            letterSpacing: 0.8,
                            color: const Color(0xFF94847B),
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

                  // 9. Tombol Masuk dengan Akun Google
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: OutlinedButton(
                      onPressed: (authProvider.isLoading || _isSubmitting) ? null : _handleGoogleLogin,
                      style: OutlinedButton.styleFrom(
                        backgroundColor: Colors.white,
                        side: const BorderSide(color: borderGray, width: 1.2),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(25),
                        ),
                        elevation: 1,
                        shadowColor: Colors.black.withAlpha(15),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          // Logo Google 'G'
                          Container(
                            width: 20,
                            height: 20,
                            alignment: Alignment.center,
                            child: RichText(
                              text: TextSpan(
                                style: GoogleFonts.poppins(
                                  fontSize: 15,
                                  fontWeight: FontWeight.w800,
                                ),
                                children: const [
                                  TextSpan(
                                    text: 'G',
                                    style: TextStyle(color: Color(0xFF4285F4)),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Text(
                            'Masuk dengan Akun Google',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF331317),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 26),

                  // 10. Link Register
                  Center(
                    child: GestureDetector(
                      onTap: () {
                        authProvider.clearError();
                        context.push('/register');
                      },
                      child: RichText(
                        text: TextSpan(
                          text: 'Belum punya akun Raso? ',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 13,
                            color: const Color(0xFF6B5D55),
                          ),
                          children: [
                            TextSpan(
                              text: 'Daftar sekarang',
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
                  const SizedBox(height: 32),

                  // 11. Footer Motto
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
                          '“Citarasa Nan Sabana Raso”',
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
                  const SizedBox(height: 16),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
