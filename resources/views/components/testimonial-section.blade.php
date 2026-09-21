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
            @forelse($reviews as $review)
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-200 border border-[#C9A227]/20 flex flex-col justify-between space-y-4">
                <div>
                    <!-- Star Rating & Pinned badge -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-1 text-[#C9A227]">
                            @for($i = 0; $i < $review->rating; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        @if($review->is_pinned)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#C9A227]/15 text-[#8F6F14]">
                            ★ Pilihan
                        </span>
                        @endif
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
                                {{ $review->branch ? 'Cabang ' . $review->branch->kota : 'Pelanggan Setia' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 bg-white/60 rounded-2xl border border-dashed border-[#C9A227]/30">
                <p class="text-[#241B16]/60 text-sm">Belum ada ulasan yang ditampilkan. Jadilah yang pertama memberikan ulasan!</p>
            </div>
            @endforelse
        </div>

    </div>

    <!-- Review Modal -->
    <div id="reviewModal" x-data="reviewApp()" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:p-0">
            <div class="fixed inset-0 bg-neutral-950/60 backdrop-blur-xs transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-middle bg-white rounded-2xl sm:rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all duration-300 w-full my-6 border border-[#C9A227]/25"
                 :class="step === 1 ? 'max-w-md sm:max-w-lg' : 'max-w-3xl sm:max-w-4xl'">
                <div class="bg-white p-6 sm:p-7">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-[#7A1F2B]/10 flex items-center justify-center text-[#7A1F2B] flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-neutral-900 font-serif leading-tight" id="modal-title">
                                    Tulis Ulasan Pesanan
                                </h3>
                                <p class="text-xs text-neutral-500 mt-0.5" x-text="step === 1 ? 'Langkah 1: Verifikasi Kode Pesanan' : 'Langkah 2: Berikan Penilaian Hidangan'"></p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors focus:outline-none" aria-label="Tutup">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step 1: Check Order -->
                    <div x-show="step === 1" class="space-y-4 pt-4">
                        <div class="p-3.5 bg-[#F5EFE2]/70 rounded-xl border border-[#C9A227]/25 flex items-start space-x-3">
                            <svg class="w-5 h-5 text-[#7A1F2B] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs sm:text-sm text-[#241B16]/80 leading-relaxed">
                                Masukkan kode pesanan Anda untuk menulis ulasan produk yang Anda nikmati. Contoh: <code class="font-bold text-[#7A1F2B] bg-white px-1.5 py-0.5 rounded border border-[#C9A227]/30">RM-X8V3AQ-13092026</code>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5" for="order_number">
                                Kode Pesanan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <input x-model="order_number" 
                                       @keydown.enter.prevent="checkOrder()"
                                       class="w-full pl-10 pr-4 py-2.5 bg-neutral-50/50 border border-neutral-300 rounded-xl text-sm text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#7A1F2B]/20 focus:border-[#7A1F2B] transition-all" 
                                       id="order_number" 
                                       type="text" 
                                       placeholder="Masukkan Kode Pesanan">
                            </div>
                            <div x-show="error_message" class="mt-2 text-xs font-semibold text-rose-600 flex items-center space-x-1" x-cloak>
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span x-text="error_message"></span>
                            </div>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-neutral-100">
                            <button type="button" @click="closeModal()" class="px-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition-colors">
                                Batal
                            </button>
                            <button type="button" @click="checkOrder()" class="px-5 py-2.5 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-xs font-semibold text-white shadow-sm hover:shadow transition-all flex items-center space-x-2" :disabled="isLoading">
                                <span x-show="!isLoading">Lanjut</span>
                                <span x-show="isLoading" class="flex items-center space-x-1.5" x-cloak>
                                    <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memeriksa...</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Review Form -->
                    <div x-show="step === 2" style="display: none;" class="space-y-4 pt-4">
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" :value="order.id">
                            <input type="hidden" name="branch_id" :value="order.branch_id">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5" for="nama_pelanggan">
                                        Nama Anda <span class="text-rose-500">*</span>
                                    </label>
                                    <input class="w-full max-w-sm px-3.5 py-2.5 bg-neutral-50/50 border border-neutral-300 rounded-xl text-sm text-neutral-900 focus:outline-none focus:ring-2 focus:ring-[#7A1F2B]/20 focus:border-[#7A1F2B] transition-all" 
                                           id="nama_pelanggan" 
                                           name="nama_pelanggan" 
                                           type="text" 
                                           required 
                                           :value="order.customer_name" 
                                           maxlength="50">
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between border-b border-neutral-200/80 pb-2 mb-3">
                                        <h4 class="font-bold text-sm text-neutral-900 font-serif">Produk yang Dipesan</h4>
                                        <span class="text-xs text-neutral-500" x-text="items.length + ' hidangan'"></span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[50vh] overflow-y-auto pr-1">
                                        <template x-for="(item, index) in items" :key="item.menu_item_id">
                                            <div class="p-4 border border-neutral-200/80 rounded-2xl bg-neutral-50/50 flex flex-col justify-between space-y-3 hover:border-[#C9A227]/40 transition-colors">
                                                <div x-show="!item.is_reviewed" class="flex flex-col space-y-3">
                                                    <input type="hidden" :name="'reviews['+index+'][menu_item_id]'" :value="item.menu_item_id">
                                                    
                                                    <div class="font-bold text-sm text-[#7A1F2B] line-clamp-1" x-text="item.name" :title="item.name"></div>
                                                    
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-neutral-600 mb-1">Rating</label>
                                                        <select class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7A1F2B]/20 focus:border-[#7A1F2B] text-xs bg-white font-medium text-neutral-800" :name="'reviews['+index+'][rating]'" required>
                                                            <option value="5">⭐⭐⭐⭐⭐ Sangat Baik (5/5)</option>
                                                            <option value="4">⭐⭐⭐⭐ Baik (4/5)</option>
                                                            <option value="3">⭐⭐⭐ Cukup (3/5)</option>
                                                            <option value="2">⭐⭐ Kurang (2/5)</option>
                                                            <option value="1">⭐ Sangat Kurang (1/5)</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-neutral-600 mb-1">Ulasan Rasa</label>
                                                        <textarea class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#7A1F2B]/20 focus:border-[#7A1F2B] text-xs resize-none bg-white placeholder-neutral-400" 
                                                                  rows="3"
                                                                  :name="'reviews['+index+'][komentar]'" 
                                                                  required 
                                                                  placeholder="Bagaimana rasa makanan ini? (Maks 150 karakter)" 
                                                                  maxlength="150"></textarea>
                                                    </div>
                                                </div>
                                                <div x-show="item.is_reviewed" class="flex flex-col items-center justify-center py-6 text-center">
                                                    <div class="font-semibold text-sm text-neutral-600 mb-1 line-clamp-2" x-text="item.name"></div>
                                                    <div class="text-xs text-emerald-600 font-semibold px-2.5 py-1 bg-emerald-50 rounded-full border border-emerald-200">✅ Sudah diulas</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div x-show="items.filter(i => !i.is_reviewed).length === 0" class="text-center py-6 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200 text-xs font-semibold text-neutral-500">
                                        Semua produk dalam pesanan ini sudah diulas.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-4 flex items-center justify-end gap-2.5 border-t border-neutral-100">
                                <button type="button" @click="step = 1" class="px-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition-colors">
                                    Kembali
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-xs font-semibold text-white shadow-sm hover:shadow transition-all" x-show="items.filter(i => !i.is_reviewed).length > 0">
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
