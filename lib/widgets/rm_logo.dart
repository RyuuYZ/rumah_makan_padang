import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_colors.dart';

/// Widget Logo Rasa / Raso Mandeh
/// Sesuai identitas visual mockup Figma:
/// Monogram serif "RM" berwarna merah crimson pekat, nama brand "Raso Mandeh",
/// sub-judul "RUMAH MAKAN MINANG", dan kutipan filosofis Minang.
class RmLogo extends StatelessWidget {
  final double size;
  final bool showText;
  final bool showTagline;
  final bool isDark;

  const RmLogo({
    super.key,
    this.size = 80,
    this.showText = true,
    this.showTagline = false,
    this.isDark = false,
  });

  @override
  Widget build(BuildContext context) {
    final titleColor = isDark ? Colors.white : const Color(0xFF2E1114);
    final subtitleColor = isDark ? AppColors.goldLight : const Color(0xFF86756D);
    final quoteColor = isDark ? AppColors.textLight.withAlpha(200) : const Color(0xFF5D4C46);

    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // Official Red Interlocking RM Monogram from Website
        Image.asset(
          'assets/images/rm_monogram.png',
          height: size,
          fit: BoxFit.contain,
        ),

        if (showText) ...[
          SizedBox(height: size * 0.18),
          // Brand Name: Raso Mandeh
          Text(
            'Raso Mandeh',
            style: GoogleFonts.playfairDisplay(
              fontSize: size * 0.38,
              fontWeight: FontWeight.w800,
              color: titleColor,
              letterSpacing: 0.2,
            ),
          ),
          const SizedBox(height: 6),

          // Subtitle: ── RUMAH MAKAN PADANG ──
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 28,
                height: 0.8,
                color: const Color(0xFFC8BDB2),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                child: Text(
                  'RUMAH MAKAN PADANG',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: (size * 0.13).clamp(9.0, 11.5),
                    fontWeight: FontWeight.w600,
                    letterSpacing: 2.2,
                    color: subtitleColor,
                  ),
                ),
              ),
              Container(
                width: 28,
                height: 0.8,
                color: const Color(0xFFC8BDB2),
              ),
            ],
          ),
        ],

        if (showTagline) ...[
          const SizedBox(height: 24),
          // Tagline filosofi Minang: "Rasa yang tak pulang tanpa diingat."
          Text(
            '“Rasa yang tak pulang tanpa diingat.”',
            style: GoogleFonts.playfairDisplay(
              fontSize: 14.5,
              fontStyle: FontStyle.italic,
              fontWeight: FontWeight.w500,
              color: quoteColor,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ],
    );
  }
}
