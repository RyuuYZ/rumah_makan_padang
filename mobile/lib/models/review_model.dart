/// Model Ulasan Pelanggan untuk Menu Makanan
class ReviewModel {
  final String id;
  final String menuItemId;
  final String customerName;
  final int rating;
  final String comment;
  final String createdAt;

  const ReviewModel({
    required this.id,
    required this.menuItemId,
    required this.customerName,
    required this.rating,
    required this.comment,
    required this.createdAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'menu_item_id': menuItemId,
      'nama_pelanggan': customerName,
      'rating': rating,
      'komentar': comment,
      'created_at': createdAt,
    };
  }

  factory ReviewModel.fromJson(Map<String, dynamic> json) {
    return ReviewModel(
      id: json['id']?.toString() ?? '',
      menuItemId: json['menu_item_id']?.toString() ?? '',
      customerName: json['nama_pelanggan']?.toString() ?? 'Pelanggan',
      rating: (json['rating'] as num?)?.toInt() ?? 5,
      comment: json['komentar']?.toString() ?? '',
      createdAt: json['created_at']?.toString() ?? 'Baru saja',
    );
  }
}
