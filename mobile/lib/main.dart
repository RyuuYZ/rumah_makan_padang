import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'providers/auth_provider.dart';
import 'providers/cart_provider.dart';
import 'providers/menu_provider.dart';
import 'providers/order_provider.dart';
import 'routes/app_router.dart';
import 'services/auth_service.dart';
import 'services/storage_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Mendukung orientasi portrait & landscape secara responsif
  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
    DeviceOrientation.landscapeLeft,
    DeviceOrientation.landscapeRight,
  ]);

  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ),
  );

  // Inisialisasi local storage SharedPreferences
  final storageService = await StorageService.init();
  final authService = AuthService(storageService);

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(
          create: (_) => AuthProvider(authService),
        ),
        ChangeNotifierProvider(
          create: (_) => MenuProvider(),
        ),
        ChangeNotifierProvider(
          create: (_) => CartProvider(storageService),
        ),
        ChangeNotifierProvider(
          create: (_) => OrderProvider(storageService),
        ),
      ],
      child: const RasaMandehApp(),
    ),
  );
}

/// Root Widget Aplikasi Rasa Mandeh
class RasaMandehApp extends StatelessWidget {
  const RasaMandehApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(  
      title: 'Rasa Mandeh',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      routerConfig: appRouter,
    );
  }
}
