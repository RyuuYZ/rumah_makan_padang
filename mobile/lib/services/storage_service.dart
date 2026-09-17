import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/cart_item_model.dart';
import '../models/order_model.dart';
import '../utils/constants.dart';

/// Layanan penyimpanan lokal menggunakan SharedPreferences
class StorageService {
  final SharedPreferences _prefs;

  StorageService(this._prefs);

  static Future<StorageService> init() async {
    final prefs = await SharedPreferences.getInstance();
    return StorageService(prefs);
  }

  // === USER SESSION ===
  Future<bool> saveUserSession(UserModel user) async {
    final jsonStr = jsonEncode(user.toJson());
    return await _prefs.setString(AppConstants.keyUserSession, jsonStr);
  }

  UserModel? getUserSession() {
    final jsonStr = _prefs.getString(AppConstants.keyUserSession);
    if (jsonStr == null || jsonStr.isEmpty) return null;
    try {
      final map = jsonDecode(jsonStr) as Map<String, dynamic>;
      return UserModel.fromJson(map);
    } catch (_) {
      return null;
    }
  }

  Future<bool> clearUserSession() async {
    return await _prefs.remove(AppConstants.keyUserSession);
  }

  bool isLoggedIn() {
    return _prefs.containsKey(AppConstants.keyUserSession);
  }

  // === CART PERSISTENCE ===
  Future<bool> saveCart(List<CartItemModel> items) async {
    final listMap = items.map((e) => e.toJson()).toList();
    final jsonStr = jsonEncode(listMap);
    return await _prefs.setString(AppConstants.keyCartItems, jsonStr);
  }

  List<CartItemModel> getCart() {
    final jsonStr = _prefs.getString(AppConstants.keyCartItems);
    if (jsonStr == null || jsonStr.isEmpty) return [];
    try {
      final list = jsonDecode(jsonStr) as List<dynamic>;
      return list
          .map((e) => CartItemModel.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (_) {
      return [];
    }
  }

  Future<bool> clearCart() async {
    return await _prefs.remove(AppConstants.keyCartItems);
  }

  // === ORDERS PERSISTENCE ===
  Future<bool> saveOrders(List<OrderModel> orders) async {
    final listMap = orders.map((e) => e.toJson()).toList();
    final jsonStr = jsonEncode(listMap);
    return await _prefs.setString(AppConstants.keyOrderHistory, jsonStr);
  }

  List<OrderModel> getOrders() {
    final jsonStr = _prefs.getString(AppConstants.keyOrderHistory);
    if (jsonStr == null || jsonStr.isEmpty) return [];
    try {
      final list = jsonDecode(jsonStr) as List<dynamic>;
      return list
          .map((e) => OrderModel.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (_) {
      return [];
    }
  }
}
