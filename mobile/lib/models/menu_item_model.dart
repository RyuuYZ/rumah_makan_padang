/// Model menu masakan Padang
class MenuItemModel {
  final String id;
  final String name;
  final String category;
  final String description;
  final int price;
  final double rating;
  final int reviewCount;
  final String imageUrl;
  final int spiciness; // 0: tidak pedas, 1: sedang, 2: pedas, 3: sangat pedas
  final bool isPopular;
  final bool isAvailable;
  final String portionInfo;
  final String? badge;
  final String? salesCount;

  const MenuItemModel({
    required this.id,
    required this.name,
    required this.category,
    required this.description,
    required this.price,
    required this.rating,
    required this.reviewCount,
    required this.imageUrl,
    this.spiciness = 1,
    this.isPopular = false,
    this.isAvailable = true,
    this.portionInfo = '1 Porsi',
    this.badge,
    this.salesCount,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'category': category,
      'description': description,
      'price': price,
      'rating': rating,
      'reviewCount': reviewCount,
      'imageUrl': imageUrl,
      'spiciness': spiciness,
      'isPopular': isPopular,
      'isAvailable': isAvailable,
      'portionInfo': portionInfo,
      'badge': badge,
      'salesCount': salesCount,
    };
  }

  factory MenuItemModel.fromJson(Map<String, dynamic> json) {
    return MenuItemModel(
      id: json['id'] as String,
      name: json['name'] as String,
      category: json['category'] as String,
      description: json['description'] as String,
      price: (json['price'] as num).toInt(),
      rating: (json['rating'] as num).toDouble(),
      reviewCount: (json['reviewCount'] as num).toInt(),
      imageUrl: json['imageUrl'] as String,
      spiciness: (json['spiciness'] as num?)?.toInt() ?? 1,
      isPopular: json['isPopular'] as bool? ?? false,
      isAvailable: json['isAvailable'] as bool? ?? true,
      portionInfo: json['portionInfo'] as String? ?? '1 Porsi',
      badge: json['badge'] as String?,
      salesCount: json['salesCount'] as String?,
    );
  }
}
