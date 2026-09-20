import 'package:flutter/material.dart';
import '../models/menu_item_model.dart';
import '../services/api_service.dart';
import '../services/mock_data_service.dart';

enum SortOption {
  popular('Terlaris'),
  ratingHigh('Rating Tertinggi'),
  priceLow('Harga Terendah'),
  priceHigh('Harga Tertinggi');

  final String label;
  const SortOption(this.label);
}

/// Provider katalog menu masakan Padang
class MenuProvider extends ChangeNotifier {
  List<MenuItemModel> _items = [];
  String _selectedCategory = 'Semua';
  String _searchQuery = '';
  SortOption _selectedSort = SortOption.popular;
  bool _isLoading = false;
  final Set<String> _favoriteItemIds = {'m1', 'm3'};

  MenuProvider() {
    _loadMenu();
  }

  List<MenuItemModel> get allItems => _items;
  String get selectedCategory => _selectedCategory;
  String get searchQuery => _searchQuery;
  SortOption get selectedSort => _selectedSort;
  bool get isLoading => _isLoading;
  Set<String> get favoriteItemIds => _favoriteItemIds;

  bool isFavorite(String id) => _favoriteItemIds.contains(id);

  void toggleFavorite(String id) {
    if (_favoriteItemIds.contains(id)) {
      _favoriteItemIds.remove(id);
    } else {
      _favoriteItemIds.add(id);
    }
    notifyListeners();
  }

  List<MenuItemModel> get popularItems =>
      _items.where((item) => item.isPopular).toList();

  List<MenuItemModel> get filteredItems {
    var result = List<MenuItemModel>.from(_items);

    // Filter kategori cerdas sesuai chip Figma
    if (_selectedCategory != 'Semua') {
      final cat = _selectedCategory.toLowerCase();
      result = result.where((item) {
        final itemCat = item.category.toLowerCase();
        final itemName = item.name.toLowerCase();

        if (cat == 'paket') {
          return itemCat.contains('paket') || itemName.contains('paket') || itemName.contains('nasi +');
        } else if (cat == 'daging') {
          return itemCat.contains('daging') ||
              itemCat.contains('utama') ||
              itemName.contains('rendang') ||
              itemName.contains('daging') ||
              itemName.contains('dendeng') ||
              itemName.contains('tunjang');
        } else if (cat == 'ayam') {
          return itemCat.contains('ayam') ||
              itemCat.contains('bebek') ||
              itemName.contains('ayam') ||
              itemName.contains('bebek');
        } else if (cat == 'ikan') {
          return itemCat.contains('ikan') ||
              itemCat.contains('seafood') ||
              itemName.contains('ikan') ||
              itemName.contains('kakap') ||
              itemName.contains('cumi');
        } else if (cat == 'sayur') {
          return itemCat.contains('sayur') ||
              itemCat.contains('pelengkap') ||
              itemName.contains('singkong') ||
              itemName.contains('telur') ||
              itemName.contains('sambal') ||
              itemName.contains('nangka');
        } else if (cat == 'minuman') {
          return itemCat.contains('minuman') ||
              itemName.contains('es') ||
              itemName.contains('teh') ||
              itemName.contains('jus');
        }
        return itemCat.contains(cat) || itemName.contains(cat);
      }).toList();
    }

    // Filter pencarian
    if (_searchQuery.trim().isNotEmpty) {
      final query = _searchQuery.toLowerCase().trim();
      result = result.where((item) {
        return item.name.toLowerCase().contains(query) ||
            item.description.toLowerCase().contains(query) ||
            item.category.toLowerCase().contains(query);
      }).toList();
    }

    // Sorting
    switch (_selectedSort) {
      case SortOption.popular:
        result.sort((a, b) {
          if (a.isPopular && !b.isPopular) return -1;
          if (!a.isPopular && b.isPopular) return 1;
          return b.reviewCount.compareTo(a.reviewCount);
        });
        break;
      case SortOption.ratingHigh:
        result.sort((a, b) => b.rating.compareTo(a.rating));
        break;
      case SortOption.priceLow:
        result.sort((a, b) => a.price.compareTo(b.price));
        break;
      case SortOption.priceHigh:
        result.sort((a, b) => b.price.compareTo(a.price));
        break;
    }

    return result;
  }

  Future<void> _loadMenu() async {
    _isLoading = true;
    notifyListeners();

    try {
      final remoteItems = await ApiService.getMenuItems(branchId: 1);
      if (remoteItems.isNotEmpty) {
        _items = remoteItems;
      } else {
        _items = List.from(MockDataService.allMenuItems);
      }
    } catch (_) {
      _items = List.from(MockDataService.allMenuItems);
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> refreshMenu({int? branchId}) async {
    _isLoading = true;
    notifyListeners();

    try {
      final remoteItems = await ApiService.getMenuItems(branchId: branchId ?? 1);
      if (remoteItems.isNotEmpty) {
        _items = remoteItems;
      }
    } catch (_) {}

    _isLoading = false;
    notifyListeners();
  }

  void setCategory(String category) {
    _selectedCategory = category;
    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  void setSortOption(SortOption sort) {
    _selectedSort = sort;
    notifyListeners();
  }

  MenuItemModel? findById(String id) {
    try {
      return _items.firstWhere((e) => e.id == id);
    } catch (_) {
      return null;
    }
  }
}
