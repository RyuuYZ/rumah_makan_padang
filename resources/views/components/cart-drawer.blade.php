<!-- Floating Cart Trigger Button -->
<button @click="isCartOpen = true" 
        class="fixed bottom-6 right-6 z-30 bg-[#7A1F2B] hover:bg-[#3D0F15] text-white p-4 rounded-full shadow-2xl transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center border-2 border-[#C9A227]/40 group"
        aria-label="Keranjang Pesanan">
    <svg class="w-6 h-6 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
    <span x-show="cartCount > 0" 
          x-text="cartCount"
          class="absolute -top-1.5 -right-1.5 bg-[#C9A227] text-[#241B16] font-bold text-xs w-6 h-6 flex items-center justify-center rounded-full shadow border-2 border-white">
    </span>
</button>

<!-- Slide-Over Drawer -->
<div x-show="isCartOpen" 
     class="fixed inset-0 z-50 overflow-hidden" 
     style="display: none;"
     x-cloak>
    
    <!-- Backdrop -->
    <div x-show="isCartOpen"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="isCartOpen = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div x-show="isCartOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
            
            <!-- Drawer Header -->
            <div class="p-6 bg-[#7A1F2B] text-white flex items-center justify-between border-b border-[#C9A227]/30">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-[#C9A227]/20 flex items-center justify-center text-[#C9A227]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-serif font-bold text-xl leading-tight">Keranjang Pesanan</h2>
                        <span class="text-xs text-white/80">Cabang: <strong x-text="selectedBranch"></strong></span>
                    </div>
                </div>
                <button @click="isCartOpen = false" class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Drawer Items -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <template x-if="cart.length === 0">
                    <div class="text-center py-16">
                        <div class="w-20 h-20 mx-auto rounded-full bg-[#F5EFE2] flex items-center justify-center text-[#7A1F2B]/40 mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h4 class="font-serif font-bold text-lg text-[#241B16]">Keranjang Masih Kosong</h4>
                        <p class="text-sm text-[#241B16]/60 mt-1 max-w-xs mx-auto">
                            Pilih hidangan masakan Padang autentik favorit Anda untuk mulai memesan.
                        </p>
                        <button @click="isCartOpen = false" class="mt-6 bg-[#7A1F2B] text-white px-6 py-2.5 rounded-xl font-medium text-sm hover:bg-[#3D0F15] transition-all">
                            Lihat Pilihan Menu
                        </button>
                    </div>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center space-x-3 p-3 bg-[#F5EFE2]/70 rounded-2xl border border-[#C9A227]/20">
                        <img :src="item.foto" :alt="item.nama" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-[#241B16] truncate" x-text="item.nama"></h4>
                            <span class="text-xs font-semibold text-[#7A1F2B]" x-text="formatRupiah(item.harga)"></span>
                            
                            <div class="flex items-center space-x-2 mt-2">
                                <button @click="updateQuantity(item.id, -1)" 
                                        class="w-6 h-6 rounded-md bg-white border border-[#241B16]/20 flex items-center justify-center text-[#241B16] hover:bg-[#7A1F2B] hover:text-white transition-colors text-xs font-bold">
                                    -
                                </button>
                                <span class="text-xs font-bold text-[#241B16] w-6 text-center" x-text="item.quantity"></span>
                                <button @click="updateQuantity(item.id, 1)" 
                                        class="w-6 h-6 rounded-md bg-white border border-[#241B16]/20 flex items-center justify-center text-[#241B16] hover:bg-[#7A1F2B] hover:text-white transition-colors text-xs font-bold">
                                    +
                                </button>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-[#241B16] block mb-2" x-text="formatRupiah(item.harga * item.quantity)"></span>
                            <button @click="removeFromCart(item.id)" class="text-rose-600 hover:text-rose-800 text-xs p-1" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Order Options (Moved to scrollable area) -->
                <div x-show="cart.length > 0" class="mt-6 pt-6 border-t border-[#C9A227]/20 space-y-3">
                    <h3 class="font-serif font-bold text-[#7A1F2B] mb-2 text-sm">Informasi Pemesanan</h3>
                    <div>
                        <input type="text" x-model="customerName" placeholder="Atas Nama (Mis: Budi / Gojek / Bawa Sendiri)" class="w-full text-sm px-3 py-2 rounded-lg border border-[#C9A227]/30 bg-[#F5EFE2]/50 focus:bg-white focus:border-[#C9A227] focus:ring focus:ring-[#C9A227]/20 transition-colors">
                    </div>
                    <div class="flex items-center space-x-4 px-1">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" x-model="orderType" value="dine-in" class="form-radio text-[#7A1F2B] focus:ring-[#7A1F2B]">
                            <span class="text-sm text-[#241B16] font-medium">Makan di Tempat</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" x-model="orderType" value="takeaway" class="form-radio text-[#7A1F2B] focus:ring-[#7A1F2B]">
                            <span class="text-sm text-[#241B16] font-medium">Bungkus</span>
                        </label>
                    </div>
                    
                    <div x-show="orderType === 'dine-in'" x-collapse>
                        <input type="text" x-model="tableNumber" placeholder="Nomor Meja (opsional)" class="w-full text-sm px-3 py-2 rounded-lg border border-[#C9A227]/30 bg-[#F5EFE2]/50 focus:bg-white focus:border-[#C9A227] focus:ring focus:ring-[#C9A227]/20 transition-colors">
                    </div>
                    
                    <div>
                        <textarea x-model="orderNotes" rows="2" placeholder="Catatan pesanan (mis: Gulai dipisah, sambal banyakan...)" class="w-full text-sm px-3 py-2 rounded-lg border border-[#C9A227]/30 bg-[#F5EFE2]/50 focus:bg-white focus:border-[#C9A227] focus:ring focus:ring-[#C9A227]/20 transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer / Checkout -->
            <div x-show="cart.length > 0" class="p-6 bg-[#F5EFE2] border-t border-[#C9A227]/30 space-y-4 shadow-[0_-10px_20px_rgba(0,0,0,0.03)] z-10">
                
                <div class="flex items-center justify-between text-sm text-[#241B16]/80">
                    <span>Jumlah Menu:</span>
                    <span class="font-bold" x-text="cartCount + ' Porsi'"></span>
                </div>
                <div class="flex items-center justify-between text-base font-bold text-[#241B16] pt-2 border-t border-[#C9A227]/20">
                    <span>Total Tagihan:</span>
                    <span class="text-xl text-[#7A1F2B]" x-text="formatRupiah(cartTotal)"></span>
                </div>

                <div class="space-y-2 pt-2">
                    <div class="bg-amber-50 p-2 rounded-lg border border-amber-200 text-center mb-2">
                        <span class="text-[10px] text-amber-800 font-medium">
                            <svg class="w-3.5 h-3.5 inline mb-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tunjukkan QR ke kasir untuk melakukan pembayaran.
                        </span>
                    </div>

                    <button @click="checkout()" 
                            class="w-full bg-[#C9A227] hover:bg-[#B38F23] text-[#241B16] py-3.5 px-4 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>Buat QR Pesanan</span>
                    </button>
                    
                    <button @click="clearCart()" class="w-full text-xs text-[#241B16]/60 hover:text-rose-700 py-1 font-medium transition-colors">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
