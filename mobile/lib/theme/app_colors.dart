import 'package:flutter/material.dart';

/// Palet warna utama aplikasi Rasa Mandeh
/// Mengusung identitas visual Minangkabau modern:
/// Latar krem gading, aksen marun tua elegan, dan sentuhan emas.
class AppColors {
  AppColors._();

  // Primary: Marun Tua Minang
  static const Color primary = Color(0xFF70121A);
  static const Color primaryDark = Color(0xFF4A0B11);
  static const Color primaryLight = Color(0xFF8C1B26);
  static const Color primarySurface = Color(0xFFFBECEE);

  // Secondary: Emas / Kuning Keemasan
  static const Color gold = Color(0xFFD4AF37);
  static const Color goldLight = Color(0xFFF7E6AA);
  static const Color goldDark = Color(0xFFB08C20);
  static const Color goldSurface = Color(0xFFFDF8E8);

  // Backgrounds: Krem & Putih Gading
  static const Color background = Color(0xFFFAF7F0);
  static const Color surface = Color(0xFFFFFFFF);
  static const Color surfaceMuted = Color(0xFFF4EFE6);
  static const Color surfaceWarm = Color(0xFFEFE8DB);

  // Text Colors
  static const Color textPrimary = Color(0xFF221714);
  static const Color textSecondary = Color(0xFF6B5F58);
  static const Color textMuted = Color(0xFF998D85);
  static const Color textLight = Color(0xFFFAF7F0);
  static const Color textOnPrimary = Colors.white;

  // Borders & Dividers
  static const Color border = Color(0xFFE8E1D5);
  static const Color borderSubtle = Color(0xFFF0EBE1);

  // Functional Status Colors
  static const Color success = Color(0xFF2E7D32);
  static const Color successBg = Color(0xFFE8F5E9);
  static const Color warning = Color(0xFFE65100);
  static const Color warningBg = Color(0xFFFFF3E0);
  static const Color error = Color(0xFFC62828);
  static const Color errorBg = Color(0xFFFFEBEE);
  static const Color info = Color(0xFF1565C0);
  static const Color infoBg = Color(0xFFE3F2FD);

  // Gradients
  static const LinearGradient maroonGradient = LinearGradient(
    colors: [primaryLight, primary, primaryDark],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient goldGradient = LinearGradient(
    colors: [goldLight, gold, goldDark],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient cardOverlay = LinearGradient(
    colors: [Colors.transparent, Color(0xCC221714)],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );
}
