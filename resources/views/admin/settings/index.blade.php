@extends('layouts.admin')

@section('title', 'Pengaturan Website - Admin Raso Mandeh')
@section('header_title', 'Pengaturan Tampilan Publik')

@section('content')
<!-- Tambahkan Cropper.js CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div class="space-y-6 max-w-4xl">
    
    <div class="bg-white p-6 rounded-2xl border border-neutral-200/80 shadow-sm">
        <div class="mb-5">
            <h3 class="text-base font-bold text-neutral-900">Ubah Teks & Gambar Website</h3>
            <p class="text-xs text-neutral-500 mt-1">Sesuaikan konten yang akan ditampilkan di halaman depan restoran.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="imageCropper()">
            @csrf

            <!-- Hero Image with Cropper -->
            <div class="space-y-3">
                <label class="block font-semibold text-neutral-700 text-sm">Hero Image (Gambar Utama)</label>
                
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <!-- Current Image Preview -->
                    <div class="w-full sm:w-1/2 aspect-[4/3] rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100 flex items-center justify-center relative group">
                        @if(isset($settings['home_hero_image']) && $settings['home_hero_image'])
                            <img src="{{ $settings['home_hero_image'] }}" class="w-full h-full object-cover" id="current-hero">
                        @else
                            <img src="/menu/nasi-padang-rendang.webp" class="w-full h-full object-cover" id="current-hero">
                        @endif
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white text-xs font-semibold">Gambar Saat Ini</span>
                        </div>
                    </div>

                    <div class="w-full sm:w-1/2 space-y-3">
                        <label class="block w-full text-center px-4 py-3 bg-neutral-50 border-2 border-dashed border-neutral-300 rounded-xl cursor-pointer hover:bg-neutral-100 hover:border-[#7A1F2B] transition-colors">
                            <span class="text-sm font-semibold text-neutral-600">Pilih Gambar Baru</span>
                            <input type="file" class="hidden" accept="image/*" @change="fileChosen">
                        </label>
                        <p class="text-xs text-neutral-500">Pilih gambar lalu potong sesuai rasio 4:3 agar tampilan rapi.</p>
                    </div>
                </div>

                <!-- Hidden Input for Cropped Base64 -->
                <input type="hidden" name="hero_image_base64" :value="croppedImageBase64">
            </div>

            <hr class="border-neutral-100 my-4">

            <div>
                <label class="block font-semibold text-neutral-700 text-sm mb-1.5">Judul Utama (Title)</label>
                <textarea name="home_title" rows="2" maxlength="100" class="w-full text-sm p-3 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none" placeholder="Rasa Autentik Minangkabau dalam Setiap Gigitan">{{ old('home_title', $settings['home_title'] ?? 'Rasa Autentik Minangkabau dalam Setiap Gigitan') }}</textarea>
            </div>

            <div>
                <label class="block font-semibold text-neutral-700 text-sm mb-1.5">Deskripsi Singkat (Subtitle)</label>
                <textarea name="home_subtitle" rows="3" maxlength="300" class="w-full text-sm p-3 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none" placeholder="Nikmati kelezatan masakan Padang legendaris...">{{ old('home_subtitle', $settings['home_subtitle'] ?? 'Nikmati kelezatan masakan Padang legendaris dengan resep warisan leluhur yang telah dijaga selama lebih dari 7 dekade. Dimasak dengan santan kental dan rempah pilihan langsung dari Sumatera Barat.') }}</textarea>
            </div>

            <div>
                <label class="block font-semibold text-neutral-700 text-sm mb-1.5">Teks Footer</label>
                <textarea name="footer_text" rows="2" maxlength="500" class="w-full text-sm p-3 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none" placeholder="Menyajikan keaslian masakan Minangkabau...">{{ old('footer_text', $settings['footer_text'] ?? 'Menyajikan keaslian masakan Minangkabau dengan resep turun-temurun sejak tahun 1950. Cita rasa otentik yang terjaga kehalalan, kebersihan, dan kenikmatannya di setiap suapan.') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold shadow-md transition-colors text-sm">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Cropper -->
    <div x-data x-show="$store.cropperModal.isOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="$store.cropperModal.close()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-5 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">Potong Gambar</h3>
                    <button @click="$store.cropperModal.close()" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="w-full max-h-[60vh] bg-neutral-100 flex justify-center overflow-hidden rounded-xl">
                    <img id="image-to-crop" class="max-w-full">
                </div>
                
                <div class="mt-5 flex justify-end gap-3">
                    <button @click="$store.cropperModal.close()" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 rounded-xl font-semibold text-neutral-700 transition-colors">
                        Batal
                    </button>
                    <button @click="$store.cropperModal.crop()" class="px-6 py-2 bg-[#7A1F2B] hover:bg-[#611922] rounded-xl font-semibold text-white transition-colors">
                        Potong & Gunakan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    
    let cropperInstance = null;
    
    Alpine.store('cropperModal', {
        isOpen: false,
        onCropSuccess: null,
        
        open(imageUrl, callback) {
            this.isOpen = true;
            this.onCropSuccess = callback;
            
            setTimeout(() => {
                const imageElement = document.getElementById('image-to-crop');
                imageElement.src = imageUrl;
                
                if (cropperInstance) {
                    cropperInstance.destroy();
                }
                
                cropperInstance = new Cropper(imageElement, {
                    aspectRatio: 4 / 3, // Sesuai rasio yang diminta
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }, 100);
        },
        
        close() {
            this.isOpen = false;
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
        },
        
        crop() {
            if (!cropperInstance) return;
            
            const canvas = cropperInstance.getCroppedCanvas({
                width: 800, // max width resolution
                height: 600,
            });
            
            const base64Image = canvas.toDataURL('image/webp', 0.9);
            
            if (this.onCropSuccess) {
                this.onCropSuccess(base64Image);
            }
            
            this.close();
        }
    });

    Alpine.data('imageCropper', () => ({
        croppedImageBase64: '',
        
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Pilih file gambar yang valid.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageUrl = e.target.result;
                Alpine.store('cropperModal').open(imageUrl, (base64) => {
                    this.croppedImageBase64 = base64;
                    document.getElementById('current-hero').src = base64; // Update preview
                });
            };
            reader.readAsDataURL(file);
            
            // Reset input so same file can be selected again if needed
            event.target.value = '';
        }
    }));
});
</script>
@endsection
