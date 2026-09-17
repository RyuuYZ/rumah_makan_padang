import 'package:google_sign_in/google_sign_in.dart';
import '../config/api_config.dart';
import '../models/user_model.dart';
import 'api_service.dart';
import 'storage_service.dart';

/// Service autentikasi pengguna
class AuthService {
  final StorageService _storageService;
  bool _isGoogleSignInInitialized = false;

  AuthService(this._storageService);

  Future<void> _initGoogleSignIn() async {
    if (_isGoogleSignInInitialized) return;
    try {
      await GoogleSignIn.instance.initialize(
        serverClientId: ApiConfig.googleWebClientId,
        clientId: ApiConfig.googleAndroidClientId,
      );
      _isGoogleSignInInitialized = true;
    } catch (_) {
      // Abaikan jika sudah terinisialisasi
    }
  }

  /// Cek apakah user sedang login
  bool get isLoggedIn => _storageService.isLoggedIn();

  /// Ambil user yang sedang tersimpan
  UserModel? getCurrentUser() => _storageService.getUserSession();

  /// Autentikasi akun Google resmi
  Future<UserModel?> signInWithGoogle() async {
    await _initGoogleSignIn();

    try {
      // Bersihkan cache sesi Google sebelumnya agar dialog pemilih akun selalu muncul
      try {
        await GoogleSignIn.instance.signOut();
      } catch (_) {}

      final GoogleSignInAccount account =
          await GoogleSignIn.instance.authenticate();

      final String googleName = account.displayName ?? 'Pengguna Google';
      final String googleEmail = account.email;
      final String? googlePhoto = account.photoUrl;
      final String googleId = account.id;
      final auth = account.authentication;
      final String? idToken = auth.idToken;

      // Sinkronisasi ke backend Laravel
      final backendUser = await ApiService.syncGoogleUser(
        email: googleEmail,
        name: googleName,
        avatarUrl: googlePhoto,
        googleId: googleId,
        idToken: idToken,
      );

      final user = UserModel(
        id: backendUser?['id']?.toString() ?? 'usr_$googleId',
        name: backendUser?['name'] ?? googleName,
        email: backendUser?['email'] ?? googleEmail,
        phone: backendUser?['phone'] ?? '0812-3456-7890',
        address: backendUser?['address'] ?? 'Kota Padang, Sumatera Barat',
        memberTier: backendUser?['memberTier'] ?? 'Minang Gold Member',
      );

      await _storageService.saveUserSession(user);
      return user;
    } on GoogleSignInException catch (e) {
      if (e.code == GoogleSignInExceptionCode.canceled) {
        // User membatalkan pemilihan akun Google
        return null;
      }
      rethrow;
    } catch (e) {
      if (e.toString().toLowerCase().contains('cancel') ||
          e.toString().toLowerCase().contains('batal')) {
        return null;
      }
      rethrow;
    }
  }

  /// Mock login dengan validasi
  Future<UserModel> login({
    required String emailOrPhone,
    required String password,
  }) async {
    // Simulasi network delay
    await Future.delayed(const Duration(milliseconds: 600));

    final trimmedInput = emailOrPhone.trim();
    if (trimmedInput.isEmpty) {
      throw Exception('Email atau Nomor HP wajib diisi');
    }
    if (password.length < 6) {
      throw Exception('Kata sandi minimal 6 karakter');
    }

    // Jika demo atau login umum
    final user = UserModel(
      id: 'usr_${DateTime.now().millisecondsSinceEpoch}',
      name: trimmedInput.contains('@')
          ? trimmedInput.split('@').first.toUpperCase()
          : 'Pelanggan Mandeh',
      email: trimmedInput.contains('@') ? trimmedInput : '$trimmedInput@rasamandeh.com',
      phone: trimmedInput.contains('@') ? '0812-3456-7890' : trimmedInput,
      address: 'Jl. Khatib Sulaiman No. 42, Padang Barat, Kota Padang',
      memberTier: 'Minang Gold Member',
    );

    await _storageService.saveUserSession(user);
    return user;
  }

  /// Mock register pengguna baru
  Future<UserModel> register({
    required String name,
    String? emailOrPhone,
    String? phone,
    String? email,
    String? address,
    String password = 'password123',
  }) async {
    await Future.delayed(const Duration(milliseconds: 500));

    if (name.trim().isEmpty) {
      throw Exception('Nama lengkap wajib diisi');
    }

    final finalEmail = email?.trim().isNotEmpty == true
        ? email!.trim()
        : (emailOrPhone?.contains('@') == true
            ? emailOrPhone!.trim()
            : 'pelanggan@rasamandeh.com');

    final finalPhone = phone?.trim().isNotEmpty == true
        ? phone!.trim()
        : (emailOrPhone != null && !emailOrPhone.contains('@')
            ? emailOrPhone.trim()
            : '0812-3456-7890');

    final user = UserModel(
      id: 'usr_${DateTime.now().millisecondsSinceEpoch}',
      name: name.trim(),
      email: finalEmail,
      phone: finalPhone.startsWith('+62') || finalPhone.startsWith('0')
          ? finalPhone
          : '+62 $finalPhone',
      address: address?.trim().isNotEmpty == true
          ? address!.trim()
          : 'Jl. Pemuda No. 12, Padang Barat, Kota Padang',
      memberTier: 'Minang Silver Member',
    );

    await _storageService.saveUserSession(user);
    return user;
  }

  /// Update profil user
  Future<UserModel> updateProfile(UserModel updatedUser) async {
    await _storageService.saveUserSession(updatedUser);
    return updatedUser;
  }

  /// Logout
  Future<void> logout() async {
    try {
      await GoogleSignIn.instance.signOut();
    } catch (_) {}
    await _storageService.clearUserSession();
  }
}
