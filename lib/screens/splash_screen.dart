import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/rm_logo.dart';

/// Halaman Splash Screen Rasa Mandeh persis sesuai mockup Figma
class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;
  late Animation<double> _progressAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2400),
    );

    _fadeAnimation = CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.0, 0.45, curve: Curves.easeIn),
    );

    _progressAnimation = CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.2, 1.0, curve: Curves.easeInOutCubic),
    );

    _controller.addStatusListener((status) {
      if (status == AnimationStatus.completed) {
        if (!mounted) return;
        final authProvider = context.read<AuthProvider>();
        if (authProvider.isAuthenticated) {
          context.go('/');
        } else {
          context.go('/login');
        }
      }
    });

    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Warna background krem gading persis sesuai mockup
    const ivoryBackground = Color(0xFFFAF7F2);

    return Scaffold(
      backgroundColor: ivoryBackground,
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 36),
            child: FadeTransition(
              opacity: _fadeAnimation,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  // Logo RM + Brand Raso Mandeh + Divider + Tagline Filosofis
                  const RmLogo(
                    size: 88,
                    showText: true,
                    showTagline: true,
                  ),
                  const SizedBox(height: 38),

                  // Progress Bar Ramping Khas Mockup
                  SizedBox(
                    width: 190,
                    height: 3.5,
                    child: Stack(
                      children: [
                        // Track Background
                        Container(
                          decoration: BoxDecoration(
                            color: const Color(0xFFE8E2D8),
                            borderRadius: BorderRadius.circular(2),
                          ),
                        ),

                        // Active Animated Progress Line (Gradient Marun ke Emas)
                        AnimatedBuilder(
                          animation: _progressAnimation,
                          builder: (context, child) {
                            return FractionallySizedBox(
                              alignment: Alignment.centerLeft,
                              widthFactor: _progressAnimation.value,
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(2),
                                  gradient: const LinearGradient(
                                    colors: [
                                      Color(0xFF8B1524),
                                      Color(0xFFB51D29),
                                      Color(0xFFDF9C36),
                                    ],
                                    begin: Alignment.centerLeft,
                                    end: Alignment.centerRight,
                                  ),
                                ),
                              ),
                            );
                          },
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
