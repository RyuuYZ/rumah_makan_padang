import 'package:intl/intl.dart';

/// Formatter mata uang Rupiah Indonesia
class CurrencyFormatter {
  CurrencyFormatter._();

  static final NumberFormat _formatter = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  /// Format angka integer/double menjadi 'Rp 28.000'
  static String format(num amount) {
    return _formatter.format(amount).replaceAll(',00', '');
  }
}
