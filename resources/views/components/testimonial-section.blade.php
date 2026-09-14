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
                            {{ $review->branch ? $review->branch->kota : 'Pelanggan Setia' }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('reviewModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 font-serif" id="modal-title">
                            Tulis Ulasan Anda
                        </h3>
                        <button onclick="document.getElementById('reviewModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="nama_pelanggan">Nama Anda</label>
                                <input class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" id="nama_pelanggan" name="nama_pelanggan" type="text" required placeholder="Contoh: Budi Santoso">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="branch_id">Cabang Kunjungan (Opsional)</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" id="branch_id" name="branch_id">
                                    <option value="">Pilih Cabang</option>
                                    @foreach(\App\Models\Branch::all() as $b)
                                        <option value="{{ $b->id }}">{{ $b->kota }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Penilaian (1-5 Bintang)</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" name="rating" required>
                                    <option value="5">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                    <option value="4">⭐⭐⭐⭐ Baik</option>
                                    <option value="3">⭐⭐⭐ Cukup</option>
                                    <option value="2">⭐⭐ Kurang</option>
                                    <option value="1">⭐ Sangat Kurang</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="komentar">Komentar / Pengalaman Anda</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-[#7A1F2B]" id="komentar" name="komentar" rows="4" required placeholder="Bagaimana rasa makanan dan pelayanannya?"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('reviewModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-[#7A1F2B] text-white font-medium rounded-xl hover:bg-[#611922]">
                                Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
