<section id="ulasan" class="py-20 bg-[#F5EFE2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between max-w-7xl mx-auto mb-14 gap-6">
            <div class="max-w-2xl">
                <span class="text-[#7A1F2B] font-semibold text-xs uppercase tracking-widest block mb-2">Suara Pelanggan</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#241B16]">
                    Kata Mereka Tentang Raso Mandeh
                </h2>
                <p class="text-sm sm:text-base text-[#241B16]/75 mt-3">
                    Kisah kepuasan dari pecinta kuliner Minang di berbagai pelosok kota.
                </p>
            </div>
            
            <button onclick="document.getElementById('reviewModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-6 py-3 bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold rounded-xl shadow-md transition-all whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Tulis Ulasan Anda
            </button>
        </div>

        @if(session('success_review'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-8 relative flex items-center justify-between" role="alert">
            <span class="block sm:inline text-sm">{{ session('success_review') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($reviews as $review)
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200 border border-[#C9A227]/20 flex flex-col justify-between space-y-4">
                <div>
                    <!-- Star Rating -->
                    <div class="flex items-center space-x-1 text-[#C9A227] mb-3">
                        @for($i = 0; $i < $review->rating; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>

                    <!-- Comment Quote -->
                    <p class="text-sm text-[#241B16]/80 italic leading-relaxed">
                        "{{ $review->komentar }}"
                    </p>
                </div>

                <!-- Customer Info -->
                <div class="pt-4 border-t border-[#C9A227]/15 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-[#7A1F2B]/10 text-[#7A1F2B] font-bold text-sm flex items-center justify-center flex-shrink-0 border border-[#7A1F2B]/20">
                        {{ strtoupper(substr($review->nama_pelanggan, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h5 class="font-bold text-sm text-[#241B16] truncate">{{ $review->nama_pelanggan }}</h5>
                        <span class="text-xs text-[#241B16]/60 block truncate">
                            @if($review->menuItem)
                                Mengulas <strong>{{ $review->menuItem->nama }}</strong>
                            @else
                                {{ $review->branch ? $review->branch->kota : 'Pelanggan Setia' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    <!-- Review Modal -->
    <div id="reviewModal" x-data="reviewApp()" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 font-serif" id="modal-title">
                            Tulis Ulasan Pesanan
                        </h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step 1: Check Order -->
                    <div x-show="step === 1">
                        <p class="text-sm text-gray-600 mb-4">Masukkan kode pesanan Anda untuk menulis ulasan produk yang Anda nikmati. Contoh: RM-X8V3AQ-13092026</p>
                        <div class="space-y-4 text-sm max-w-2xl">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="order_number">Kode Pesanan</label>
                                <input x-model="order_number" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" id="order_number" type="text" placeholder="Masukkan Kode Pesanan">
                            </div>
                            <div x-show="error_message" class="text-red-500 text-xs font-semibold" x-text="error_message"></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 max-w-2xl">
                            <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200">
                                Batal
                            </button>
                            <button type="button" @click="checkOrder()" class="px-6 py-2 bg-[#7A1F2B] text-white font-medium rounded-xl hover:bg-[#611922]" :disabled="isLoading">
                                <span x-show="!isLoading">Lanjut</span>
                                <span x-show="isLoading">Memeriksa...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Review Form -->
                    <div x-show="step === 2" style="display: none;">
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" :value="order.id">
                            <input type="hidden" name="branch_id" :value="order.branch_id">
                            
                            <div class="text-sm">
                                <div class="max-w-md mb-6">
                                    <label class="block text-gray-700 font-bold mb-2" for="nama_pelanggan">Nama Anda</label>
                                    <input class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" id="nama_pelanggan" name="nama_pelanggan" type="text" required :value="order.customer_name" maxlength="50">
                                </div>
                                
                                <h4 class="font-bold text-gray-900 border-b pb-2 mb-4">Produk yang Dipesan</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 overflow-x-auto pb-2">
                                    <template x-for="(item, index) in items" :key="item.menu_item_id">
                                        <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 flex flex-col h-full">
                                            <div x-show="!item.is_reviewed" class="flex flex-col h-full">
                                                <input type="hidden" :name="'reviews['+index+'][menu_item_id]'" :value="item.menu_item_id">
                                                
                                                <div class="font-bold text-base text-[#7A1F2B] mb-3 line-clamp-1" x-text="item.name" :title="item.name"></div>
                                                
                                                <div class="mb-3">
                                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#7A1F2B] text-sm bg-white" :name="'reviews['+index+'][rating]'" required>
                                                        <option value="5">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                                        <option value="4">⭐⭐⭐⭐ Baik</option>
                                                        <option value="3">⭐⭐⭐ Cukup</option>
                                                        <option value="2">⭐⭐ Kurang</option>
                                                        <option value="1">⭐ Sangat Kurang</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="flex-grow">
                                                    <textarea class="w-full h-full min-h-[80px] px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#7A1F2B] text-sm resize-none" :name="'reviews['+index+'][komentar]'" required placeholder="Bagaimana rasa makanan ini? (Maks 150 karakter)" maxlength="150"></textarea>
                                                </div>
                                            </div>
                                            <div x-show="item.is_reviewed" class="flex flex-col h-full justify-center items-center text-center py-4">
                                                <div class="font-semibold text-base text-gray-500 mb-2 line-clamp-2" x-text="item.name"></div>
                                                <div class="text-sm text-emerald-600 font-medium">✅ Sudah diulas</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <div x-show="items.filter(i => !i.is_reviewed).length === 0" class="text-center py-6 font-semibold text-gray-500">
                                    Semua produk dalam pesanan ini sudah diulas.
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                                <button type="button" @click="step = 1" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200">
                                    Kembali
                                </button>
                                <button type="submit" class="px-6 py-2 bg-[#7A1F2B] text-white font-medium rounded-xl hover:bg-[#611922]" x-show="items.filter(i => !i.is_reviewed).length > 0">
                                    Kirim Ulasan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reviewApp', () => ({
                step: 1,
                order_number: '',
                error_message: '',
                isLoading: false,
                order: {},
                items: [],
                
                closeModal() {
                    document.getElementById('reviewModal').classList.add('hidden');
                    this.step = 1;
                    this.order_number = '';
                    this.error_message = '';
                },
                
                async checkOrder() {
                    if (!this.order_number) {
                        this.error_message = 'Silakan masukkan kode pesanan';
                        return;
                    }
                    
                    this.isLoading = true;
                    this.error_message = '';
                    
                    try {
                        const response = await fetch('{{ route('reviews.check-order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                            },
                            body: JSON.stringify({ order_number: this.order_number })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.order = data.order;
                            this.items = data.items;
                            this.step = 2;
                        } else {
                            this.error_message = data.message || 'Terjadi kesalahan.';
                        }
                    } catch (error) {
                        this.error_message = 'Gagal menghubungi server.';
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
</section>
