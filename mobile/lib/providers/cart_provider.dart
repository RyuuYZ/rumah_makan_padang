import 'package:flutter/material.dart';
import '../models/menu_item_model.dart';
import '../models/cart_item_model.dart';
import '../services/storage_service.dart';
import '../utils/constants.dart';

/// Provider pengelolaan keranjang belanja & kalkulasi biaya
class CartProvider extends ChangeNotifier {
  final StorageService _storageService;

  List<CartItemModel> _items = [];
  String _appliedVoucher = '';
  int _voucherDiscount = 0;
  String _deliveryMethod = 'delivery'; // 'delivery' atau 'pickup'
  String _orderNotes = '';

  CartProvider(this._storageService) {
    _loadCartFromStorage();
  }

  List<CartItemModel> get items => _items;
  String get appliedVoucher => _appliedVoucher;
  int get voucherDiscount => _voucherDiscount;
  String get deliveryMethod => _deliveryMethod;
  String get orderNotes => _orderNotes;

  /// Jumlah total porsi di keranjang untuk badge di bottom bar
  int get totalItemCount =>
      _items.fold(0, (total, item) => total + item.quantity);

  bool get isEmpty => _items.isEmpty;

  /// Subtotal harga makanan murni
  int get subtotal =>
      _items.fold(0, (total, item) => total + item.subtotal);

  /// Ongkos kirim (Gratis jika pickup atau subtotal >= Rp 75.000)
  int get deliveryFee {
    if (_deliveryMethod == 'pickup' || _items.isEmpty) return 0;
    if (subtotal >= AppConstants.freeDeliveryThreshold) return 0;
    return AppConstants.defaultDeliveryFee;
  }

  /// Biaya layanan aplikasi
  int get serviceFee => _items.isEmpty ? 0 : AppConstants.defaultServiceFee;

  /// Diskon yang diaplikasikan
  int get discount => _voucherDiscount;

  /// Grand Total akhir yang harus dibayar
  int get grandTotal {
    if (_items.isEmpty) return 0;
    final total = subtotal + deliveryFee + serviceFee - discount;
    return total < 0 ? 0 : total;
  }

  void _loadCartFromStorage() {
    _items = _storageService.getCart();
    notifyListeners();
  }

  Future<void> _persistCart() async {
    await _storageService.saveCart(_items);
  }

  /// Tambah menu ke keranjang
  Future<void> addItem(MenuItemModel menuItem, {int quantity = 1, String notes = ''}) async {
    final existingIndex = _items.indexWhere(
      (item) => item.menuItem.id == menuItem.id && item.notes == notes,
    );

    if (existingIndex >= 0) {
      final currentItem = _items[existingIndex];
      _items[existingIndex] = currentItem.copyWith(
        quantity: currentItem.quantity + quantity,
      );
    } else {
      _items.add(
        CartItemModel(
          id: 'cart_${DateTime.now().millisecondsSinceEpoch}_${menuItem.id}',
          menuItem: menuItem,
          quantity: quantity,
          notes: notes,
        ),
      );
    }

    notifyListeners();
    await _persistCart();
  }

  /// Kurangi kuantitas item
  Future<void> decrementItem(String cartItemId) async {
    final index = _items.indexWhere((item) => item.id == cartItemId);
    if (index < 0) return;

    if (_items[index].quantity > 1) {
      _items[index] = _items[index].copyWith(
        quantity: _items[index].quantity - 1,
      );
    } else {
      _items.removeAt(index);
    }

    notifyListeners();
    await _persistCart();
  }

  /// Tambah kuantitas item
  Future<void> incrementItem(String cartItemId) async {
    final index = _items.indexWhere((item) => item.id == cartItemId);
    if (index < 0) return;

    _items[index] = _items[index].copyWith(
      quantity: _items[index].quantity + 1,
    );

    notifyListeners();
    await _persistCart();
  }

  /// Hapus satu item dari keranjang
  Future<void> removeItem(String cartItemId) async {
    _items.removeWhere((item) => item.id == cartItemId);
    notifyListeners();
    await _persistCart();
  }

  /// Ganti metode pengiriman
  void setDeliveryMethod(String method) {
    _deliveryMethod = method;
    notifyListeners();
  }

  /// Set catatan pesanan
  void setOrderNotes(String notes) {
    _orderNotes = notes;
    notifyListeners();
  }

  /// Terapkan kode voucher promo
  bool applyVoucher(String code) {
    final cleanCode = code.trim().toUpperCase();
    if (cleanCode == AppConstants.voucherCode) {
      _appliedVoucher = cleanCode;
      _voucherDiscount = AppConstants.voucherDiscount;
      notifyListeners();
      return true;
    }
    return false;
  }

  /// Hapus voucher promo
  void removeVoucher() {
    _appliedVoucher = '';
    _voucherDiscount = 0;
    notifyListeners();
  }

  /// Bersihkan keranjang saat checkout sukses
  Future<void> clearCart() async {
    _items.clear();
    _appliedVoucher = '';
    _voucherDiscount = 0;
    _orderNotes = '';
    notifyListeners();
    await _storageService.clearCart();
  }
}
