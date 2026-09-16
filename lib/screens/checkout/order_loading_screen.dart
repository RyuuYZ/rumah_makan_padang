import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';

/// Halaman Loading Status Pesanan persis mockup Figma "load page":
/// - Background krem gading bersih (#FAF7F2)
/// - Ilustrasi kurir motor merah dengan kotak delivery emas berlogo monogram RM
/// - Animasi gerak halus (bobbing motion) simulasi motor melaju
/// - Pill badge: "SEDANG MEMPROSES ORDER"
/// - Progress bar ramping gradasi marun-emas yang terisi otomatis
/// - Navigasi otomatis ke halaman detail pesanan setelah progress selesai
class OrderLoadingScreen extends StatefulWidget {
  final String orderId;

  const OrderLoadingScreen({super.key, required this.orderId});

  @override
  State<OrderLoadingScreen> createState() => _OrderLoadingScreenState();
}

class _OrderLoadingScreenState extends State<OrderLoadingScreen>
    with TickerProviderStateMixin {
  late AnimationController _progressController;
  late Animation<double> _progressAnimation;

  late AnimationController _bobbingController;
  late Animation<double> _bobbingAnimation;

  @override
  void initState() {
    super.initState();

    // 1. Animasi progress bar (berjalan ~3 detik)
    _progressController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 3200),
    );

    _progressAnimation = CurvedAnimation(
      parent: _progressController,
      curve: Curves.easeInOutCubic,
    );

    _progressController.addStatusListener((status) {
      if (status == AnimationStatus.completed && mounted) {
        // Pindah ke detail pesanan saat loading selesai
        if (widget.orderId.isNotEmpty) {
          context.go('/orders/${widget.orderId}');
        } else {
          context.go('/orders');
        }
      }
    });

    _progressController.forward();

    // 2. Animasi bobbing (getaran lembut motor saat melaju)
    _bobbingController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    )..repeat(reverse: true);

    _bobbingAnimation = Tween<double>(begin: -1.5, end: 1.5).animate(
      CurvedAnimation(parent: _bobbingController, curve: Curves.easeInOutSine),
    );
  }

  @override
  void dispose() {
    _progressController.dispose();
    _bobbingController.dispose();
    super.dispose();
  }

  void _skipLoading() {
    if (_progressController.isAnimating) {
      _progressController.stop();
    }
    if (widget.orderId.isNotEmpty) {
      context.go('/orders/${widget.orderId}');
    } else {
      context.go('/orders');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: GestureDetector(
          onTap: _skipLoading,
          behavior: HitTestBehavior.opaque,
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // 1. ILUSTRASI KURIR MOTOR DENGAN ANIMASI BOBBING
                AnimatedBuilder(
                  animation: _bobbingAnimation,
                  builder: (context, child) {
                    return Transform.translate(
                      offset: Offset(0, _bobbingAnimation.value),
                      child: child,
                    );
                  },
                  child: SizedBox(
                    width: 270,
                    height: 250,
                    child: Image.asset(
                      'assets/images/courier.png',
                      fit: BoxFit.contain,
                    ),
                  ),
                ),

                const SizedBox(height: 28),

                // 2. STATUS PILL BADGE (SEDANG MEMPROSES ORDER)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 7),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF0E9DF),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: const Color(0xFFE5DDD2),
                      width: 0.8,
                    ),
                  ),
                  child: Text(
                    'SEDANG MEMPROSES ORDER',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.2,
                      color: const Color(0xFF8A7966),
                    ),
                  ),
                ),

                const SizedBox(height: 14),

                // 3. SLIM HORIZONTAL GRADIENT PROGRESS BAR
                SizedBox(
                  width: 190,
                  height: 3.5,
                  child: AnimatedBuilder(
                    animation: _progressAnimation,
                    builder: (context, child) {
                      return Stack(
                        children: [
                          // Background Track
                          Container(
                            width: 190,
                            height: 3.5,
                            decoration: BoxDecoration(
                              color: const Color(0xFFE8DFD5),
                              borderRadius: BorderRadius.circular(2),
                            ),
                          ),
                          // Active Progress Fill
                          FractionallySizedBox(
                            widthFactor: _progressAnimation.value.clamp(0.02, 1.0),
                            child: Container(
                              height: 3.5,
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [
                                    Color(0xFF4A141A),
                                    Color(0xFFBD141D),
                                    Color(0xFFDF9C36),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(2),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
