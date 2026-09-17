import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('restaurantApp', () => ({
        selectedBranch: 'Jakarta Selatan',
        selectedBranchId: 1,
        selectedCategory: 'all',
        searchQuery: '',
        isCartOpen: false,
        isMobileMenuOpen: false,
        isScrolled: false,
        isNavbarHidden: false,
        orderType: 'dine-in', // 'dine-in' or 'takeaway'
        tableNumber: '',
        customerName: '',
        orderNotes: '',
        cart: [],
        notificationMessage: '',
        showNotification: false,

        selectBranch(city, id) {
            this.selectedBranch = city;
            this.selectedBranchId = id;
            try {
                localStorage.setItem('raso_minang_branch_id', id);
                localStorage.setItem('raso_minang_branch_name', city);
            } catch(e) {}
            this.notify(`Cabang diubah ke ${city}`);
        },

        init() {
            // Load saved cart from localStorage if available
            try {
                const savedCart = localStorage.getItem('raso_minang_cart');
                if (savedCart) {
                    this.cart = JSON.parse(savedCart);
                }
                const savedBranchId = localStorage.getItem('raso_minang_branch_id');
                const savedBranchName = localStorage.getItem('raso_minang_branch_name');
                if (savedBranchId) this.selectedBranchId = parseInt(savedBranchId);
                if (savedBranchName) this.selectedBranch = savedBranchName;
            } catch (e) {
                console.warn('Could not read from localStorage', e);
            }

            let lastScrollY = window.scrollY;
            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                this.isScrolled = currentScrollY > 20;

                // Hide navbar when scrolling down, show when scrolling up
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    this.isNavbarHidden = true;
                } else {
                    this.isNavbarHidden = false;
                }
                
                lastScrollY = currentScrollY <= 0 ? 0 : currentScrollY; // For Mobile or negative scrolling
            }, { passive: true });
        },

        saveCart() {
            try {
                localStorage.setItem('raso_minang_cart', JSON.stringify(this.cart));
            } catch (e) {
                console.warn('Could not save cart to localStorage', e);
            }
        },

        addToCart(item) {
            const existing = this.cart.find(c => c.id === item.id);
            if (existing) {
                existing.quantity += 1;
            } else {
                this.cart.push({
                    id: item.id,
                    nama: item.nama,
                    harga: Number(item.harga),
                    foto: item.foto,
                    quantity: 1
                });
            }
            this.saveCart();
            this.notify(`${item.nama} ditambahkan ke pesanan!`);
        },

        updateQuantity(itemId, delta) {
            const index = this.cart.findIndex(c => c.id === itemId);
            if (index !== -1) {
                this.cart[index].quantity += delta;
                if (this.cart[index].quantity <= 0) {
                    this.cart.splice(index, 1);
                }
                this.saveCart();
            }
        },

        removeFromCart(itemId) {
            this.cart = this.cart.filter(c => c.id !== itemId);
            this.saveCart();
            this.notify('Menu dihapus dari pesanan');
        },

        clearCart() {
            this.cart = [];
            this.orderType = 'dine-in';
            this.tableNumber = '';
            this.orderNotes = '';
            this.saveCart();
        },

        get cartCount() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get cartTotal() {
            return this.cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        notify(msg) {
            this.notificationMessage = msg;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 2500);
        },

        async checkout() {
            if (this.cart.length === 0) return;
            
            this.notify('Memproses pesanan...');
            
            try {
                const payload = {
                    branch_id: this.selectedBranchId || 1, 
                    order_type: this.orderType === 'dine-in' ? 'dine_in' : 'takeaway',
                    table_number: this.tableNumber,
                    customer_name: this.customerName,
                    notes: this.orderNotes,
                    items: this.cart.map(item => ({
                        menu_item_id: item.id,
                        quantity: item.quantity
                    }))
                };

                const response = await fetch('/api/v1/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.clearCart();
                    // Redirect to order status page
                    window.location.href = `/pesanan/${data.data.qr_code_token}`;
                } else {
                    this.notify(data.message || 'Gagal memproses pesanan');
                }
            } catch (error) {
                console.error(error);
                this.notify('Terjadi kesalahan jaringan.');
            }
        }
    }));
});

Alpine.start();
