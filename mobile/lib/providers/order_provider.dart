import 'package:flutter/material.dart';
import '../models/order_model.dart';
import '../models/cart_item_model.dart';
import '../models/menu_item_model.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

/// Provider riwayat pesanan dan tracking status pengiriman
class OrderProvider extends ChangeNotifier {
  final StorageService _storageService;

  List<OrderModel> _orders = [];
  bool _isLoading = false;

  OrderProvider(this._storageService) {
    _loadOrders();
  }

  List<OrderModel> get orders => _orders;
  bool get isLoading => _isLoading;

  /// Pesanan yang sedang aktif diproses atau diantar
  List<OrderModel> get activeOrders => _orders
      .where((o) =>
          o.status != OrderStatus.delivered && o.status != OrderStatus.cancelled)
      .toList();

  /// Riwayat pesanan yang sudah selesai atau dibatalkan
  List<OrderModel> get pastOrders => _orders
      .where((o) =>
          o.status == OrderStatus.delivered || o.status == OrderStatus.cancelled)
      .toList();

  /// Pesanan aktif paling baru untuk pelacakan
  OrderModel? get activeOrder {
    final active = activeOrders;
    if (active.isNotEmpty) return active.first;
    if (_orders.isNotEmpty) return _orders.first;
    return null;
  }

  void _loadOrders() {
    _orders = _storageService.getOrders();
    // Jika belum ada pesanan sama sekali di storage, buat pesanan sample riwayat
    if (_orders.isEmpty) {
      _seedSampleOrders();
    }
    notifyListeners();
  }

  void _seedSampleOrders() {
    final now = DateTime.now();
    final createdAt = now.subtract(const Duration(minutes: 18));
    final cookingAt = now.subtract(const Duration(minutes: 12));

    final sampleOrder = OrderModel(
      id: '#RSO-88429',
      createdAt: createdAt,
      items: const [
        CartItemModel(
          id: 'item_1',
          menuItem: MenuItemModel(
            id: 'm1',
            name: 'Rendang Daging Sapi Karamel',
            category: 'Daging',
            description: 'Bumbu kelapa hitam, daging empuk',
            price: 28000,
            rating: 4.9,
            reviewCount: 300,
            imageUrl: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400',
          ),
          quantity: 1,
        ),
        CartItemModel(
          id: 'item_2',
          menuItem: MenuItemModel(
            id: 'm2',
            name: 'Paket Nasi Padang Komplit',
            category: 'Paket',
            description: 'Gulai Cincang, sayur kapau, kuah lado',
            price: 38000,
            rating: 5.0,
            reviewCount: 400,
            imageUrl: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400',
          ),
          quantity: 1,
        ),
        CartItemModel(
          id: 'item_3',
          menuItem: MenuItemModel(
            id: 'm3',
            name: 'Es Teh Talua Tradisional',
            category: 'Minuman',
            description: 'Kocokan telur bebek, jeruk nipis & teh pekat',
            price: 12000,
            rating: 4.8,
            reviewCount: 200,
            imageUrl: 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400',
          ),
          quantity: 1,
        ),
      ],
      deliveryMethod: 'delivery',
      deliveryAddress: 'Jl. Kemang Raya No. 14, Jakarta Selatan',
      paymentMethod: 'QRIS BCA (Lunas • 12.18 WIB)',
      notes: 'Patokan: Pagar hitam depan Apotek. Titip di meja pos satpam.',
      subtotal: 78000,
      deliveryFee: 0,
      serviceFee: 0,
      discount: 0,
      totalPrice: 78000,
      status: OrderStatus.cooking,
      estimatedMinutes: 20,
      timeline: [
        OrderTimelineMilestone(
          status: OrderStatus.confirmed,
          timestamp: createdAt,
          title: 'Pesanan Diterima Dapur',
          description: 'Rincian pesanan terverifikasi & pembayaran tervalidasi',
        ),
        OrderTimelineMilestone(
          status: OrderStatus.cooking,
          timestamp: cookingAt,
          title: 'Sedang Dimasak & Dibungkus',
          description: 'Lauk dipanaskan di kuali tanah liat & dibungkus daun pisang berlapis.',
        ),
      ],
    );
    _orders = [sampleOrder];
    _storageService.saveOrders(_orders);
  }

  Future<void> _persistOrders() async {
    await _storageService.saveOrders(_orders);
  }

