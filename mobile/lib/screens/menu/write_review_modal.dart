import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../models/menu_item_model.dart';
import '../../services/api_service.dart';
import '../../theme/app_colors.dart';
import '../../theme/app_typography.dart';
import '../../widgets/primary_button.dart';

/// Modal dialog interaktif untuk menulis ulasan menu makanan
class WriteReviewModal extends StatefulWidget {
  final MenuItemModel item;
  final VoidCallback onReviewSubmitted;

  const WriteReviewModal({
    super.key,
    required this.item,
    required this.onReviewSubmitted,
  });

  static Future<void> show(
    BuildContext context, {
    required MenuItemModel item,
    required VoidCallback onReviewSubmitted,
  }) {
    return showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => WriteReviewModal(
        item: item,
        onReviewSubmitted: onReviewSubmitted,
      ),
    );
  }

  @override
  State<WriteReviewModal> createState() => _WriteReviewModalState();
}

class _WriteReviewModalState extends State<WriteReviewModal> {
  int _selectedRating = 5;
  final _nameController = TextEditingController();
  final _commentController = TextEditingController();
  bool _isSubmitting = false;

  final Map<int, String> _ratingLabels = {
    1: 'Kurang Pas 😞',
    2: 'Cukup 🙂',
    3: 'Lumayan Enak 😋',
    4: 'Enak Sekali! 😍',
    5: 'Sangat Puas & Autentik! ⭐⭐⭐⭐⭐',
  };

  @override
  void dispose() {
    _nameController.dispose();
    _commentController.dispose();
    super.dispose();
  }

  Future<void> _submitReview() async {
    final name = _nameController.text.trim().isEmpty
        ? 'Pelanggan Raso Mandeh'
        : _nameController.text.trim();
    final comment = _commentController.text.trim();

    if (comment.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan tuliskan ulasan atau pengalaman rasa Anda.'),
          backgroundColor: Color(0xFFB71C1C),
        ),
      );
      return;
    }

    setState(() {
      _isSubmitting = true;
    });

    final success = await ApiService.submitReview(
      menuItemId: widget.item.id,
      customerName: name,
      rating: _selectedRating,
      comment: comment,
    );

    if (!mounted) return;

    setState(() {
      _isSubmitting = false;
    });

    if (success) {
      widget.onReviewSubmitted();
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              const Icon(Icons.check_circle_rounded, color: Colors.white, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Terima kasih! Ulasan Anda untuk ${widget.item.name} berhasil ditayangkan.',
                  style: const TextStyle(color: Colors.white, fontSize: 13),
                ),
              ),
            ],
          ),
          backgroundColor: const Color(0xFF2E7D32),
          behavior: SnackBarBehavior.floating,
          duration: const Duration(seconds: 3),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Gagal mengirim ulasan. Silakan periksa koneksi Anda.'),
          backgroundColor: Color(0xFFB71C1C),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.of(context).viewInsets.bottom;

    return Container(
      padding: EdgeInsets.fromLTRB(20, 16, 20, 20 + bottomInset),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Handle bar
            Center(
              child: Container(
                width: 42,
                height: 4,
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: const Color(0xFFE2DCD5),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),

            // Header Title
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Tulis Ulasan Rasa',
                      style: GoogleFonts.playfairDisplay(
                        fontSize: 20,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF301115),
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      widget.item.name,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
                IconButton(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.close_rounded, color: Color(0xFF7C6C64)),
                ),
              ],
            ),
            const Divider(height: 24, color: Color(0xFFF0EAE1)),

            // Star Rating Picker
            Center(
              child: Column(
                children: [
                  Text(
                    'Bagaimana kenikmatan hidangan ini?',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w600,
                      color: const Color(0xFF4A3525),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(5, (index) {
                      final starValue = index + 1;
                      final isSelected = starValue <= _selectedRating;
                      return GestureDetector(
                        onTap: () {
                          setState(() {
                            _selectedRating = starValue;
                          });
                        },
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 4),
                          child: Icon(
                            isSelected ? Icons.star_rounded : Icons.star_outline_rounded,
                            color: isSelected ? const Color(0xFFDF9C36) : const Color(0xFFD5C8B8),
                            size: 38,
                          ),
                        ),
                      );
                    }),
                  ),
                  const SizedBox(height: 6),
                  AnimatedSwitcher(
                    duration: const Duration(milliseconds: 200),
                    child: Container(
                      key: ValueKey(_selectedRating),
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFF8E7),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFDF9C36).withAlpha(80)),
                      ),
                      child: Text(
                        _ratingLabels[_selectedRating] ?? '',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11.5,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF8A5500),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 18),

            // Input Nama Pelanggan
            Text(
              'Nama Anda',
              style: AppTypography.subtitle2.copyWith(fontWeight: FontWeight.w700, fontSize: 13),
            ),
            const SizedBox(height: 6),
            TextField(
              controller: _nameController,
              decoration: InputDecoration(
                hintText: 'Nama panggilan (misal: Uni Nita / Uda Rian)',
                hintStyle: AppTypography.bodySmall.copyWith(color: AppColors.textMuted),
                filled: true,
                fillColor: const Color(0xFFFAF7F2),
                contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFFE8DFD5)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFFE8DFD5)),
                ),
              ),
            ),
            const SizedBox(height: 14),

            // Input Ulasan / Komentar
            Text(
              'Ulasan / Pengalaman Rasa',
              style: AppTypography.subtitle2.copyWith(fontWeight: FontWeight.w700, fontSize: 13),
            ),
            const SizedBox(height: 6),
            TextField(
              controller: _commentController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'Ceritakan rasa bumbu, keempukan daging, atau aroma masakannya...',
                hintStyle: AppTypography.bodySmall.copyWith(color: AppColors.textMuted),
                filled: true,
                fillColor: const Color(0xFFFAF7F2),
                contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFFE8DFD5)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFFE8DFD5)),
                ),
              ),
            ),
            const SizedBox(height: 20),

            // Tombol Kirim Ulasan
            SizedBox(
              width: double.infinity,
              child: PrimaryButton(
                text: _isSubmitting ? 'Mengirim Ulasan...' : 'Kirim Ulasan Sekarang',
                onPressed: _isSubmitting ? null : _submitReview,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
