import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart' hide TextDirection;
import 'package:provider/provider.dart';
import '../../models/cart_item_model.dart';
import '../../models/menu_item_model.dart';
import '../../models/order_model.dart';
import '../../providers/order_provider.dart';
import '../../services/receipt_service.dart';
import '../../utils/currency_formatter.dart';

/// Halaman Detail Pesanan Rasa Mandeh (100% Persis Mockup Figma "order details page")
class OrderDetailScreen extends StatelessWidget {
  final String orderId;

  const OrderDetailScreen({super.key, this.orderId = '#RSO-88429'});

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();
    final order = orderProvider.getOrderById(orderId) ?? orderProvider.activeOrder;

    return PopScope(
      canPop: Navigator.of(context).canPop(),
      onPopInvokedWithResult: (didPop, result) {
        if (!didPop) {
          context.go('/orders');
        }
      },
      child: Scaffold(
        backgroundColor: const Color(0xFFFAF7F2),
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // 1. Title Status Pesanan with Back button
                Row(
                  children: [
                    InkWell(
                      onTap: () {
                        if (Navigator.of(context).canPop()) {
                          Navigator.of(context).pop();
                        } else {
                          context.go('/orders');
                        }
                      },
                      borderRadius: BorderRadius.circular(10),
                      child: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: const Color(0xFFEBE0D2)),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withAlpha(8),
                              blurRadius: 4,
                              offset: const Offset(0, 1),
                            ),
                          ],
                        ),
                        child: const Icon(
                          Icons.arrow_back_ios_new_rounded,
                          size: 16,
                          color: Color(0xFF301115),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Text(
                      'Status Pesanan',
                      style: GoogleFonts.playfairDisplay(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF301115),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),

              // 2. Bar Nomor Pesanan & Tombol Salin
              _buildOrderNumberBar(context, order?.id ?? '#RSO-88429'),
              const SizedBox(height: 16),

              // 3. Card 1: Status Stepper Dapur & Estimasi
              _buildCookingStatusCard(context, order),
              const SizedBox(height: 16),

              // 4. Card 2: Live Tracking Map & Informasi Kurir
              _buildLiveTrackingCard(context, order),
              const SizedBox(height: 16),

              // 5. Card 3: Rincian Menu Dipesan & Pembayaran
              _buildOrderSummaryCard(context, order),
              const SizedBox(height: 20),

              // 6. Action Buttons: Pesan Menu Lainnya & Struk Digital
              _buildActionButtons(context, order),
              const SizedBox(height: 24),

              // 7. Footer Motto Minang
              Center(
                child: Text(
                  '“Rasa yang tak pulang tanpa diingat.”',
                  style: GoogleFonts.playfairDisplay(
                    fontSize: 14,
                    fontStyle: FontStyle.italic,
                    fontWeight: FontWeight.w600,
                    color: const Color(0xFF4A141A),
                  ),
                ),
              ),
              const SizedBox(height: 18),
            ],
          ),
        ),
      ),
    ),
  );
}

  /// Bar Nomor Pesanan dengan Badge Monogram RM dan Tombol Salin
  Widget _buildOrderNumberBar(BuildContext context, String id) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: const Color(0xFFFAF5ED),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFEBE0D2)),
      ),
      child: Row(
        children: [
          Container(
            width: 26,
            height: 26,
            decoration: BoxDecoration(
              color: const Color(0xFFFAF7F2),
              shape: BoxShape.circle,
              border: Border.all(color: const Color(0xFFEBE0D2)),
            ),
            alignment: Alignment.center,
            child: Padding(
              padding: const EdgeInsets.all(3.0),
              child: Image.asset(
                'assets/images/rm_monogram.png',
                fit: BoxFit.contain,
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Row(
              children: [
                Text(
                  'No. Pesanan: ',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12.5,
                    color: const Color(0xFF7C6C64),
                    fontWeight: FontWeight.w500,
                  ),
                ),
                Flexible(
                  child: Text(
                    id,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12.5,
                      color: const Color(0xFF301115),
                      fontWeight: FontWeight.w800,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 6),
          InkWell(
            onTap: () {
              Clipboard.setData(ClipboardData(text: id));
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text('Nomor pesanan $id disalin ke clipboard'),
                  backgroundColor: const Color(0xFF4A141A),
                  behavior: SnackBarBehavior.floating,
                  duration: const Duration(seconds: 2),
                ),
              );
            },
            borderRadius: BorderRadius.circular(8),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    'Salin',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF8E662F),
                    ),
                  ),
                  const SizedBox(width: 4),
                  const Icon(
                    Icons.copy_rounded,
                    size: 14,
                    color: Color(0xFF8E662F),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _formatMilestoneTime(DateTime dt) {
    final h = dt.hour.toString().padLeft(2, '0');
    final m = dt.minute.toString().padLeft(2, '0');
    return '$h.$m WIB';
  }

  /// Card 1: Status Stepper Memasak & Estimasi Tiba Dinamis
  Widget _buildCookingStatusCard(BuildContext context, OrderModel? order) {
    final status = order?.status ?? OrderStatus.cooking;
    final currentStep = status.stepIndex;
    final isCancelled = status == OrderStatus.cancelled;

    // Ambil milestone dari riwayat log timeline
    final confirmedMilestone = order?.getMilestone(OrderStatus.confirmed);
    final cookingMilestone = order?.getMilestone(OrderStatus.cooking);
    final deliveringMilestone = order?.getMilestone(OrderStatus.delivering);
    final deliveredMilestone = order?.getMilestone(OrderStatus.delivered);
    final cancelledMilestone = order?.getMilestone(OrderStatus.cancelled);

    // Konfigurasi Header Card
    String pillText;
    Color pillDotColor;
    Color pillBgColor;
    Color pillTextColor;
    IconData headerIcon;

    switch (status) {
      case OrderStatus.confirmed:
        pillText = 'PESANAN DIKONFIRMASI';
        pillDotColor = const Color(0xFFDF9C36);
        pillBgColor = const Color(0xFFFBF4E8);
        pillTextColor = const Color(0xFF8A5D19);
        headerIcon = Icons.receipt_long_rounded;
        break;
      case OrderStatus.cooking:
        pillText = 'SEDANG DISIAPKAN';
        pillDotColor = const Color(0xFFC0151E);
        pillBgColor = const Color(0xFFF6ECEC);
        pillTextColor = const Color(0xFF4A141A);
        headerIcon = Icons.soup_kitchen_rounded;
        break;
      case OrderStatus.delivering:
        pillText = 'SEDANG DIANTAR';
        pillDotColor = const Color(0xFF1E88E5);
        pillBgColor = const Color(0xFFE8F1FC);
        pillTextColor = const Color(0xFF0D47A1);
        headerIcon = Icons.delivery_dining_rounded;
        break;
      case OrderStatus.delivered:
        pillText = 'PESANAN SAMPAI';
        pillDotColor = const Color(0xFF2E7D32);
        pillBgColor = const Color(0xFFE8F5E9);
        pillTextColor = const Color(0xFF1B5E20);
        headerIcon = Icons.check_circle_rounded;
        break;
      case OrderStatus.cancelled:
        pillText = 'PESANAN DIBATALKAN';
        pillDotColor = const Color(0xFFC0151E);
        pillBgColor = const Color(0xFFFCEAEA);
        pillTextColor = const Color(0xFF900B13);
        headerIcon = Icons.cancel_rounded;
        break;
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFEFE8DD)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header: Status Pill & Ikon
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: pillBgColor,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: pillDotColor.withAlpha(60)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 7,
                      height: 7,
                      decoration: BoxDecoration(
                        color: pillDotColor,
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Text(
                      pillText,
                      style: GoogleFonts.plusJakartaSans(
                        color: pillTextColor,
                        fontSize: 10.5,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  color: const Color(0xFFF7EFE6),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  headerIcon,
                  color: const Color(0xFF4A141A),
                  size: 22,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Estimasi Tiba Box (Dinamis Berdasarkan Status & Waktu Riil)
          if (isCancelled)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF1F1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFF5C6CB)),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.error_outline_rounded,
                    color: Color(0xFFC0151E),
                    size: 26,
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'PESANAN TELAH DIBATALKAN',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 10,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF900B13),
                            letterSpacing: 0.5,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          order?.cancelReason ?? 'Dibatalkan oleh pelanggan',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w600,
                            color: const Color(0xFF5C1014),
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (cancelledMilestone != null)
                    Text(
                      _formatMilestoneTime(cancelledMilestone.timestamp),
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        color: const Color(0xFF900B13),
                      ),
                    ),
                ],
              ),
            )
          else if (status == OrderStatus.delivered)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
              decoration: BoxDecoration(
                color: const Color(0xFFE8F5E9),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFC8E6C9)),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.verified_rounded,
                    color: Color(0xFF2E7D32),
                    size: 26,
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'PESANAN SELESAI',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF1B5E20),
                            letterSpacing: 0.5,
                          ),
                        ),
                        Text(
                          'Telah sampai di alamat tujuan',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 13.5,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF1B5E20),
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (deliveredMilestone != null)
                    Text(
                      _formatMilestoneTime(deliveredMilestone.timestamp),
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1B5E20),
                      ),
                    ),
                ],
              ),
            )
          else
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
              decoration: BoxDecoration(
                color: const Color(0xFFFAF3E8),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFEEDDC8)),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.access_time_filled_rounded,
                    color: Color(0xFFDF9C36),
                    size: 26,
                  ),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'ESTIMASI TIBA',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 9.5,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF8C7B70),
                          letterSpacing: 0.5,
                        ),
                      ),
                      Text(
                        order != null ? order.formattedRemainingTime : '15 – 25 Menit',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14.5,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF301115),
                        ),
                      ),
                    ],
                  ),
                  const Spacer(),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        'Kira-kira tiba pukul',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 10.5,
                          color: const Color(0xFF8C7B70),
                        ),
                      ),
                      Text(
                        order != null ? order.formattedEstimatedArrivalTime : '12.45 WIB',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF301115),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          const SizedBox(height: 18),

          // Stepper Timeline Dinamis
          if (isCancelled) ...[
            _buildTimelineItem(
              isDone: true,
              isActive: false,
              isLast: false,
              time: confirmedMilestone != null
                  ? _formatMilestoneTime(confirmedMilestone.timestamp)
                  : (order != null ? _formatMilestoneTime(order.createdAt) : '12.18 WIB'),
              title: 'Pesanan Diterima Dapur',
              subtitle: 'Rincian pesanan terverifikasi & pembayaran tervalidasi',
              icon: Icons.check_rounded,
            ),
            if (cookingMilestone != null)
              _buildTimelineItem(
                isDone: true,
                isActive: false,
                isLast: false,
                time: _formatMilestoneTime(cookingMilestone.timestamp),
                title: 'Sedang Dimasak & Dibungkus',
                subtitle: 'Lauk dipanaskan di kuali tanah liat.',
                icon: Icons.restaurant_rounded,
              ),
            _buildTimelineItem(
              isDone: false,
              isActive: true,
              isLast: true,
              time: cancelledMilestone != null
                  ? _formatMilestoneTime(cancelledMilestone.timestamp)
                  : null,
              title: 'Pesanan Dibatalkan',
              subtitle: order?.cancelReason != null
                  ? 'Alasan: ${order?.cancelReason}'
                  : 'Pesanan telah dibatalkan oleh pengguna.',
              badgeText: 'Batal',
              icon: Icons.cancel_rounded,
            ),
          ] else ...[
            _buildTimelineItem(
              isDone: currentStep > 0,
              isActive: currentStep == 0,
              isLast: false,
              time: confirmedMilestone != null
                  ? _formatMilestoneTime(confirmedMilestone.timestamp)
                  : (order != null ? _formatMilestoneTime(order.createdAt) : '12.18 WIB'),
              title: 'Pesanan Diterima Dapur',
              subtitle: confirmedMilestone?.description ??
                  'Rincian pesanan terverifikasi & pembayaran tervalidasi',
              badgeText: currentStep == 0 ? 'Proses' : null,
              icon: Icons.check_rounded,
            ),
            _buildTimelineItem(
              isDone: currentStep > 1,
              isActive: currentStep == 1,
              isLast: false,
              time: cookingMilestone != null
                  ? _formatMilestoneTime(cookingMilestone.timestamp)
                  : null,
              title: 'Sedang Dimasak & Dibungkus',
              subtitle: cookingMilestone?.description ??
                  'Lauk dipanaskan di kuali tanah liat & dibungkus daun pisang berlapis.',
              badgeText: currentStep == 1 ? 'Proses' : null,
              icon: Icons.restaurant_rounded,
            ),
            _buildTimelineItem(
              isDone: currentStep > 2,
              isActive: currentStep == 2,
              isLast: false,
              time: deliveringMilestone != null
                  ? _formatMilestoneTime(deliveringMilestone.timestamp)
                  : null,
              title: 'Kurir Menjemput & Mengantar',
              subtitle: deliveringMilestone?.description ??
                  'Kurir ${order?.courierName ?? "Ranah Express"} bersiap mengantar pesanan Anda.',
              badgeText: currentStep == 2 ? 'Diantar' : null,
              icon: Icons.delivery_dining_rounded,
            ),
            _buildTimelineItem(
              isDone: currentStep == 3,
              isActive: false,
              isLast: true,
              time: deliveredMilestone != null
                  ? _formatMilestoneTime(deliveredMilestone.timestamp)
                  : null,
              title: 'Pesanan Tiba & Siap Disajikan',
              subtitle: deliveredMilestone?.description ??
                  'Nikmati selagi hangat bersama kerabat.',
              icon: Icons.roofing_rounded,
            ),
          ],
        ],
      ),
    );
  }

  /// Satu baris dalam Stepper Timeline
  Widget _buildTimelineItem({
    required bool isDone,
    required bool isActive,
    required bool isLast,
    required String title,
    required String subtitle,
    required IconData icon,
    String? time,
    String? badgeText,
  }) {
    final indicatorColor = (isDone || isActive)
        ? const Color(0xFF4A141A)
        : const Color(0xFFEDE4D8);
    final iconColor = (isDone || isActive)
        ? Colors.white
        : const Color(0xFFA09488);
    final lineColor = isDone
        ? const Color(0xFF4A141A)
        : const Color(0xFFDDD2C4);

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Kolom Indikator & Garis Vertikal
          SizedBox(
            width: 28,
            child: Column(
              children: [
                Container(
                  width: 24,
                  height: 24,
                  decoration: BoxDecoration(
                    color: indicatorColor,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(icon, color: iconColor, size: 13.5),
                ),
                if (!isLast)
                  Expanded(
                    child: Container(
                      width: 2,
                      margin: const EdgeInsets.symmetric(vertical: 3),
                      color: lineColor,
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 12),

          // Konten Deskripsi Langkah
          Expanded(
            child: Padding(
              padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
              child: isActive
                  ? Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFAF3EA),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFEFE2D4)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  title,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 12.5,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF4A141A),
                                  ),
                                ),
                              ),
                              if (badgeText != null) ...[
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF4A141A),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Text(
                                    badgeText,
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 9.5,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                                ),
                              ],
                            ],
                          ),
                          const SizedBox(height: 3),
                          Text(
                            subtitle,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 11,
                              color: const Color(0xFF6C5D54),
                              height: 1.3,
                            ),
                          ),
                        ],
                      ),
                    )
                  : Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                title,
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 12.5,
                                  fontWeight: (isDone || isActive)
                                      ? FontWeight.w700
                                      : FontWeight.w600,
                                  color: (isDone || isActive)
                                      ? const Color(0xFF301115)
                                      : const Color(0xFF5C4E47),
                                ),
                              ),
                            ),
                            if (time != null) ...[
                              const SizedBox(width: 8),
                              Text(
                                time,
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10.5,
                                  color: const Color(0xFF8C7B70),
                                ),
                              ),
                            ],
                          ],
                        ),
                        const SizedBox(height: 2),
                        Text(
                          subtitle,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            color: const Color(0xFF7C6C64),
                            height: 1.3,
                          ),
                        ),
                      ],
                    ),
            ),
          ),
        ],
      ),
    );
  }

  /// Card 2: Live Tracking Map & Rincian Kurir Pengantar
  Widget _buildLiveTrackingCard(BuildContext context, OrderModel? order) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFEFE8DD)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Area Peta Vector Cantik dengan Rute & Floating Badge
          SizedBox(
            height: 128,
            width: double.infinity,
            child: Stack(
              children: [
                Positioned.fill(
                  child: CustomPaint(
                    painter: _MiniTrackingMapPainter(),
                  ),
                ),
                Positioned(
                  top: 10,
                  right: 12,
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: Colors.white.withAlpha(240),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE5DDD0)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withAlpha(12),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 6,
                          height: 6,
                          decoration: const BoxDecoration(
                            color: Color(0xFF2E7D32),
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 5),
                        Text(
                          'Pelacakan Langsung',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 10.5,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF301115),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),

          // Informasi Kurir: Uda Hendra Kurniawan
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            child: Row(
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: const Color(0xFFF5EFE6),
                    shape: BoxShape.circle,
                    border: Border.all(color: const Color(0xFFD8CCBC)),
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    'UH',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 14,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF4A141A),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Flexible(
                            child: Text(
                              'Uda Hendra Kurniawan',
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                color: const Color(0xFF301115),
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          const SizedBox(width: 4),
                          const Icon(
                            Icons.verified_rounded,
                            size: 15,
                            color: Color(0xFFDF9C36),
                          ),
                        ],
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'Raso Express • Honda Vario B 4821 SOX',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          color: const Color(0xFF7C6C64),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                // Tombol Panggil
                InkWell(
                  onTap: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Menghubungi Uda Hendra (+62 812-8829-1090)...'),
                        backgroundColor: Color(0xFF4A141A),
                        behavior: SnackBarBehavior.floating,
                      ),
                    );
                  },
                  borderRadius: BorderRadius.circular(18),
                  child: Container(
                    width: 36,
                    height: 36,
                    decoration: const BoxDecoration(
                      color: Color(0xFFF5EEE6),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.phone_rounded,
                      size: 17,
                      color: Color(0xFF4A141A),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                // Tombol Chat
                InkWell(
                  onTap: () => _showChatDialog(context),
                  borderRadius: BorderRadius.circular(18),
                  child: Container(
                    width: 36,
                    height: 36,
                    decoration: const BoxDecoration(
                      color: Color(0xFFFBE4C4),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.chat_bubble_outline_rounded,
                      size: 17,
                      color: Color(0xFF8A6B32),
                    ),
                  ),
                ),
              ],
            ),
          ),

          const Divider(height: 1, color: Color(0xFFEFE8DD)),

          // Alamat Antar Tujuan
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(
                  Icons.location_on_outlined,
                  color: Color(0xFF4A141A),
                  size: 20,
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'ALAMAT ANTAR',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 9.5,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF8A6B32),
                          letterSpacing: 0.6,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        order?.deliveryAddress ??
                            'Jl. Kemang Raya No. 14, Jakarta Selatan',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF301115),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        order?.notes.isNotEmpty == true
                            ? (order?.notes ?? '')
                            : 'Patokan: Pagar hitam depan Apotek. Titip di meja pos satpam.',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 11,
                          color: const Color(0xFF7C6C64),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Card 3: Rincian Menu Dipesan & Rincian Pembayaran
  Widget _buildOrderSummaryCard(BuildContext context, OrderModel? order) {
    // Menu items persis di mockup Figma
    final items = (order != null && order.items.isNotEmpty)
        ? order.items
        : [
            _SampleItem(
              name: 'Rendang Daging Sapi Karamel',
              desc: 'Bumbu kelapa hitam, daging empuk',
              price: 28000,
            ),
            _SampleItem(
              name: 'Paket Nasi Padang Komplit',
              desc: 'Gulai Cincang, sayur kapau, kuah lado',
              price: 38000,
            ),
            _SampleItem(
              name: 'Es Teh Talua Tradisional',
              desc: 'Kocokan telur bebek, jeruk nipis & teh pekat',
              price: 12000,
            ),
          ];

    final subtotal = order?.subtotal ?? 78000;
    final total = order?.totalPrice ?? 78000;

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFEFE8DD)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(6),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Rincian Menu Dipesan',
            style: GoogleFonts.playfairDisplay(
              fontSize: 17,
              fontWeight: FontWeight.w800,
              color: const Color(0xFF301115),
            ),
          ),
          const SizedBox(height: 14),

          // Daftar Item Makanan
          ...items.map((item) {
            final name = item is _SampleItem ? item.name : (item as dynamic).menuItem.name;
            final desc = item is _SampleItem ? item.desc : (item as dynamic).menuItem.description;
            final price = item is _SampleItem ? item.price : (item as dynamic).subtotal;
            final qty = item is _SampleItem ? 1 : (item as dynamic).quantity;

            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 7, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFEFE8DD),
                      borderRadius: BorderRadius.circular(7),
                    ),
                    child: Text(
                      '${qty}x',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11.5,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF4A3E39),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          name,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF301115),
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          desc,
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            color: const Color(0xFF7C6C64),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    CurrencyFormatter.format(price),
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF301115),
                    ),
                  ),
                ],
              ),
            );
          }),

          const SizedBox(height: 6),
          const Divider(height: 1, color: Color(0xFFEFE8DD)),
          const SizedBox(height: 12),

          // Rincian Biaya
          _buildSummaryRow(
            label: 'Subtotal Makanan',
            value: CurrencyFormatter.format(subtotal),
          ),
          const SizedBox(height: 6),
          _buildSummaryRow(
            label: 'Ongkos Kirim Minang Express',
            value: 'Gratis (Promo Raso)',
            valueColor: const Color(0xFF1B8A5A),
          ),
          const SizedBox(height: 6),
          _buildSummaryRow(
            label: 'Biaya Layanan & Pengemasan',
            value: 'Rp 0',
          ),
          const SizedBox(height: 12),
          const Divider(height: 1, color: Color(0xFFEFE8DD)),
          const SizedBox(height: 12),

          // Total Pembayaran & QRIS Status
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Total Pembayaran',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF301115),
                      ),
                    ),
                    const SizedBox(height: 3),
                    Row(
                      children: [
                        const Icon(
                          Icons.qr_code_2_rounded,
                          size: 14,
                          color: Color(0xFF1B8A5A),
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'QRIS BCA (Lunas • 12.18 WIB)',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 10.5,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF1B8A5A),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              Text(
                CurrencyFormatter.format(total),
                style: GoogleFonts.playfairDisplay(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF301115),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow({
    required String label,
    required String value,
    Color? valueColor,
  }) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11.5,
            color: const Color(0xFF7C6C64),
          ),
        ),
        Text(
          value,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11.5,
            fontWeight: FontWeight.w700,
            color: valueColor ?? const Color(0xFF301115),
          ),
        ),
      ],
    );
  }

  /// Tombol Aksi: Pesan Menu Lainnya, Struk Digital, dan Batalkan Pesanan
  Widget _buildActionButtons(BuildContext context, OrderModel? order) {
    final canCancel = order != null &&
        (order.status == OrderStatus.confirmed || order.status == OrderStatus.cooking);

    return Column(
      children: [
        // Tombol Emas Utama: Pesan Menu Lainnya
        SizedBox(
          width: double.infinity,
          height: 48,
          child: ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFFDF9C36),
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(24),
              ),
            ),
            onPressed: () => context.go('/menu'),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.replay_rounded,
                  color: Color(0xFF301115),
                  size: 19,
                ),
                const SizedBox(width: 8),
                Text(
                  'Pesan Menu Lainnya',
                  style: GoogleFonts.plusJakartaSans(
                    color: const Color(0xFF301115),
                    fontSize: 13.5,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 12),

        // Tombol Garis Maroon: Struk Digital
        SizedBox(
          width: double.infinity,
          height: 48,
          child: OutlinedButton(
            style: OutlinedButton.styleFrom(
              side: const BorderSide(color: Color(0xFF4A141A), width: 1.2),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(24),
              ),
            ),
            onPressed: () => _showDigitalReceipt(context, order),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.receipt_long_rounded,
                  color: Color(0xFF4A141A),
                  size: 19,
                ),
                const SizedBox(width: 8),
                Text(
                  'Struk Digital',
                  style: GoogleFonts.plusJakartaSans(
                    color: const Color(0xFF4A141A),
                    fontSize: 13.5,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ],
            ),
          ),
        ),

        // Tombol Merah Halus: Batalkan Pesanan (Hanya jika tahap awal: Confirmed/Cooking)
        if (canCancel) ...[
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            height: 46,
            child: OutlinedButton(
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: Color(0xFFC0151E), width: 1.2),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(24),
                ),
                backgroundColor: const Color(0xFFFFF7F7),
              ),
              onPressed: () => _showCancelOrderDialog(context, order),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.cancel_outlined,
                    color: Color(0xFFC0151E),
                    size: 18,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Batalkan Pesanan',
                    style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFFC0151E),
                      fontSize: 13,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ],
    );
  }

  /// Dialog Bottom Sheet Pilihan Alasan Pembatalan Pesanan
  void _showCancelOrderDialog(BuildContext context, OrderModel order) {
    final reasons = [
      'Ingin menambah atau mengubah pilihan menu',
      'Salah memasukkan alamat pengantaran',
      'Waktu estimasi tiba dirasa terlalu lama',
      'Ingin mengganti metode pembayaran',
      'Alasan lainnya',
    ];
    String selectedReason = reasons.first;
    final otherController = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (modalCtx) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Container(
              padding: EdgeInsets.only(
                top: 20,
                left: 20,
                right: 20,
                bottom: MediaQuery.of(context).viewInsets.bottom + 24,
              ),
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Center(
                      child: Container(
                        width: 40,
                        height: 4,
                        decoration: BoxDecoration(
                          color: const Color(0xFFDDD2C4),
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: const Color(0xFFFDE8E8),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.cancel_outlined, color: Color(0xFFC0151E), size: 22),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Batalkan Pesanan?',
                                style: GoogleFonts.playfairDisplay(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w800,
                                  color: const Color(0xFF301115),
                                ),
                              ),
                              Text(
                                'Pilih alasan pembatalan pesanan ${order.id}',
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 12,
                                  color: const Color(0xFF7C6C64),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    ...reasons.map((r) {
                      final isSelected = selectedReason == r;
                      return InkWell(
                        onTap: () {
                          setModalState(() {
                            selectedReason = r;
                          });
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          margin: const EdgeInsets.only(bottom: 8),
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                          decoration: BoxDecoration(
                            color: isSelected ? const Color(0xFFFAF3EA) : const Color(0xFFFAFAFA),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isSelected ? const Color(0xFF4A141A) : const Color(0xFFECE5DC),
                              width: isSelected ? 1.5 : 1,
                            ),
                          ),
                          child: Row(
                            children: [
                              Icon(
                                isSelected ? Icons.radio_button_checked : Icons.radio_button_unchecked,
                                color: isSelected ? const Color(0xFF4A141A) : const Color(0xFFA09488),
                                size: 18,
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  r,
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 13,
                                    fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                    color: isSelected ? const Color(0xFF301115) : const Color(0xFF5C4E47),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }),
                    if (selectedReason == 'Alasan lainnya') ...[
                      const SizedBox(height: 8),
                      TextField(
                        controller: otherController,
                        maxLines: 2,
                        decoration: InputDecoration(
                          hintText: 'Tuliskan alasan spesifik Anda...',
                          hintStyle: GoogleFonts.plusJakartaSans(fontSize: 12, color: const Color(0xFFA09488)),
                          filled: true,
                          fillColor: const Color(0xFFFAF7F2),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: const BorderSide(color: Color(0xFFE5DDD0)),
                          ),
                        ),
                      ),
                    ],
                    const SizedBox(height: 20),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton(
                            style: OutlinedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              side: const BorderSide(color: Color(0xFFDDD2C4)),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                            ),
                            onPressed: () => Navigator.pop(modalCtx),
                            child: Text(
                              'Kembali',
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF6C5D54),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          flex: 2,
                          child: ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFFC0151E),
                              elevation: 0,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                            ),
                            onPressed: () async {
                              final finalReason = selectedReason == 'Alasan lainnya' && otherController.text.trim().isNotEmpty
                                  ? otherController.text.trim()
                                  : selectedReason;
                              Navigator.pop(modalCtx);
                              await context.read<OrderProvider>().cancelOrder(order.id, reason: finalReason);
                              if (context.mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Pesanan ${order.id} berhasil dibatalkan.'),
                                    backgroundColor: const Color(0xFF4A141A),
                                    behavior: SnackBarBehavior.floating,
                                  ),
                                );
                              }
                            },
                            child: Text(
                              'Ya, Batalkan Pesanan',
                              style: GoogleFonts.plusJakartaSans(
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                                fontSize: 13,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  /// Modal Dialog Chat dengan Uda Kurir
  void _showChatDialog(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return Container(
          decoration: const BoxDecoration(
            color: Color(0xFFFAF7F2),
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          padding: EdgeInsets.only(
            left: 20,
            right: 20,
            top: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 20,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: const Color(0xFFD4C8B8),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Container(
                    width: 40,
                    height: 40,
                    decoration: const BoxDecoration(
                      color: Color(0xFF4A141A),
                      shape: BoxShape.circle,
                    ),
                    alignment: Alignment.center,
                    child: const Text('UH',
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Uda Hendra Kurniawan',
                          style: GoogleFonts.plusJakartaSans(
                              fontWeight: FontWeight.bold, fontSize: 14)),
                      Text('Kurir Raso Express • Aktif',
                          style: GoogleFonts.plusJakartaSans(
                              color: const Color(0xFF1B8A5A), fontSize: 11)),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFFAF3EA),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFEFE2D4)),
                ),
                child: Text(
                  'Halo Uda, pesanan sedang dibungkus daun pisang panas di dapur. Saya sudah siap di resto langsung tancap gas ke Kemang!',
                  style: GoogleFonts.plusJakartaSans(fontSize: 12, height: 1.4),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      decoration: InputDecoration(
                        hintText: 'Ketik pesan untuk Uda kurir...',
                        hintStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(20),
                          borderSide: const BorderSide(color: Color(0xFFEFE8DD)),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    style: IconButton.styleFrom(
                      backgroundColor: const Color(0xFFDF9C36),
                    ),
                    icon: const Icon(Icons.send_rounded, color: Color(0xFF301115), size: 18),
                    onPressed: () {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Pesan terkirim ke Uda Hendra'),
                          backgroundColor: Color(0xFF4A141A),
                        ),
                      );
                    },
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  /// Modal Bottom Sheet Struk Digital Resmi Minang
  void _showDigitalReceipt(BuildContext context, OrderModel? order) {
    final targetOrder = order ??
        OrderModel(
          id: '#RSO-88429',
          createdAt: DateTime(2026, 9, 14, 12, 18),
          items: const [
            CartItemModel(
              id: 'sample-1',
              menuItem: MenuItemModel(
                id: 'm-1',
                name: 'Rendang Daging Sapi Karamel',
                category: 'Lauk Utama',
                description: '',
                price: 28000,
                rating: 4.9,
                reviewCount: 120,
                imageUrl: '',
              ),
              quantity: 1,
            ),
            CartItemModel(
              id: 'sample-2',
              menuItem: MenuItemModel(
                id: 'm-2',
                name: 'Paket Nasi Padang Komplit',
                category: 'Paket Spesial',
                description: '',
                price: 38000,
                rating: 4.8,
                reviewCount: 95,
                imageUrl: '',
              ),
              quantity: 1,
            ),
            CartItemModel(
              id: 'sample-3',
              menuItem: MenuItemModel(
                id: 'm-3',
                name: 'Es Teh Talua Tradisional',
                category: 'Minuman Segar',
                description: '',
                price: 12000,
                rating: 4.7,
                reviewCount: 60,
                imageUrl: '',
              ),
              quantity: 1,
            ),
          ],
          deliveryMethod: 'delivery',
          deliveryAddress: 'Jl. Kemang Raya No. 45, Jakarta Selatan',
          paymentMethod: 'QRIS BCA Instan',
          subtotal: 78000,
          deliveryFee: 0,
          serviceFee: 0,
          discount: 0,
          totalPrice: 78000,
          status: OrderStatus.delivered,
        );

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        bool isDownloading = false;

        return StatefulBuilder(
          builder: (modalCtx, setModalState) {
            return Container(
              margin: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFFDF9),
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: const Color(0xFFEFE8DD)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withAlpha(20),
                    blurRadius: 20,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 40,
                    height: 4,
                    decoration: BoxDecoration(
                      color: const Color(0xFFD4C8B8),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'RM RASO MANDEH',
                    style: GoogleFonts.playfairDisplay(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF301115),
                      letterSpacing: 1.2,
                    ),
                  ),
                  Text(
                    'CABANG KEMANG - JAKARTA SELATAN',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 10.5,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF7C6C64),
                      letterSpacing: 0.8,
                    ),
                  ),
                  const SizedBox(height: 12),
                  const Divider(color: Color(0xFFEFE8DD), thickness: 1),
                  const SizedBox(height: 8),

                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('No. Transaksi', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF7C6C64))),
                      Text(targetOrder.id, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold, color: const Color(0xFF301115))),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Waktu Pembayaran', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF7C6C64))),
                      Text('${DateFormat('dd MMM yyyy, HH.mm').format(targetOrder.createdAt)} WIB', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF301115))),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Metode Pembayaran', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF7C6C64))),
                      Text(targetOrder.paymentMethod, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w700, color: const Color(0xFF1B8A5A))),
                    ],
                  ),
                  if (targetOrder.deliveryAddress.isNotEmpty) ...[
                    const SizedBox(height: 4),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Tujuan Antar', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF7C6C64))),
                        Flexible(
                          child: Text(
                            targetOrder.deliveryAddress,
                            style: GoogleFonts.plusJakartaSans(fontSize: 10.5, color: const Color(0xFF301115)),
                            overflow: TextOverflow.ellipsis,
                            textAlign: TextAlign.right,
                            maxLines: 1,
                          ),
                        ),
                      ],
                    ),
                  ],
                  const SizedBox(height: 12),
                  const Divider(color: Color(0xFFEFE8DD), thickness: 1),
                  const SizedBox(height: 8),

                  // Rincian Item di Struk Aktual
                  ...targetOrder.items.map((item) => _receiptItem(
                        '${item.quantity}x ${item.menuItem.name}',
                        CurrencyFormatter.format(item.menuItem.price * item.quantity),
                      )),

                  const SizedBox(height: 8),
                  const Divider(color: Color(0xFFEFE8DD), thickness: 1),
                  const SizedBox(height: 8),

                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('TOTAL BAYAR', style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.w800, color: const Color(0xFF301115))),
                      Text(CurrencyFormatter.format(targetOrder.totalPrice), style: GoogleFonts.playfairDisplay(fontSize: 16, fontWeight: FontWeight.w800, color: const Color(0xFF4A141A))),
                    ],
                  ),
                  const SizedBox(height: 14),

                  // Cap Stempel Status
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                    decoration: BoxDecoration(
                      border: Border.all(
                        color: targetOrder.status == OrderStatus.cancelled ? const Color(0xFFD32F2F) : const Color(0xFF1B8A5A),
                        width: 1.5,
                      ),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      targetOrder.status == OrderStatus.cancelled ? '★ PESANAN DIBATALKAN ★' : '★ LUNAS / TELAH TERVERIFIKASI ★',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: targetOrder.status == OrderStatus.cancelled ? const Color(0xFFD32F2F) : const Color(0xFF1B8A5A),
                        letterSpacing: 0.8,
                      ),
                    ),
                  ),
                  const SizedBox(height: 18),

                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF4A141A),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      onPressed: isDownloading
                          ? null
                          : () async {
                              setModalState(() {
                                isDownloading = true;
                              });

                              final result = await ReceiptService.downloadReceipt(targetOrder);

                              if (context.mounted) {
                                Navigator.pop(modalCtx);

                                if (result.success) {
                                  // Tampilkan Dialog Sukses Berisi Opsi Buka & Bagikan
                                  showDialog(
                                    context: context,
                                    builder: (dialogCtx) => AlertDialog(
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                                      title: Row(
                                        children: [
                                          const Icon(Icons.check_circle, color: Color(0xFF1B8A5A), size: 26),
                                          const SizedBox(width: 8),
                                          Text(
                                            'Struk Berhasil Diunduh!',
                                            style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.bold, color: const Color(0xFF301115)),
                                          ),
                                        ],
                                      ),
                                      content: Column(
                                        mainAxisSize: MainAxisSize.min,
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'Berkas PDF struk digital resmi telah disimpan di:',
                                            style: GoogleFonts.plusJakartaSans(fontSize: 12.5, color: const Color(0xFF55443D)),
                                          ),
                                          const SizedBox(height: 8),
                                          Container(
                                            width: double.infinity,
                                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                            decoration: BoxDecoration(
                                              color: const Color(0xFFF9F6F0),
                                              borderRadius: BorderRadius.circular(10),
                                              border: Border.all(color: const Color(0xFFEFE8DD)),
                                            ),
                                            child: Text(
                                              result.filePath,
                                              style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w700, color: const Color(0xFF4A141A)),
                                            ),
                                          ),
                                        ],
                                      ),
                                      actionsPadding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                                      actions: [
                                        TextButton(
                                          onPressed: () {
                                            Navigator.pop(dialogCtx);
                                            ReceiptService.shareReceipt(result.fileName, result.bytes);
                                          },
                                          child: Text(
                                            'Bagikan',
                                            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: const Color(0xFF7C6C64)),
                                          ),
                                        ),
                                        ElevatedButton.icon(
                                          icon: const Icon(Icons.picture_as_pdf, size: 16, color: Colors.white),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: const Color(0xFF4A141A),
                                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                          ),
                                          onPressed: () {
                                            Navigator.pop(dialogCtx);
                                            ReceiptService.openReceipt(result.fileName, result.bytes);
                                          },
                                          label: Text(
                                            'Buka PDF',
                                            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.white),
                                          ),
                                        ),
                                      ],
                                    ),
                                  );
                                } else {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text('Gagal mengunduh struk: ${result.errorMessage ?? "Kesalahan tidak diketahui"}'),
                                      backgroundColor: Colors.red.shade800,
                                    ),
                                  );
                                }
                              }
                            },
                      child: isDownloading
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.2),
                            )
                          : Text(
                              'Unduh Struk Digital (PDF)',
                              style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.white),
                            ),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }



  Widget _receiptItem(String name, String price) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 3),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(name, style: GoogleFonts.plusJakartaSans(fontSize: 11.5, color: const Color(0xFF4A3E39))),
          Text(price, style: GoogleFonts.plusJakartaSans(fontSize: 11.5, fontWeight: FontWeight.w600, color: const Color(0xFF301115))),
        ],
      ),
    );
  }
}

