import 'menu_item_model.dart';

/// Model item dalam keranjang belanja
class CartItemModel {
  final String id;
  final MenuItemModel menuItem;
  final int quantity;
  final String notes;

  const CartItemModel({
    required this.id,
    required this.menuItem,
    required this.quantity,
    this.notes = '',
  });

  int get subtotal => menuItem.price * quantity;

  CartItemModel copyWith({
    String? id,
    MenuItemModel? menuItem,
    int? quantity,
    String? notes,
  }) {
    return CartItemModel(
      id: id ?? this.id,
      menuItem: menuItem ?? this.menuItem,
      quantity: quantity ?? this.quantity,
      notes: notes ?? this.notes,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'menuItem': menuItem.toJson(),
      'quantity': quantity,
      'notes': notes,
    };
  }

  factory CartItemModel.fromJson(Map<String, dynamic> json) {
    return CartItemModel(
      id: json['id'] as String,
      menuItem: MenuItemModel.fromJson(json['menuItem'] as Map<String, dynamic>),
      quantity: (json['quantity'] as num).toInt(),
      notes: json['notes'] as String? ?? '',
    );
  }
}
