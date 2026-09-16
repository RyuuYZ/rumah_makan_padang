import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';

/// Provider autentikasi dan status sesi pengguna
class AuthProvider extends ChangeNotifier {
  final AuthService _authService;

  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  AuthProvider(this._authService) {
    _initUser();
  }

  UserModel? get user => _user;
  bool get isAuthenticated => _user != null;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  void _initUser() {
    _user = _authService.getCurrentUser();
    notifyListeners();
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }

  /// Login reguler
  Future<bool> login(String emailOrPhone, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _user = await _authService.login(
        emailOrPhone: emailOrPhone,
        password: password,
      );
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Login Demo 1-tap
  Future<bool> loginAsDemo() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await Future.delayed(const Duration(milliseconds: 300));
      _user = UserModel.demo();
      await _authService.updateProfile(_user!);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = 'Gagal masuk akun demo';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Login resmi dengan Akun Google
  Future<bool> loginWithGoogle() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final user = await _authService.signInWithGoogle();
      if (user == null) {
        // Pengguna membatalkan dialog pemilihan akun Google
        _isLoading = false;
        notifyListeners();
        return false;
      }

      _user = user;
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = 'Gagal masuk dengan Google: ${e.toString().replaceAll("Exception: ", "")}';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Register pengguna baru
  Future<bool> register({
    required String name,
    String? emailOrPhone,
    String? phone,
    String? email,
    String? address,
    String password = 'password123',
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _user = await _authService.register(
        name: name,
        emailOrPhone: emailOrPhone,
        phone: phone,
        email: email,
        address: address,
        password: password,
      );
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  /// Perbarui alamat pengiriman user
  Future<void> updateDeliveryAddress(String newAddress) async {
    if (_user == null) return;
    _user = _user!.copyWith(address: newAddress);
    await _authService.updateProfile(_user!);
    notifyListeners();
  }

  /// Perbarui profil user
  Future<void> updateProfile({
    String? name,
    String? phone,
    String? email,
    String? address,
  }) async {
    if (_user == null) {
      _user = UserModel(
        id: 'usr_default',
        name: name ?? 'Siti Nurhaliza',
        email: email ?? 'siti.nurhaliza@email.com',
        phone: phone ?? '+62 812-3456-7890',
        address: address ?? 'Jl. Kemang Raya No. 14, Mampang Prapatan',
      );
    } else {
      _user = _user!.copyWith(
        name: name ?? _user!.name,
        phone: phone ?? _user!.phone,
        email: email ?? _user!.email,
        address: address ?? _user!.address,
      );
    }
    await _authService.updateProfile(_user!);
    notifyListeners();
  }

  /// Logout
  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();
    await _authService.logout();
    _user = null;
    _isLoading = false;
    notifyListeners();
  }
}