/// Helper model untuk data sample mockup
class _SampleItem {
  final String name;
  final String desc;
  final int price;

  _SampleItem({required this.name, required this.desc, required this.price});
}

/// Custom Painter Peta Tracking Live Cantik Khas Padang
class _MiniTrackingMapPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    // 1. Background Peta Sand/Tan
    final bgPaint = Paint()..color = const Color(0xFFEFE8DC);
    canvas.drawRect(Rect.fromLTWH(0, 0, size.width, size.height), bgPaint);

    // 2. Jalur Jalanan Ivory Bersih
    final roadPaint = Paint()
      ..color = const Color(0xFFFAF7F2)
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    // Jalan horizontal berkelok utama
    roadPaint.strokeWidth = 22;
    final mainRoadPath = Path();
    mainRoadPath.moveTo(0, size.height * 0.65);
    mainRoadPath.cubicTo(
      size.width * 0.35,
      size.height * 0.85,
      size.width * 0.65,
      size.height * 0.40,
      size.width,
      size.height * 0.60,
    );
    canvas.drawPath(mainRoadPath, roadPaint);

    // Jalan arteri cabang 1
    roadPaint.strokeWidth = 14;
    final sideRoad1 = Path();
    sideRoad1.moveTo(size.width * 0.28, 0);
    sideRoad1.quadraticBezierTo(
      size.width * 0.32,
      size.height * 0.45,
      size.width * 0.50,
      size.height * 0.60,
    );
    canvas.drawPath(sideRoad1, roadPaint);

    // Jalan arteri cabang 2
    final sideRoad2 = Path();
    sideRoad2.moveTo(size.width * 0.70, size.height);
    sideRoad2.quadraticBezierTo(
      size.width * 0.75,
      size.height * 0.55,
      size.width * 0.90,
      0,
    );
    canvas.drawPath(sideRoad2, roadPaint);

    // 3. Garis Putus-Putus Rute Minang Express Maroon
    final routePaint = Paint()
      ..color = const Color(0xFF7A2028)
      ..strokeWidth = 2.5
      ..style = PaintingStyle.stroke;

    final dashPath = Path();
    dashPath.moveTo(size.width * 0.18, size.height * 0.60);
    dashPath.cubicTo(
      size.width * 0.35,
      size.height * 0.50,
      size.width * 0.55,
      size.height * 0.45,
      size.width * 0.85,
      size.height * 0.58,
    );

    // Gambar dashed path secara manual
    final pathMetrics = dashPath.computeMetrics().first;
    double distance = 0.0;
    final totalLength = pathMetrics.length;
    const dashWidth = 5.0;
    const dashSpace = 4.0;

    final dashedPath = Path();
    while (distance < totalLength) {
      final len = (distance + dashWidth < totalLength) ? dashWidth : (totalLength - distance);
      dashedPath.addPath(
        pathMetrics.extractPath(distance, distance + len),
        Offset.zero,
      );
      distance += dashWidth + dashSpace;
    }
    canvas.drawPath(dashedPath, routePaint);

    // 4. Pin Restoran: Raso Kemang (Kiri)
    final storePos = Offset(size.width * 0.18, size.height * 0.60);
    // Lingkaran pin maroon
    final storePinPaint = Paint()..color = const Color(0xFF4A141A);
    canvas.drawCircle(storePos, 14, storePinPaint);
    // Icon resto di tengah pin
    final storeIconPainter = TextPainter(
      text: const TextSpan(
        text: '🏬',
        style: TextStyle(fontSize: 12),
      ),
      textDirection: TextDirection.ltr,
    )..layout();
    storeIconPainter.paint(
      canvas,
      Offset(storePos.dx - 6.5, storePos.dy - 7.5),
    );

    // Label pill Raso Kemang di bawah pin
    final labelBgPaint = Paint()..color = Colors.white;
    final labelRect = RRect.fromRectAndRadius(
      Rect.fromCenter(
        center: Offset(storePos.dx, storePos.dy + 20),
        width: 68,
        height: 16,
      ),
      const Radius.circular(8),
    );
    canvas.drawRRect(labelRect, labelBgPaint);
    final storeLabelPainter = TextPainter(
      text: const TextSpan(
        text: 'Raso Kemang',
        style: TextStyle(
          color: Color(0xFF4A141A),
          fontSize: 8.5,
          fontWeight: FontWeight.w800,
        ),
      ),
      textDirection: TextDirection.ltr,
    )..layout();
    storeLabelPainter.paint(
      canvas,
      Offset(storePos.dx - (storeLabelPainter.width / 2), storePos.dy + 14.5),
    );

    // 5. Pin Kurir: Uda Raso (Tengah Jalur)
    final courierPos = Offset(size.width * 0.48, size.height * 0.52);
    // Lingkaran emas bersinar
    final courierPinPaint = Paint()..color = const Color(0xFFDF9C36);
    canvas.drawCircle(courierPos, 15, courierPinPaint);
    final courierBorderPaint = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;
    canvas.drawCircle(courierPos, 15, courierBorderPaint);

    // Icon motor
    final bikePainter = TextPainter(
      text: const TextSpan(
        text: '🛵',
        style: TextStyle(fontSize: 13),
      ),
      textDirection: TextDirection.ltr,
    )..layout();
    bikePainter.paint(
      canvas,
      Offset(courierPos.dx - 7, courierPos.dy - 8),
    );

    // Label pill Uda Raso di bawah motor
    final courierLabelBg = Paint()..color = const Color(0xFF4A141A);
    final courierRect = RRect.fromRectAndRadius(
      Rect.fromCenter(
        center: Offset(courierPos.dx, courierPos.dy + 20),
        width: 54,
        height: 16,
      ),
      const Radius.circular(8),
    );
    canvas.drawRRect(courierRect, courierLabelBg);
    final courierLabelPainter = TextPainter(
      text: const TextSpan(
        text: 'Uda Raso',
        style: TextStyle(
          color: Colors.white,
          fontSize: 8.5,
          fontWeight: FontWeight.w800,
        ),
      ),
      textDirection: TextDirection.ltr,
    )..layout();
    courierLabelPainter.paint(
      canvas,
      Offset(courierPos.dx - (courierLabelPainter.width / 2), courierPos.dy + 14.5),
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
