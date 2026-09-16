import 'dart:io';
import 'package:flutter/foundation.dart';

/// Konfigurasi integrasi API antara Flutter Mobile dan Backend Laravel Raso Mandeh
class ApiConfig {
  ApiConfig._();

  /// Port default server Laravel development
  static const int defaultPort = 8000;

  /// Mendapatkan Base URL yang adaptif berdasarkan platform eksekusi
  static String get baseUrl {
    if (kIsWeb) {
      return 'http://localhost:$defaultPort/api/v1';
    }

    if (Platform.isAndroid) {
      // 10.0.2.2 adalah alias loopback localhost untuk Android Emulator
      return 'http://10.0.2.2:$defaultPort/api/v1';
    }

    if (Platform.isIOS || Platform.isMacOS || Platform.isLinux || Platform.isWindows) {
      return 'http://localhost:$defaultPort/api/v1';
    }

    return 'http://10.0.2.2:$defaultPort/api/v1';
  }

  /// Timeout koneksi dalam detik
  static const Duration timeoutDuration = Duration(seconds: 8);

  // Endpoint API RESTful Laravel
  static const String endpointBranches = '/branches';
  static const String endpointBranchesWithMenu = '/branches-with-menu';
  static const String endpointMenuItems = '/menu-items';
  static const String endpointOrders = '/orders';
  static const String endpointDownloadApk = '/download/apk';
  static const String endpointAuthGoogle = '/auth/google';

  // Google OAuth 2.0 Credentials
  static const String googleAndroidClientId = '186615191343-nkm9tj3hun8rktd1ajjpmcfoao7rgkse.apps.googleusercontent.com';
  static const String googleWebClientId = '186615191343-cs2joeig8vjfonnnaojp73i2gjtqt2gr.apps.googleusercontent.com';
}
