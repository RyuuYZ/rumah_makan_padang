import '../models/menu_item_model.dart';

/// Data mock otentik masakan Padang "Rasa Mandeh"
class MockDataService {
  MockDataService._();

  static const List<Map<String, String>> promoBanners = [
    {
      'title': 'Diskon 30% Paket Baralek',
      'subtitle': 'Nikmati sajian prasmanan Minang beramai-ramai',
      'tag': 'PROMO SPESIAL',
      'code': 'MANDEMURAH',
      'imageUrl': 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800',
    },
    {
      'title': 'Rendang Sapi Warisan Nenek',
      'subtitle': 'Dimasak 8 jam dengan kelapa parut sangrai pilihan',
      'tag': 'BEST SELLER',
      'code': '',
      'imageUrl': 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
    },
    {
      'title': 'Gratis Ongkos Kirim',
      'subtitle': 'Khusus area Padang & sekitarnya min. belanja 75rb',
      'tag': 'HEMAT ONGKIR',
      'code': '',
      'imageUrl': 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800',
    },
  ];

  static const List<MenuItemModel> allMenuItems = [
    // 1. Rendang Daging (Match Figma)
    MenuItemModel(
      id: 'm1',
      name: 'Rendang Daging',
      category: 'Daging',
      description:
          'Daging sapi pilihan yang dimasak perlahan hingga empuk dan meresap bumbu hingga berwarna cokelat pekat.',
      price: 22000,
      rating: 4.9,
      reviewCount: 1240,
      imageUrl: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600',
      spiciness: 2,
      isPopular: true,
      badge: 'TERLARIS',
      salesCount: '1.2k terjual',
      portionInfo: '1 Porsi Daging Empuk',
    ),
    // 2. Gulai Daging (Match Figma)
    MenuItemModel(
      id: 'm2',
      name: 'Gulai Daging',
      category: 'Daging',
      description:
          'Potongan daging sapi empuk dalam kuah santan gulai kental gurih berempah kaya kunyit, jahe, dan serai wangi.',
      price: 25000,
      rating: 4.8,
      reviewCount: 890,
      imageUrl: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600',
      spiciness: 2,
      isPopular: true,
      badge: 'REKOMENDASI',
      salesCount: '890 terjual',
      portionInfo: '1 Porsi Gulai Daging',
    ),
    // 3. Nasi + Ayam Pop (Match Figma)
    MenuItemModel(
      id: 'm3',
      name: 'Nasi + Ayam Pop',
      category: 'Paket',
      description:
          'Nasi hangat pulen berpadu ayam pop lembut gurih khas Bukittinggi, disiram kuah gulai dan sambal tomat khas.',
      price: 25000,
      rating: 4.9,
      reviewCount: 1120,
      imageUrl: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600',
      spiciness: 1,
      isPopular: true,
      badge: 'FAVORIT',
      salesCount: '1.1k terjual',
      portionInfo: '1 Paket Nasi + Ayam Pop',
    ),
    // 4. Gulai Kepala Ikan (Match Figma)
    MenuItemModel(
      id: 'm4',
      name: 'Gulai Kepala Ikan',
      category: 'Ikan',
      description:
          'Kepala kakap segar laut Padang berkuah santan asam pedas asam kandis nan wangi semerbak dengan daun ruku-ruku.',
      price: 38000,
      rating: 4.8,
      reviewCount: 780,
      imageUrl: 'https://images.unsplash.com/photo-1534939561126-855b8675edd7?w=600',
      spiciness: 2,
      isPopular: true,
      badge: 'SPESIAL',
      salesCount: '650 terjual',
      portionInfo: '1 Kepala Ikan Utuh',
    ),
    // 5. Dendeng Batokok
    MenuItemModel(
      id: 'm5',
      name: 'Dendeng Batokok',
      category: 'Daging',
      description:
          'Daging sapi tipis empuk dipukul lembut lalu dipanggang arang, disiram lado mudo minyak kelapa asli.',
      price: 26000,
      rating: 4.8,
      reviewCount: 650,
      imageUrl: 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=600',
      spiciness: 3,
      isPopular: true,
      badge: 'POPULER',
      salesCount: '540 terjual',
      portionInfo: '2 Lembar Dendeng Panggang',
    ),
    // 6. Ayam Bakar Padang
    MenuItemModel(
      id: 'm6',
      name: 'Ayam Bakar Padang',
      category: 'Ayam',
      description:
          'Ayam ungkep bumbu santan kuning pekat dibakar aroma arang dengan daun jeruk yang semerbak harum.',
      price: 24000,
      rating: 4.8,
      reviewCount: 450,
      imageUrl: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600',
      spiciness: 2,
      badge: 'FAVORIT',
      salesCount: '410 terjual',
      portionInfo: '1 Potong Ayam Bakar',
    ),
    // 6b. Ayam Goreng Padang (Match Figma Cart)
    MenuItemModel(
      id: 'm6b',
      name: 'Ayam Goreng Padang',
      category: 'Ayam',
      description:
          'Ayam goreng rempah bumbu lengkuas sangrai renyah gurih khas Minang dengan taburan serundeng lezat.',
      price: 33000,
      rating: 4.9,
      reviewCount: 520,
      imageUrl: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600',
      spiciness: 1,
      isPopular: true,
      badge: 'FAVORIT',
      salesCount: '620 terjual',
      portionInfo: '1 Porsi Ayam Goreng',
    ),
    // 7. Paket Nasi Kapau
    MenuItemModel(
      id: 'm7',
      name: 'Paket Nasi Kapau',
      category: 'Paket',
      description:
          'Nasi putih hangat, Rendang Daging, Gulai Tunjang, Sayur Singkong, Telur Dadar tebal, dan Sambal Lado Ijo.',
      price: 35000,
      rating: 5.0,
      reviewCount: 1450,
      imageUrl: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600',
      spiciness: 2,
      isPopular: true,
      badge: 'KOMPLIT',
      salesCount: '1.5k terjual',
      portionInfo: '1 Porsi Komplit',
    ),
    // 8. Telur Dadar Padang
    MenuItemModel(
      id: 'm8',
      name: 'Telur Dadar Padang',
      category: 'Sayur',
      description:
          'Telur bebek dadar tebal khas Padang dengan irisan daun bawang, seledri, dan kelapa sangrai berbumbu gurih.',
      price: 14000,
      rating: 4.9,
      reviewCount: 920,
      imageUrl: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=600',
      spiciness: 1,
      isPopular: true,
      badge: 'TERLARIS',
      salesCount: '920 terjual',
      portionInfo: '1 Potong Telur Tebal',
    ),
    // 9. Sayur Daun Singkong & Nangka
    MenuItemModel(
      id: 'm9',
      name: 'Sayur Daun Singkong',
      category: 'Sayur',
      description:
          'Rebusan daun singkong hijau segar berpadu gulai nangka muda Kapau dan kuah santan kuning nikmat.',
      price: 10000,
      rating: 4.7,
      reviewCount: 380,
      imageUrl: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600',
      spiciness: 1,
      badge: 'PELENGKAP',
      salesCount: '520 terjual',
      portionInfo: '1 Mangkok Sayur Singkong',
    ),
    // 10. Sambal Ijo Lado Mudo
    MenuItemModel(
      id: 'm10',
      name: 'Sambal Ijo Lado Mudo',
      category: 'Sayur',
      description:
          'Cabe hijau keriting dan tomat hijau kukus ulek kasar dengan minyak tanak kelapa dan teri gurih.',
      price: 10000,
      rating: 4.9,
      reviewCount: 680,
      imageUrl: 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=600',
      spiciness: 2,
      badge: 'PEDAS GURIH',
      salesCount: '800 terjual',
      portionInfo: '1 Porsi Sambal 100ml',
    ),
    // 11. Es Tebak Padang
    MenuItemModel(
      id: 'm11',
      name: 'Es Tebak Segar Minang',
      category: 'Minuman',
      description:
          'Dessert Ranah Minang berisi tebak tepung beras kenyal, cincau hitam, tape singkong, santan manis dan sirup.',
      price: 15000,
      rating: 4.9,
      reviewCount: 430,
      imageUrl: 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600',
      spiciness: 0,
      badge: 'SEGAR',
      salesCount: '430 terjual',
      portionInfo: '1 Gelas Es Tebak',
    ),
    // 12. Teh Talua Pinang
    MenuItemModel(
      id: 'm12',
      name: 'Teh Talua Pinang',
      category: 'Minuman',
      description:
          'Minuman penambah stamina khas Minang dari teh kental, kuning telur bebek berbusa lembut, susu, dan jeruk nipis.',
      price: 18000,
      rating: 4.8,
      reviewCount: 310,
      imageUrl: 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=600',
      spiciness: 0,
      badge: 'KHAS',
      salesCount: '310 terjual',
      portionInfo: '1 Gelas Teh Talua',
    ),
  ];
}
