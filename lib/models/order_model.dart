import 'cart_item_model.dart';

/// Status pesanan makanan
enum OrderStatus {
  confirmed,
  cooking,
  delivering,
  delivered,
  cancelled;

  String get label {
    switch (this) {
      case OrderStatus.confirmed:
        return 'Pesanan Dikonfirmasi';
      case OrderStatus.cooking:
        return 'Sedang Dimasak';
      case OrderStatus.delivering:
        return 'Sedang Diantar';
      case OrderStatus.delivered:
        return 'Pesanan Sampai';
      case OrderStatus.cancelled:
        return 'Dibatalkan';
    }
  }

  String get description {
    switch (this) {
      case OrderStatus.confirmed:
        return 'Pesanan telah diterima oleh dapur Rasa Mandeh';
      case OrderStatus.cooking:
        return 'Koki sedang meracik bumbu & memanaskan lauk Padang Anda';
      case OrderStatus.delivering:
        return 'Kurir Ranah Express sedang meluncur menuju alamat Anda';
      case OrderStatus.delivered:
        return 'Selamat menikmati sajian otentik Rasa Mandeh!';
      case OrderStatus.cancelled:
        return 'Pesanan telah dibatalkan';
    }
  }

  int get stepIndex {
    switch (this) {
      case OrderStatus.confirmed:
        return 0;
      case OrderStatus.cooking:
        return 1;
      case OrderStatus.delivering:
        return 2;
      case OrderStatus.delivered:
        return 3;
      case OrderStatus.cancelled:
        return -1;
    }
  }
}

/// Milestone pencatatan riwayat tahapan pesanan (Timeline Log)
class OrderTimelineMilestone {
  final OrderStatus status;
  final DateTime timestamp;
  final String title;
  final String description;

  const OrderTimelineMilestone({
    required this.status,
    required this.timestamp,
    required this.title,
    required this.description,
  });

  Map<String, dynamic> toJson() {
    return {
      'status': status.name,
      'timestamp': timestamp.toIso8601String(),
      'title': title,
      'description': description,
    };
  }

  factory OrderTimelineMilestone.fromJson(Map<String, dynamic> json) {
    return OrderTimelineMilestone(
      status: OrderStatus.values.firstWhere(
        (e) => e.name == json['status'],
        orElse: () => OrderStatus.confirmed,
      ),
      timestamp: DateTime.parse(json['timestamp'] as String),
      title: json['title'] as String? ?? '',
      description: json['description'] as String? ?? '',
    );
  }
}

/// Model pesanan makanan lengkap
class OrderModel {
  final String id;
  final DateTime createdAt;
  final List<CartItemModel> items;
  final String deliveryMethod; // 'delivery' atau 'pickup'
  final String deliveryAddress;
  final String paymentMethod;
  final String notes;
  final int subtotal;
  final int deliveryFee;
  final int serviceFee;
  final int discount;
  final int totalPrice;
  final OrderStatus status;
  final int estimatedMinutes;
  final String courierName;
  final String courierPhone;
  final List<OrderTimelineMilestone> timeline;
  final String? cancelReason;

  const OrderModel({
    required this.id,
    required this.createdAt,
    required this.items,
    required this.deliveryMethod,
    required this.deliveryAddress,
    required this.paymentMethod,
    this.notes = '',
    required this.subtotal,
    required this.deliveryFee,
    this.serviceFee = 2000,
    this.discount = 0,
    required this.totalPrice,
    this.status = OrderStatus.confirmed,
    this.estimatedMinutes = 30,
    this.courierName = 'Buyung Ramli (Kurir RM)',
    this.courierPhone = '0813-7452-1190',
    this.timeline = const [],
    this.cancelReason,
  });

  /// Estimasi waktu tiba berbasis waktu pemesanan + durasi estimasi
  DateTime get estimatedArrivalTime => createdAt.add(Duration(minutes: estimatedMinutes));

  /// Sisa menit berjalan menuju waktu tiba
  int get remainingMinutes {
    final diff = estimatedArrivalTime.difference(DateTime.now()).inMinutes;
    return diff > 0 ? diff : 0;
  }

  /// Format waktu kedatangan (contoh: "12.45 WIB")
  String get formattedEstimatedArrivalTime {
    final hour = estimatedArrivalTime.hour.toString().padLeft(2, '0');
    final minute = estimatedArrivalTime.minute.toString().padLeft(2, '0');
    return '$hour.$minute WIB';
  }

  /// Format sisa waktu kedatangan (contoh: "15 Menit lagi" atau "Segera tiba")
  String get formattedRemainingTime {
    final rem = remainingMinutes;
    if (rem <= 0) return 'Segera tiba';
    return '$rem Menit lagi';
  }

