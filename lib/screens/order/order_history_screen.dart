import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../models/order_model.dart';
import '../../providers/cart_provider.dart';
import '../../providers/order_provider.dart';
import '../../theme/app_colors.dart';
import '../../theme/app_typography.dart';
import '../../utils/currency_formatter.dart';
import '../../widgets/status_badge.dart';

/// Halaman Riwayat Pesanan Rasa Mandeh
class OrderHistoryScreen extends StatelessWidget {
  const OrderHistoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(
          title: Text('Riwayat Pesanan', style: AppTypography.heading2.copyWith(fontSize: 20)),
          bottom: TabBar(
            labelColor: AppColors.primary,
            unselectedLabelColor: AppColors.textMuted,
            indicatorColor: AppColors.primary,
            indicatorWeight: 3,
            labelStyle: AppTypography.subtitle2.copyWith(fontWeight: FontWeight.w700),
            tabs: [
              Tab(
                text: 'Pesanan Aktif (${orderProvider.activeOrders.length})',
              ),
              Tab(
                text: 'Selesai (${orderProvider.pastOrders.length})',
              ),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            // Tab 1: Pesanan Aktif
            _buildOrderList(context, orderProvider.activeOrders, isActiveTab: true),
            // Tab 2: Riwayat Selesai
            _buildOrderList(context, orderProvider.pastOrders, isActiveTab: false),
          ],
        ),
      ),
    );
  }

  Widget _buildOrderList(
    BuildContext context,
    List<OrderModel> orders, {
    required bool isActiveTab,
  }) {
    if (orders.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                isActiveTab ? Icons.pending_actions_rounded : Icons.history_rounded,
                size: 64,
                color: AppColors.textMuted,
              ),
              const SizedBox(height: 16),
              Text(
                isActiveTab ? 'Tidak Ada Pesanan Aktif' : 'Belum Ada Riwayat Pesanan',
                style: AppTypography.subtitle1,
              ),
              const SizedBox(height: 6),
              Text(
                isActiveTab
                    ? 'Pesanan yang Anda buat akan muncul dan dapat dilacak di sini.'
                    : 'Pesanan yang telah selesai atau dibatalkan akan tersimpan di sini.',
                style: AppTypography.bodySmall,
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      );
    }

    final dateFormat = DateFormat('dd MMM yyyy, HH:mm');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: orders.length,
      separatorBuilder: (context, index) => const SizedBox(height: 12),
      itemBuilder: (context, index) {
        final order = orders[index];
        final formattedDate = dateFormat.format(order.createdAt);
        final firstItemTitle = order.items.isNotEmpty
            ? order.items.first.menuItem.name
            : 'Paket Hidangan Minang';
        final otherCount = order.items.length - 1;

        return Container(
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.borderSubtle),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withAlpha(8),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () {
                context.push('/orders/${order.id}');
              },
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Top: ID & Status Badge
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            order.id,
                            style: AppTypography.subtitle2.copyWith(
                              fontWeight: FontWeight.w800,
                              color: AppColors.primary,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 8),
                        StatusBadge(status: order.status),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      formattedDate,
                      style: AppTypography.caption.copyWith(color: AppColors.textMuted),
                    ),
                    const Divider(height: 20),

                    // Ringkasan Items
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: AppColors.surfaceWarm,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.restaurant_rounded,
                              color: AppColors.primary, size: 20),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                otherCount > 0
                                    ? '$firstItemTitle + $otherCount menu lainnya'
                                    : firstItemTitle,
                                style: AppTypography.subtitle2.copyWith(fontSize: 13.5),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              Text(
                                '${order.items.fold(0, (t, i) => t + i.quantity)} porsi total • ${order.deliveryMethod == 'delivery' ? 'Antar Kurir' : 'Ambil di Resto'}',
                                style: AppTypography.caption,
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    // Info ETA Dinamis untuk Pesanan Aktif
                    if (isActiveTab)
                      Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFAF3E8),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: const Color(0xFFEEDDC8)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.access_time_filled_rounded, size: 14, color: Color(0xFFDF9C36)),
                            const SizedBox(width: 6),
                            Expanded(
                              child: Text(
                                'Estimasi Tiba: ${order.formattedEstimatedArrivalTime} (${order.formattedRemainingTime})',
                                style: AppTypography.caption.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: const Color(0xFF4A141A),
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),

                    // Info Alasan Pembatalan untuk Pesanan yang Dibatalkan
                    if (!isActiveTab && order.status == OrderStatus.cancelled)
                      Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFFF1F1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: const Color(0xFFF5C6CB)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.info_outline_rounded, size: 14, color: Color(0xFFC0151E)),
                            const SizedBox(width: 6),
                            Expanded(
                              child: Text(
                                'Alasan Batal: ${order.cancelReason ?? "Dibatalkan oleh pelanggan"}',
                                style: AppTypography.caption.copyWith(
                                  color: const Color(0xFF900B13),
                                  fontWeight: FontWeight.w600,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),

                    // Bottom: Total & Aksi
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Total Belanja', style: AppTypography.caption),
                            Text(
                              CurrencyFormatter.format(order.totalPrice),
                              style: AppTypography.priceLarge.copyWith(fontSize: 15),
                            ),
                          ],
                        ),
                        Row(
                          children: [
                            if (!isActiveTab && order.items.isNotEmpty)
                              OutlinedButton(
                                style: OutlinedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                  minimumSize: Size.zero,
                                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                ),
                                onPressed: () {
                                  final cart = context.read<CartProvider>();
                                  for (var item in order.items) {
                                    cart.addItem(item.menuItem,
                                        quantity: item.quantity, notes: item.notes);
                                  }
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('Menu ditambahkan kembali ke keranjang!'),
                                      backgroundColor: AppColors.primary,
                                    ),
                                  );
                                  context.go('/cart');
                                },
                                child: Text('Pesan Lagi', style: AppTypography.caption),
                              ),
                            const SizedBox(width: 8),
                            Icon(
                              Icons.arrow_forward_ios_rounded,
                              size: 14,
                              color: AppColors.textMuted,
                            ),
                          ],
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
