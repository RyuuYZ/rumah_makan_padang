import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../routes/app_router.dart';
import '../theme/app_colors.dart';

/// Helper terpusat untuk menampilkan notifikasi SnackBar keranjang belanja.
class SnackBarHelper {
  /// Menampilkan SnackBar penambahan item ke keranjang dengan tombol 'Lihat Keranjang'.
  ///
  /// Menggunakan [rootScaffoldMessengerKey] dan [appRouter] agar:
  /// 1. Tombol 'Lihat Keranjang' tetap berfungsi meski dipanggil setelah modal ditutup (pop).
  /// 2. [persist] diset ke false agar SnackBar otomatis menghilang setelah durasi berlalu.
  /// 3. Dapat di-swipe ke kiri/kanan untuk menutup langsung.
  static void showCartSnackBar({
    required String message,
    BuildContext? context,
    Duration duration = const Duration(seconds: 3),
  }) {
    ScaffoldMessengerState? messenger;
    if (context != null && context.mounted) {
      messenger = ScaffoldMessenger.maybeOf(context);
    }
    messenger ??= rootScaffoldMessengerKey.currentState;
    if (messenger == null) return;

    try {
      // Bersihkan snackbar aktif sebelumnya agar tidak menumpuk
      messenger.clearSnackBars();

      messenger.showSnackBar(
        SnackBar(
          content: Text(
            message,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w600,
              fontSize: 13,
            ),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          backgroundColor: AppColors.primary,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          duration: duration,
          persist: false, // Memastikan SnackBar otomatis menghilang setelah durasi
          dismissDirection: DismissDirection.horizontal,
          action: SnackBarAction(
            label: 'Lihat Keranjang',
            textColor: AppColors.goldLight,
            onPressed: () {
              // Tutup SnackBar seketika saat tombol ditekan
              try {
                messenger?.hideCurrentSnackBar();
                rootScaffoldMessengerKey.currentState?.hideCurrentSnackBar();
              } catch (_) {}

              // Arahkan ke halaman keranjang
              try {
                appRouter.push('/cart');
              } catch (_) {
                if (context != null && context.mounted) {
                  try {
                    context.push('/cart');
                  } catch (_) {}
                }
              }
            },
          ),
        ),
      );
    } catch (_) {}
  }
}