  /// Ambil milestone riwayat berdasarkan status tertentu
  OrderTimelineMilestone? getMilestone(OrderStatus targetStatus) {
    try {
      return timeline.firstWhere((m) => m.status == targetStatus);
    } catch (_) {
      return null;
    }
  }

  OrderModel copyWith({
    String? id,
    DateTime? createdAt,
    List<CartItemModel>? items,
    String? deliveryMethod,
    String? deliveryAddress,
    String? paymentMethod,
    String? notes,
    int? subtotal,
    int? deliveryFee,
    int? serviceFee,
    int? discount,
    int? totalPrice,
    OrderStatus? status,
    int? estimatedMinutes,
    String? courierName,
    String? courierPhone,
    List<OrderTimelineMilestone>? timeline,
    String? cancelReason,
  }) {
    return OrderModel(
      id: id ?? this.id,
      createdAt: createdAt ?? this.createdAt,
      items: items ?? this.items,
      deliveryMethod: deliveryMethod ?? this.deliveryMethod,
      deliveryAddress: deliveryAddress ?? this.deliveryAddress,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      notes: notes ?? this.notes,
      subtotal: subtotal ?? this.subtotal,
      deliveryFee: deliveryFee ?? this.deliveryFee,
      serviceFee: serviceFee ?? this.serviceFee,
      discount: discount ?? this.discount,
      totalPrice: totalPrice ?? this.totalPrice,
      status: status ?? this.status,
      estimatedMinutes: estimatedMinutes ?? this.estimatedMinutes,
      courierName: courierName ?? this.courierName,
      courierPhone: courierPhone ?? this.courierPhone,
      timeline: timeline ?? this.timeline,
      cancelReason: cancelReason ?? this.cancelReason,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'createdAt': createdAt.toIso8601String(),
      'items': items.map((e) => e.toJson()).toList(),
      'deliveryMethod': deliveryMethod,
      'deliveryAddress': deliveryAddress,
      'paymentMethod': paymentMethod,
      'notes': notes,
      'subtotal': subtotal,
      'deliveryFee': deliveryFee,
      'serviceFee': serviceFee,
      'discount': discount,
      'totalPrice': totalPrice,
      'status': status.name,
      'estimatedMinutes': estimatedMinutes,
      'courierName': courierName,
      'courierPhone': courierPhone,
      'timeline': timeline.map((e) => e.toJson()).toList(),
      'cancelReason': cancelReason,
    };
  }

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    final status = OrderStatus.values.firstWhere(
      (e) => e.name == json['status'],
      orElse: () => OrderStatus.confirmed,
    );
    final createdAt = DateTime.parse(json['createdAt'] as String);

    List<OrderTimelineMilestone> timelineList = [];
    if (json['timeline'] is List) {
      timelineList = (json['timeline'] as List<dynamic>)
          .map((e) => OrderTimelineMilestone.fromJson(e as Map<String, dynamic>))
          .toList();
    } else {
      // Fallback kompatibilitas jika data lama belum memiliki timeline
      timelineList = [
        OrderTimelineMilestone(
          status: OrderStatus.confirmed,
          timestamp: createdAt,
          title: 'Pesanan Diterima Dapur',
          description: 'Rincian pesanan terverifikasi & pembayaran tervalidasi',
        ),
      ];
    }

    return OrderModel(
      id: json['id'] as String,
      createdAt: createdAt,
      items: (json['items'] as List<dynamic>)
          .map((e) => CartItemModel.fromJson(e as Map<String, dynamic>))
          .toList(),
      deliveryMethod: json['deliveryMethod'] as String? ?? 'delivery',
      deliveryAddress: json['deliveryAddress'] as String? ?? '',
      paymentMethod: json['paymentMethod'] as String? ?? 'QRIS',
      notes: json['notes'] as String? ?? '',
      subtotal: (json['subtotal'] as num).toInt(),
      deliveryFee: (json['deliveryFee'] as num).toInt(),
      serviceFee: (json['serviceFee'] as num?)?.toInt() ?? 2000,
      discount: (json['discount'] as num?)?.toInt() ?? 0,
      totalPrice: (json['totalPrice'] as num).toInt(),
      status: status,
      estimatedMinutes: (json['estimatedMinutes'] as num?)?.toInt() ?? 30,
      courierName: json['courierName'] as String? ?? 'Buyung Ramli',
      courierPhone: json['courierPhone'] as String? ?? '0813-7452-1190',
      timeline: timelineList,
      cancelReason: json['cancelReason'] as String?,
    );
  }
}