  /// Buat order baru dari checkout
  Future<OrderModel> createOrder({
    required List<CartItemModel> items,
    required String deliveryMethod,
    required String deliveryAddress,
    required String paymentMethod,
    required String notes,
    required int subtotal,
    required int deliveryFee,
    required int serviceFee,
    required int discount,
    required int totalPrice,
    String? customerName,
    String? customerPhone,
    int branchId = 1,
  }) async {
    _isLoading = true;
    notifyListeners();

    String? remoteOrderId;
    String? remoteQrToken;

    try {
      final payloadItems = items.map((cartItem) {
        final menuIdInt = int.tryParse(cartItem.menuItem.id) ?? 1;
        return {
          'menu_item_id': menuIdInt,
          'quantity': cartItem.quantity,
          'notes': cartItem.notes,
        };
      }).toList();

      final apiResponse = await ApiService.createOrder(
        branchId: branchId,
        customerName: customerName ?? 'Pelanggan Mobile',
        customerPhone: customerPhone ?? '081234567890',
        orderType: deliveryMethod == 'takeaway' ? 'takeaway' : 'dine_in',
        items: payloadItems,
      );

      if (apiResponse != null && apiResponse['success'] == true && apiResponse['data'] != null) {
        final data = apiResponse['data'];
        remoteOrderId = data['order_number'] ?? data['order_id']?.toString();
        remoteQrToken = data['qr_code_token'];
      }
    } catch (_) {
      // Fallback diam-diam ke pemrosesan lokal jika backend offline
    }

    final now = DateTime.now();
    final orderId = remoteOrderId ??
        'RM-${now.year}${now.month.toString().padLeft(2, '0')}${now.day.toString().padLeft(2, '0')}-${now.minute}${now.second}';

    final initialTimeline = [
      OrderTimelineMilestone(
        status: OrderStatus.confirmed,
        timestamp: now,
        title: 'Pesanan Diterima Dapur',
        description: 'Rincian pesanan terverifikasi & pembayaran tervalidasi' +
            (remoteQrToken != null ? ' (Token: $remoteQrToken)' : ''),
      ),
    ];

    final newOrder = OrderModel(
      id: orderId,
      createdAt: now,
      items: List.from(items),
      deliveryMethod: deliveryMethod,
      deliveryAddress: deliveryAddress,
      paymentMethod: paymentMethod,
      notes: notes,
      subtotal: subtotal,
      deliveryFee: deliveryFee,
      serviceFee: serviceFee,
      discount: discount,
      totalPrice: totalPrice,
      status: OrderStatus.confirmed,
      estimatedMinutes: 30,
      timeline: initialTimeline,
    );

    _orders.insert(0, newOrder);
    _isLoading = false;
    notifyListeners();
    await _persistOrders();

    return newOrder;
  }

  /// Ambil order berdasarkan ID
  OrderModel? getOrderById(String orderId) {
    try {
      return _orders.firstWhere((o) => o.id == orderId);
    } catch (_) {
      return null;
    }
  }

  /// Perbarui status pesanan (simulasi progres order) beserta catatan milestone
  Future<void> updateOrderStatus(String orderId, OrderStatus newStatus, {String? customNote}) async {
    final index = _orders.indexWhere((o) => o.id == orderId);
    if (index < 0) return;

    final currentOrder = _orders[index];

    String milestoneTitle;
    String milestoneDesc;
    switch (newStatus) {
      case OrderStatus.confirmed:
        milestoneTitle = 'Pesanan Diterima Dapur';
        milestoneDesc = 'Rincian pesanan terverifikasi & pembayaran tervalidasi';
        break;
      case OrderStatus.cooking:
        milestoneTitle = 'Sedang Dimasak & Dibungkus';
        milestoneDesc = 'Lauk dipanaskan di kuali tanah liat & dibungkus daun pisang berlapis.';
        break;
      case OrderStatus.delivering:
        milestoneTitle = 'Kurir Menjemput & Mengantar';
        milestoneDesc = 'Kurir ${currentOrder.courierName} sedang meluncur menuju alamat tujuan.';
        break;
      case OrderStatus.delivered:
        milestoneTitle = 'Pesanan Tiba & Siap Disajikan';
        milestoneDesc = 'Pesanan telah sampai. Selamat menikmati sajian otentik Rasa Mandeh!';
        break;
      case OrderStatus.cancelled:
        milestoneTitle = 'Pesanan Dibatalkan';
        milestoneDesc = customNote ?? 'Pesanan dibatalkan oleh pengguna.';
        break;
    }

    final newMilestone = OrderTimelineMilestone(
      status: newStatus,
      timestamp: DateTime.now(),
      title: milestoneTitle,
      description: milestoneDesc,
    );

    final updatedTimeline = List<OrderTimelineMilestone>.from(currentOrder.timeline)..add(newMilestone);

    final updated = currentOrder.copyWith(
      status: newStatus,
      timeline: updatedTimeline,
    );

    _orders[index] = updated;
    notifyListeners();
    await _persistOrders();
  }

  /// Batalkan pesanan dengan alasan pembatalan
  Future<void> cancelOrder(String orderId, {String? reason}) async {
    final index = _orders.indexWhere((o) => o.id == orderId);
    if (index < 0) return;

    final currentOrder = _orders[index];
    final finalReason = (reason != null && reason.trim().isNotEmpty)
        ? reason.trim()
        : 'Dibatalkan oleh pelanggan';

    final cancelMilestone = OrderTimelineMilestone(
      status: OrderStatus.cancelled,
      timestamp: DateTime.now(),
      title: 'Pesanan Dibatalkan',
      description: 'Alasan: $finalReason',
    );

    final updatedTimeline = List<OrderTimelineMilestone>.from(currentOrder.timeline)..add(cancelMilestone);

    final updated = currentOrder.copyWith(
      status: OrderStatus.cancelled,
      cancelReason: finalReason,
      timeline: updatedTimeline,
    );

    _orders[index] = updated;
    notifyListeners();
    await _persistOrders();
  }
}
