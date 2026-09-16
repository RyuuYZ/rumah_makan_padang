import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../screens/splash_screen.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/main_scaffold.dart';
import '../screens/home/home_screen.dart';
import '../screens/menu/menu_screen.dart';
import '../screens/cart/cart_screen.dart';
import '../screens/checkout/payment_screen.dart';
import '../screens/checkout/order_loading_screen.dart';
import '../screens/order/order_history_screen.dart';
import '../screens/order/order_detail_screen.dart';
import '../screens/profile/profile_screen.dart';

final GlobalKey<NavigatorState> _rootNavigatorKey =
    GlobalKey<NavigatorState>(debugLabel: 'root');
final GlobalKey<NavigatorState> _shellNavigatorKey =
    GlobalKey<NavigatorState>(debugLabel: 'shell');

/// Konfigurasi GoRouter dengan named routes & ShellRoute untuk bottom navigation
final GoRouter appRouter = GoRouter(
  navigatorKey: _rootNavigatorKey,
  initialLocation: '/splash',
  routes: [
    // 1. Splash Screen
    GoRoute(
      path: '/splash',
      name: 'splash',
      builder: (context, state) => const SplashScreen(),
    ),

    // 2. Auth Routes
    GoRoute(
      path: '/login',
      name: 'login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/register',
      name: 'register',
      builder: (context, state) => const RegisterScreen(),
    ),

    // 3. ShellRoute for Tab Navigation (Home, Menu, Cart, Profile)
    ShellRoute(
      navigatorKey: _shellNavigatorKey,
      builder: (context, state, child) {
        return MainScaffold(child: child);
      },
      routes: [
        GoRoute(
          path: '/',
          name: 'home',
          pageBuilder: (context, state) => const NoTransitionPage(
            child: HomeScreen(),
          ),
        ),
        GoRoute(
          path: '/menu',
          name: 'menu',
          pageBuilder: (context, state) => const NoTransitionPage(
            child: MenuScreen(),
          ),
        ),
        GoRoute(
          path: '/orders',
          name: 'orders',
          pageBuilder: (context, state) => const NoTransitionPage(
            child: OrderHistoryScreen(),
          ),
        ),
        GoRoute(
          path: '/orders/:id',
          name: 'order-detail',
          pageBuilder: (context, state) {
            final orderId = state.pathParameters['id'] ?? '';
            return NoTransitionPage(
              child: OrderDetailScreen(orderId: orderId),
            );
          },
        ),
        GoRoute(
          path: '/profile',
          name: 'profile',
          pageBuilder: (context, state) => const NoTransitionPage(
            child: ProfileScreen(),
          ),
        ),
      ],
    ),

    // 4. Cart Modal (Pushed full screen over root navigator with [X] close button)
    GoRoute(
      parentNavigatorKey: _rootNavigatorKey,
      path: '/cart',
      name: 'cart',
      builder: (context, state) => const CartScreen(),
    ),

    // 5. Checkout & Orders Routes
    GoRoute(
      parentNavigatorKey: _rootNavigatorKey,
      path: '/payment',
      name: 'payment',
      builder: (context, state) => const PaymentScreen(),
    ),
    GoRoute(
      parentNavigatorKey: _rootNavigatorKey,
      path: '/order-loading/:id',
      name: 'order-loading',
      builder: (context, state) {
        final orderId = state.pathParameters['id'] ?? '';
        return OrderLoadingScreen(orderId: orderId);
      },
    ),
    GoRoute(
      parentNavigatorKey: _rootNavigatorKey,
      path: '/order-history',
      name: 'order-history',
      builder: (context, state) => const OrderHistoryScreen(),
    ),
  ],
);
