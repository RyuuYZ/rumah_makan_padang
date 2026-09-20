import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart' hide TextDirection;
import '../models/order_model.dart';
import '../utils/currency_formatter.dart';

/// Hasil dari proses pengunduhan struk
class ReceiptResult {
  final bool success;
  final String fileName;
  final String filePath;
  final String? errorMessage;
  final Uint8List? bytes;

  const ReceiptResult({
    required this.success,
    required this.fileName,
    required this.filePath,
    this.errorMessage,
    this.bytes,
  });
}

/// Service untuk membuat dan mengunduh struk digital resmi dalam format PDF
class ReceiptService {
  static const MethodChannel _channel = MethodChannel('com.rasamandeh.rasa_mandeh/receipt');

  /// Hasilkan berkas PDF dan simpan langsung ke folder Download ponsel
  static Future<ReceiptResult> downloadReceipt(OrderModel order) async {
    try {
      final safeId = order.id.replaceAll(RegExp(r'[^a-zA-Z0-9_-]'), '_');
      final fileName = 'struk_$safeId.pdf';
      final pdfBytes = generateReceiptPdf(order);

      if (kIsWeb) {
        return ReceiptResult(
          success: true,
          fileName: fileName,
          filePath: 'Web Download',
          bytes: Uint8List.fromList(pdfBytes),
        );
      }

      // Simpan ke storage Android melalui MethodChannel
      final resultPath = await _channel.invokeMethod<String>('saveReceiptPdf', {
        'fileName': fileName,
        'bytes': Uint8List.fromList(pdfBytes),
      });

      return ReceiptResult(
        success: true,
        fileName: fileName,
        filePath: resultPath ?? 'Download/$fileName',
        bytes: Uint8List.fromList(pdfBytes),
      );
    } catch (e) {
      debugPrint('Error downloading receipt: $e');
      return ReceiptResult(
        success: false,
        fileName: 'struk_${order.id}.pdf',
        filePath: '',
        errorMessage: e.toString(),
      );
    }
  }

  /// Buka struk PDF di aplikasi penampil PDF perangkat
  static Future<bool> openReceipt(String fileName, Uint8List? bytes) async {
    try {
      final res = await _channel.invokeMethod<bool>('openReceiptPdf', {
        'fileName': fileName,
        'bytes': bytes,
      });
      return res ?? false;
    } catch (e) {
      debugPrint('Error opening receipt PDF: $e');
      return false;
    }
  }

  /// Bagikan struk PDF ke aplikasi lain (WhatsApp, Telegram, dsb.)
  static Future<bool> shareReceipt(String fileName, Uint8List? bytes) async {
    try {
      final res = await _channel.invokeMethod<bool>('shareReceiptPdf', {
        'fileName': fileName,
        'bytes': bytes,
      });
      return res ?? false;
    } catch (e) {
      debugPrint('Error sharing receipt PDF: $e');
      return false;
    }
  }

