/// Model pengguna / user sesi
class UserModel {
  final String id;
  final String name;
  final String email;
  final String phone;
  final String address;
  final String avatarUrl;
  final String memberTier;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.address,
    this.avatarUrl = '',
    this.memberTier = 'Minang Gold',
  });

  UserModel copyWith({
    String? id,
    String? name,
    String? email,
    String? phone,
    String? address,
    String? avatarUrl,
    String? memberTier,
  }) {
    return UserModel(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      address: address ?? this.address,
      avatarUrl: avatarUrl ?? this.avatarUrl,
      memberTier: memberTier ?? this.memberTier,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'address': address,
      'avatarUrl': avatarUrl,
      'memberTier': memberTier,
    };
  }

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as String? ?? 'user_1',
      name: json['name'] as String? ?? 'Sutan Bagindo',
      email: json['email'] as String? ?? 'sutan@rasamandeh.com',
      phone: json['phone'] as String? ?? '081234567890',
      address: json['address'] as String? ?? 'Jl. Khatib Sulaiman No. 42, Padang Barat',
      avatarUrl: json['avatarUrl'] as String? ?? '',
      memberTier: json['memberTier'] as String? ?? 'Minang Gold',
    );
  }

  /// User demo default
  factory UserModel.demo() {
    return const UserModel(
      id: 'demo_user_1',
      name: 'Budi Pratama',
      email: 'budi@rasamandeh.com',
      phone: '0812-9876-5432',
      address: 'Jl. Sudirman No. 18, Padang Barat, Kota Padang',
      avatarUrl: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
      memberTier: 'Minang Gold Member',
    );
  }
}
