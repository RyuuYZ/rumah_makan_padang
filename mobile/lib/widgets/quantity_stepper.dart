import 'package:flutter/material.dart';
import '../theme/app_colors.dart';
import '../theme/app_typography.dart';

/// Stepper kuantitas porsi makanan (+ / -)
class QuantityStepper extends StatelessWidget {
  final int quantity;
  final VoidCallback onIncrement;
  final VoidCallback onDecrement;
  final bool isCompact;

  const QuantityStepper({
    super.key,
    required this.quantity,
    required this.onIncrement,
    required this.onDecrement,
    this.isCompact = false,
  });

  @override
  Widget build(BuildContext context) {
    final btnSize = isCompact ? 28.0 : 34.0;
    final iconSize = isCompact ? 14.0 : 18.0;

    return Container(
      decoration: BoxDecoration(
        color: AppColors.surfaceWarm,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.border, width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Tombol Minus
          Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(9),
              onTap: onDecrement,
              child: SizedBox(
                width: btnSize,
                height: btnSize,
                child: Icon(
                  quantity == 1 ? Icons.delete_outline_rounded : Icons.remove_rounded,
                  size: iconSize,
                  color: quantity == 1 ? AppColors.error : AppColors.primary,
                ),
              ),
            ),
          ),

          // Kuantitas Text
          Padding(
            padding: EdgeInsets.symmetric(horizontal: isCompact ? 8.0 : 12.0),
            child: Text(
              '$quantity',
              style: AppTypography.subtitle2.copyWith(
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
                fontSize: isCompact ? 13 : 15,
              ),
            ),
          ),

          // Tombol Plus
          Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(9),
              onTap: onIncrement,
              child: Container(
                width: btnSize,
                height: btnSize,
                decoration: BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.circular(9),
                ),
                child: Icon(
                  Icons.add_rounded,
                  size: iconSize,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