  /// Generator PDF 1.4 murni dalam Dart
  static List<int> generateReceiptPdf(OrderModel order) {
    const double pageWidth = 360.0;
    // Sesuaikan tinggi halaman secara dinamis mengikuti jumlah item
    final double pageHeight = 490.0 + (order.items.length * 22.0);

    final commands = <String>[];

    void drawText(String text, double x, double y, {
      double fontSize = 10,
      bool isBold = false,
      List<double>? color,
    }) {
      final c = color ?? [0.19, 0.07, 0.08]; // default dark
      commands.add('${c[0].toStringAsFixed(2)} ${c[1].toStringAsFixed(2)} ${c[2].toStringAsFixed(2)} rg');
      final font = isBold ? '/F1' : '/F2';
      final escaped = text
          .replaceAll('\\', '\\\\')
          .replaceAll('(', '\\(')
          .replaceAll(')', '\\)');
      commands.add('BT $font $fontSize Tf $x $y Td ($escaped) Tj ET');
    }

    void drawLine(double x1, double y1, double x2, double y2, {
      double strokeWidth = 0.8,
      List<double>? color,
    }) {
      final c = color ?? [0.85, 0.82, 0.78];
      commands.add('${c[0].toStringAsFixed(2)} ${c[1].toStringAsFixed(2)} ${c[2].toStringAsFixed(2)} RG');
      commands.add('$strokeWidth w $x1 $y1 m $x2 $y2 l S');
    }

    void drawRect(double x, double y, double w, double h, {
      List<double>? strokeColor,
      List<double>? fillColor,
      double strokeWidth = 1.0,
    }) {
      if (fillColor != null) {
        commands.add('${fillColor[0].toStringAsFixed(2)} ${fillColor[1].toStringAsFixed(2)} ${fillColor[2].toStringAsFixed(2)} rg');
        commands.add('$x $y $w $h re f');
      }
      if (strokeColor != null) {
        commands.add('${strokeColor[0].toStringAsFixed(2)} ${strokeColor[1].toStringAsFixed(2)} ${strokeColor[2].toStringAsFixed(2)} RG');
        commands.add('$strokeWidth w $x $y $w $h re S');
      }
    }

    // 1. Latar Belakang Hangat Khas Minang
    drawRect(0, 0, pageWidth, pageHeight, fillColor: [0.99, 0.98, 0.96]);

    // 2. Header Restoran
    double currentY = pageHeight - 35.0;
    drawText('RM RASO MANDEH', 105, currentY, fontSize: 16, isBold: true, color: [0.29, 0.08, 0.10]);
    currentY -= 16.0;
    drawText('CABANG KEMANG - JAKARTA SELATAN', 85, currentY, fontSize: 8.5, isBold: false, color: [0.49, 0.42, 0.39]);

    currentY -= 14.0;
    drawLine(24, currentY, 336, currentY);

    // 3. Metadata Transaksi
    currentY -= 18.0;
    drawText('No. Transaksi', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText(order.id, 220, currentY, fontSize: 9.5, isBold: true, color: [0.19, 0.07, 0.08]);

    currentY -= 15.0;
    final formattedDate = DateFormat('dd MMM yyyy, HH:mm').format(order.createdAt);
    drawText('Waktu Pembayaran', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText('$formattedDate WIB', 200, currentY, fontSize: 9.5, color: [0.19, 0.07, 0.08]);

    currentY -= 15.0;
    drawText('Metode Pembayaran', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText(order.paymentMethod, 200, currentY, fontSize: 9.5, isBold: true, color: [0.11, 0.54, 0.35]);

    if (order.deliveryAddress.isNotEmpty) {
      currentY -= 15.0;
      final displayAddr = order.deliveryAddress.length > 25
          ? '${order.deliveryAddress.substring(0, 22)}...'
          : order.deliveryAddress;
      drawText('Tujuan Pengantaran', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
      drawText(displayAddr, 180, currentY, fontSize: 9.0, color: [0.19, 0.07, 0.08]);
    }

    currentY -= 12.0;
    drawLine(24, currentY, 336, currentY);

    // 4. Rincian Item yang Dipesan
    currentY -= 18.0;
    drawText('ITEM MENU', 24, currentY, fontSize: 9, isBold: true, color: [0.49, 0.42, 0.39]);
    drawText('TOTAL', 280, currentY, fontSize: 9, isBold: true, color: [0.49, 0.42, 0.39]);

    currentY -= 10.0;
    drawLine(24, currentY, 336, currentY, strokeWidth: 0.5);

    for (final item in order.items) {
      currentY -= 16.0;
      final itemName = '${item.quantity}x ${item.menuItem.name}';
      final truncatedName = itemName.length > 26 ? '${itemName.substring(0, 24)}...' : itemName;
      final itemPrice = CurrencyFormatter.format(item.menuItem.price * item.quantity);

      drawText(truncatedName, 24, currentY, fontSize: 9.5, color: [0.19, 0.07, 0.08]);
      drawText(itemPrice, 270, currentY, fontSize: 9.5, isBold: true, color: [0.19, 0.07, 0.08]);
    }

    currentY -= 12.0;
    drawLine(24, currentY, 336, currentY);

    // 5. Ringkasan Biaya
    currentY -= 16.0;
    drawText('Subtotal Menu', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText(CurrencyFormatter.format(order.subtotal), 270, currentY, fontSize: 9.5, color: [0.19, 0.07, 0.08]);

    currentY -= 14.0;
    drawText('Biaya Pengantaran', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText(CurrencyFormatter.format(order.deliveryFee), 270, currentY, fontSize: 9.5, color: [0.19, 0.07, 0.08]);

    if (order.discount > 0) {
      currentY -= 14.0;
      drawText('Diskon Promo', 24, currentY, fontSize: 9.5, color: [0.11, 0.54, 0.35]);
      drawText('- ${CurrencyFormatter.format(order.discount)}', 260, currentY, fontSize: 9.5, isBold: true, color: [0.11, 0.54, 0.35]);
    }

    currentY -= 14.0;
    drawText('Biaya Layanan', 24, currentY, fontSize: 9.5, color: [0.49, 0.42, 0.39]);
    drawText(CurrencyFormatter.format(order.serviceFee), 270, currentY, fontSize: 9.5, color: [0.19, 0.07, 0.08]);

    currentY -= 10.0;
    drawLine(24, currentY, 336, currentY, strokeWidth: 1.0);

    // 6. TOTAL BAYAR
    currentY -= 18.0;
    drawText('TOTAL BAYAR', 24, currentY, fontSize: 11, isBold: true, color: [0.19, 0.07, 0.08]);
    drawText(CurrencyFormatter.format(order.totalPrice), 250, currentY, fontSize: 13, isBold: true, color: [0.29, 0.08, 0.10]);

    // 7. Cap Stempel Status
    currentY -= 36.0;
    final isCancelled = order.status == OrderStatus.cancelled;
    final badgeText = isCancelled ? '★ DIBATALKAN ★' : '★ LUNAS / TELAH TERVERIFIKASI ★';
    final badgeColor = isCancelled ? [0.85, 0.15, 0.15] : [0.11, 0.54, 0.35];

    drawRect(60, currentY - 6, 240, 26, strokeColor: badgeColor, strokeWidth: 1.5);
    drawText(badgeText, isCancelled ? 125 : 78, currentY, fontSize: 9.5, isBold: true, color: badgeColor);

    // 8. Catatan Kaki
    currentY -= 28.0;
    drawText('Terima kasih telah memesan di RM Raso Mandeh!', 75, currentY, fontSize: 8.5, color: [0.49, 0.42, 0.39]);
    currentY -= 12.0;
    drawText('Cita Rasa Minang Nan Sabana Lamak', 110, currentY, fontSize: 8, isBold: true, color: [0.29, 0.08, 0.10]);

    // Kompilasi dokumen PDF standar
    final objects = <String>[];
    objects.add('<< /Type /Catalog /Pages 2 0 R >>');
    objects.add('<< /Type /Pages /Kids [3 0 R] /Count 1 >>');
    objects.add('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $pageWidth $pageHeight] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>');
    objects.add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>');
    objects.add('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>');

    final streamContent = commands.join('\n');
    final streamBytes = latin1.encode(streamContent);
    objects.add('<< /Length ${streamBytes.length} >>\nstream\n$streamContent\nendstream');

    final buffer = BytesBuilder();
    buffer.add(latin1.encode('%PDF-1.4\n%\xe2\xe3\xcf\xd3\n'));

    final offsets = <int>[];
    for (var i = 0; i < objects.length; i++) {
      offsets.add(buffer.length);
      buffer.add(latin1.encode('${i + 1} 0 obj\n${objects[i]}\nendobj\n'));
    }

    final xrefOffset = buffer.length;
    buffer.add(latin1.encode('xref\n0 ${objects.length + 1}\n0000000000 65535 f \n'));
    for (final off in offsets) {
      final offStr = off.toString().padLeft(10, '0');
      buffer.add(latin1.encode('$offStr 00000 n \n'));
    }

    buffer.add(latin1.encode('trailer\n<< /Size ${objects.length + 1} /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF\n'));
    return buffer.toBytes();
  }
}
