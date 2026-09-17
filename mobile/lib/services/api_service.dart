import 'dart:convert';
import 'dart:io';
import '../config/api_config.dart';
import '../models/menu_item_model.dart';
import 'mock_data_service.dart';

/// Service untuk menghubungkan aplikasi Flutter dengan REST API Laravel
class ApiService {
  static final HttpClient _client = HttpClient()
    ..connectionTimeout = ApiConfig.timeoutDuration;

  /// Mengambil daftar cabang dari backend Laravel
  static Future<List<Map<String, dynamic>>> getBranches() async {
    try {
      final uri = Uri.parse('${ApiConfig.baseUrl}${ApiConfig.endpointBranches}');
      final request = await _client.getUrl(uri);
      request.headers.set('Accept', 'application/json');

      final response = await request.close().timeout(ApiConfig.timeoutDuration);
      if (response.statusCode == 200) {
        final bodyString = await response.transform(utf8.decoder).join();
        final json = jsonDecode(bodyString);
        if (json['success'] == true && json['data'] is List) {
          return List<Map<String, dynamic>>.from(json['data']);
        }
      }
    } catch (_) {
      // Fallback diam-diam ke data default jika backend Laravel tidak sedang menyala
    }

    return [
      {'id': 1, 'nama': 'Cabang Utama Jakarta', 'alamat': 'Jl. Sabang No. 18, Jakarta Pusat'},
      {'id': 2, 'nama': 'Cabang Bukittinggi', 'alamat': 'Jl. Jam Gadang No. 45, Bukittinggi'},
    ];
  }

  /// Mengambil daftar menu hidangan dari backend Laravel
  static Future<List<MenuItemModel>> getMenuItems({int? branchId, String? category}) async {
    try {
      final queryParams = <String, String>{};
      if (branchId != null) queryParams['branch_id'] = branchId.toString();
      if (category != null && category != 'all') queryParams['kategori'] = category;

      final uri = Uri.parse('${ApiConfig.baseUrl}${ApiConfig.endpointMenuItems}')
          .replace(queryParameters: queryParams.isNotEmpty ? queryParams : null);

      final request = await _client.getUrl(uri);
      request.headers.set('Accept', 'application/json');

      final response = await request.close().timeout(ApiConfig.timeoutDuration);
      if (response.statusCode == 200) {
        final bodyString = await response.transform(utf8.decoder).join();
        final json = jsonDecode(bodyString);

        if (json['success'] == true && json['data'] is List) {
          final list = json['data'] as List;
          return list.map((item) {
            return MenuItemModel(
              id: item['id'].toString(),
              name: item['nama'] ?? '',
              category: item['kategori'] ?? 'Daging',
              description: item['deskripsi'] ?? '',
              price: (item['harga'] as num?)?.toInt() ?? 25000,
              rating: 4.8,
              reviewCount: 120,
              imageUrl: item['foto'] ?? 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600',
              spiciness: 2,
              isPopular: true,
              isAvailable: item['is_available'] ?? true,
              portionInfo: '1 Porsi Lengkap',
            );
          }).toList();
        }
      }
    } catch (_) {
      // Fallback ke mock data bawaan saat offline
    }

    return MockDataService.allMenuItems;
  }

  /// Mengirimkan pesanan baru ke backend Laravel
  static Future<Map<String, dynamic>?> createOrder({
    required int branchId,
    required String customerName,
    required String customerPhone,
    required String orderType,
    String? tableNumber,
    required List<Map<String, dynamic>> items,
  }) async {
    try {
      final uri = Uri.parse('${ApiConfig.baseUrl}${ApiConfig.endpointOrders}');
      final request = await _client.postUrl(uri);
      request.headers.set('Content-Type', 'application/json');
      request.headers.set('Accept', 'application/json');

      final payload = jsonEncode({
        'branch_id': branchId,
        'customer_name': customerName,
        'customer_phone': customerPhone,
        'order_type': orderType,
        'table_number': tableNumber,
        'items': items,
      });

      request.write(payload);
      final response = await request.close().timeout(ApiConfig.timeoutDuration);
      if (response.statusCode == 200 || response.statusCode == 201) {
        final bodyString = await response.transform(utf8.decoder).join();
        return jsonDecode(bodyString) as Map<String, dynamic>;
      }
    } catch (_) {}

    return null;
  }

  /// Sinkronisasi akun Google Sign-In ke backend Laravel
  static Future<Map<String, dynamic>?> syncGoogleUser({
    required String email,
    required String name,
    String? avatarUrl,
    String? googleId,
    String? idToken,
  }) async {
    try {
      final uri = Uri.parse('${ApiConfig.baseUrl}${ApiConfig.endpointAuthGoogle}');
      final request = await _client.postUrl(uri);
      request.headers.set('Content-Type', 'application/json');
      request.headers.set('Accept', 'application/json');

      final payload = jsonEncode({
        'email': email,
        'name': name,
        'avatar_url': avatarUrl,
        'google_id': googleId,
        'id_token': idToken,
      });

      request.write(payload);
      final response = await request.close().timeout(ApiConfig.timeoutDuration);

      if (response.statusCode == 200 || response.statusCode == 201) {
        final bodyString = await response.transform(utf8.decoder).join();
        final json = jsonDecode(bodyString);
        if (json['success'] == true && json['data'] is Map) {
          return Map<String, dynamic>.from(json['data']);
        }
      }
    } catch (_) {
      // Fallback diam-diam
    }
    return null;
  }
}
