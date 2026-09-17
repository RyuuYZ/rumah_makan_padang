/// Konstanta aplikasi Rasa Mandeh
class AppConstants {
  AppConstants._();

  static const String appName = 'Rasa Mandeh';
  static const String appTagline = 'Cita Rasa Otentik Ranah Minang';

  // Storage Keys
  static const String keyUserSession = 'rm_user_session';
  static const String keyCartItems = 'rm_cart_items';
  static const String keyOrderHistory = 'rm_order_history';

  // Default Order Constants
  static const int defaultDeliveryFee = 10000; // Rp 10.000
  static const int defaultServiceFee = 2000;   // Rp 2.000
  static const int freeDeliveryThreshold = 75000; // Gratis ongkir jika > Rp 75.000

  // Promo Vouchers
  static const String voucherCode = 'MANDEMURAH';
  static const int voucherDiscount = 15000; // Rp 15.000

  // Categories
  static const List<String> menuCategories = [
    'Semua',
    'Lauk Utama',
    'Ayam & Bebek',
    'Ikan & Seafood',
    'Gulai & Kuah',
    'Sayur & Pelengkap',
    'Sambal Khas',
    'Minuman Segar',
    'Paket Nasi',
  ];
}
