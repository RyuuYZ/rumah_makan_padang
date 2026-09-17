import 'package:flutter/material.dart';
import '../models/order_model.dart';
import '../theme/app_colors.dart';
import '../theme/app_typography.dart';

/// Badge status pesanan dengan indikator warna sesuai tahapan
class StatusBadge extends StatelessWidget {
  final OrderStatus status;

  const StatusBadge({super.key, required this.status});

  @override
  Widget build(BuildContext context) {
    Color bgColor;
    Color textColor;
    IconData icon;

    switch (status) {
      case OrderStatus.confirmed:
        bgColor = AppColors.infoBg;
        textColor = AppColors.info;
        icon = Icons.receipt_long_rounded;
        break;
      case OrderStatus.cooking:
        bgColor = AppColors.warningBg;
        textColor = AppColors.warning;
        icon = Icons.soup_kitchen_rounded;
        break;
      case OrderStatus.delivering:
        bgColor = const Color(0xFFFFF8E1);
        textColor = const Color(0xFFF57F17);
        icon = Icons.two_wheeler_rounded;
        break;
      case OrderStatus.delivered:
        bgColor = AppColors.successBg;
        textColor = AppColors.success;
        icon = Icons.check_circle_rounded;
        break;
      case OrderStatus.cancelled:
        bgColor = AppColors.errorBg;
        textColor = AppColors.error;
        icon = Icons.cancel_rounded;
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: textColor.withAlpha(60), width: 0.8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: textColor),
          const SizedBox(width: 4),
          Text(
            status.label,
            style: AppTypography.caption.copyWith(
              color: textColor,
              fontWeight: FontWeight.w700,
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }
}
