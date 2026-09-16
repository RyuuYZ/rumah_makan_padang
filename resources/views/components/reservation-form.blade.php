<section id="booking-section" class="py-20 relative bg-[#F5EFE2] overflow-hidden">
    <!-- Decorative Batik Pattern -->
    <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-multiply pointer-events-none"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col lg:flex-row">
            
            <!-- Left Side: Image & Branding -->
            <div class="lg:w-1/2 relative min-h-[400px]">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=1000" alt="Suasana Restoran Raso Mandeh" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex flex-col justify-end p-10 lg:p-14 text-white">
                    <span class="inline-block px-4 py-1.5 bg-[#C9A227] text-white text-xs font-bold tracking-widest uppercase rounded-full mb-4 w-fit shadow-lg backdrop-blur-sm">VIP & Reguler</span>
                    <h3 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black leading-tight mb-4 drop-shadow-md">
                        Warisan Rasa Otentik <br>Minangkabau <span class="text-[#C9A227]">Sejak 1950</span>
                    </h3>
                    <p class="text-neutral-200 text-sm sm:text-base max-w-md drop-shadow">
                        Nikmati pengalaman bersantap eksklusif dengan pelayanan khas keramahtamahan Minang. Amankan meja Anda hari ini untuk momen tak terlupakan bersama keluarga.
                    </p>
                </div>
            </div>

            <!-- Right Side: Booking Form -->
            <div class="lg:w-1/2 p-8 sm:p-12 lg:p-14 bg-white relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#C9A227]/5 rounded-bl-full -z-10"></div>
                
                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-2xl sm:text-3xl font-black text-[#7A1F2B] font-serif mb-2">Reservasi Meja</h2>
                    <p class="text-neutral-500 text-sm">Isi detail di bawah ini untuk memesan meja secara online.</p>
                </div>

                @if(session('success_booking'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl mb-6 text-sm font-medium flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('success_booking') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl mb-6 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservation.store') }}" method="POST" class="space-y-5">
    @csrf
 @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Nama Lengkap *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="Contoh: Budi Santoso" 
                                class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all">
                        </div>
                        
                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Nomor WhatsApp *</label>
                            <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}" placeholder="Contoh: 08123456789" 
                                class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Date & Time -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Waktu Reservasi *</label>
                            <input type="datetime-local" name="reservation_time" required value="{{ old('reservation_time') }}" 
                                class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all">
                        </div>
                        
                        <!-- Guests -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Jumlah Tamu *</label>
                            <input type="number" name="guest_count" required min="1" max="50" value="{{ old('guest_count', 2) }}" 
                                class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all">
                        </div>
                    </div>

                    <!-- Branch -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Pilih Cabang *</label>
                        <select name="branch_id" required class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->nama }} ({{ $branch->kota }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" placeholder="Contoh: Butuh kursi tinggi (baby chair), area non-smoking, dll." 
                            class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-2 focus:ring-[#C9A227] focus:border-[#C9A227] outline-none transition-all resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#7A1F2B] hover:bg-[#9A2A38] text-white font-bold rounded-xl shadow-lg shadow-[#7A1F2B]/30 transition-all flex items-center justify-center space-x-2 group">
                        <span>Konfirmasi Booking</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    
                    <p class="text-center text-xs text-neutral-400 mt-4">
                        * Pihak restoran akan menelepon Anda maksimal 1 jam setelah pengajuan booking untuk konfirmasi.
                    </p>

                </form>
            </div>
        </div>
    </div>
</section>
