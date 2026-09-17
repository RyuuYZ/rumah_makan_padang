import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../models/menu_item_model.dart';
import '../../providers/cart_provider.dart';
import '../../theme/app_colors.dart';
import '../../theme/app_typography.dart';
import '../../utils/currency_formatter.dart';
import '../../widgets/primary_button.dart';
import '../../widgets/quantity_stepper.dart';

/// Modal bottom sheet detail makanan masakan Padang
class MenuDetailModal extends StatefulWidget {
  final MenuItemModel item;

  const MenuDetailModal({super.key, required this.item});

  static void show(BuildContext context, MenuItemModel item) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => MenuDetailModal(item: item),
    );
  }

  @override
  State<MenuDetailModal> createState() => _MenuDetailModalState();
}

class _MenuDetailModalState extends State<MenuDetailModal> {
  int _quantity = 1;
  final TextEditingController _notesController = TextEditingController();

  @override
  void dispose() {
    _notesController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final item = widget.item;
    final totalPrice = item.price * _quantity;

    return Container(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SafeArea(
        top: false,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Handle drag bar
              Center(
                child: Container(
                  margin: const EdgeInsets.only(top: 10, bottom: 6),
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppColors.border,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),

              // Gambar Makanan Besar
              Stack(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: AspectRatio(
                        aspectRatio: 16 / 9,
                        child: Image.network(
                          item.imageUrl,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) => Container(
                            color: AppColors.surfaceWarm,
                            child: const Icon(Icons.restaurant, color: AppColors.primary, size: 50),
                          ),
                        ),
                      ),
                    ),
                  ),

                  // Close button
                  Positioned(
                    top: 8,
                    right: 24,
                    child: Container(
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: IconButton(
                        icon: const Icon(Icons.close_rounded, size: 20, color: AppColors.textPrimary),
                        onPressed: () => Navigator.pop(context),
                      ),
                    ),
                  ),
                ],
              ),

              Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Kategori & Rating
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppColors.primarySurface,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            item.category,
                            style: AppTypography.caption.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                        Row(
                          children: [
                            const Icon(Icons.star_rounded, color: AppColors.gold, size: 18),
                            const SizedBox(width: 4),
                            Text(
                              '${item.rating} (${item.reviewCount} ulasan)',
                              style: AppTypography.caption.copyWith(
                                color: AppColors.textSecondary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Nama Menu & Harga
                    Text(
                      item.name,
                      style: AppTypography.heading2.copyWith(fontSize: 20),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      CurrencyFormatter.format(item.price),
                      style: AppTypography.priceLarge.copyWith(fontSize: 20),
                    ),
                    const SizedBox(height: 12),

                    // Deskripsi Menu
                    Text(
                      'Deskripsi Sajian',
                      style: AppTypography.subtitle2.copyWith(fontWeight: FontWeight.w700),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      item.description,
                      style: AppTypography.bodyMedium.copyWith(color: AppColors.textSecondary),
                    ),
                    const SizedBox(height: 12),

                    // Info Porsi & Level Pedas
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: AppColors.surfaceWarm,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppColors.borderSubtle),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.lunch_dining_rounded,
                              color: AppColors.primary, size: 20),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              item.portionInfo,
                              style: AppTypography.bodySmall.copyWith(
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ),
                          if (item.spiciness > 0)
                            Row(
                              children: [
                                const Text('Pedas: ', style: TextStyle(fontSize: 12)),
                                ...List.generate(
                                  item.spiciness,
                                  (_) => const Icon(
                                    Icons.local_fire_department_rounded,
                                    color: Colors.deepOrange,
                                    size: 15,
                                  ),
                                ),
                              ],
                            ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Catatan Khusus
                    Text(
                      'Catatan Tambahan (Opsional)',
                      style: AppTypography.subtitle2.copyWith(fontWeight: FontWeight.w700),
                    ),
                    const SizedBox(height: 6),
                    TextField(
                      controller: _notesController,
                      decoration: InputDecoration(
                        hintText: 'Misal: Kuah gulai banyakin, sambal dipisah ya uni...',
                        hintStyle: AppTypography.bodySmall.copyWith(color: AppColors.textMuted),
                        filled: true,
                        fillColor: AppColors.surfaceWarm,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.border),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Bottom Action Row: Stepper + Add Button
                    Row(
                      children: [
                        QuantityStepper(
                          quantity: _quantity,
                          onIncrement: () {
                            setState(() {
                              _quantity++;
                            });
                          },
                          onDecrement: () {
                            if (_quantity > 1) {
                              setState(() {
                                _quantity--;
                              });
                            }
                          },
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: PrimaryButton(
                            text: 'Tambah • ${CurrencyFormatter.format(totalPrice)}',
                            onPressed: () {
                              final cartProvider = context.read<CartProvider>();
                              cartProvider.addItem(
                                item,
                                quantity: _quantity,
                                notes: _notesController.text.trim(),
                              );
                              Navigator.pop(context);

                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(
                                    '$_quantity x ${item.name} berhasil ditambahkan!',
                                    style: const TextStyle(color: Colors.white),
                                  ),
                                  backgroundColor: AppColors.primary,
                                  behavior: SnackBarBehavior.floating,
                                  duration: const Duration(seconds: 2),
                                  action: SnackBarAction(
                                    label: 'Lihat Keranjang',
                                    textColor: AppColors.goldLight,
                                    onPressed: () => context.push('/cart'),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
