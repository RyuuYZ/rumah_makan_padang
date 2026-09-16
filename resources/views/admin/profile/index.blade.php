@extends('layouts.admin')

@section('title', 'Profil & ID Card - Raso Mandeh')
@section('header_title', 'Profil Pengguna')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

<div class="p-6 lg:p-8 space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        
        <!-- Kolom Kiri: ID Card & Action Buttons -->
        <div class="md:col-span-5 lg:col-span-4 flex flex-col items-center space-y-4">
            
            <!-- Main Card Container -->
            <div id="id-card-element" class="w-full max-w-[340px] bg-white rounded-[32px] shadow-xl relative flex flex-col border border-neutral-100 overflow-hidden">
                <!-- Notch/Slot for lanyard -->
                <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-16 h-2.5 bg-white/40 rounded-full border border-white/60 shadow-inner z-20 backdrop-blur-sm"></div>

                <!-- Top Header Gradient -->
                <div class="h-40 bg-gradient-to-br from-[#7A1F2B] to-[#9A2A38] relative flex flex-col items-center justify-center rounded-b-[40%]">
                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-overlay"></div>
                </div>

                <!-- Profile Picture -->
                <div class="flex justify-center -mt-16 relative z-20 group">
                    <label for="profilePhotoInput" class="cursor-pointer w-[110px] h-[110px] rounded-full bg-white p-1.5 shadow-[0_8px_16px_rgba(0,0,0,0.1)] block relative overflow-hidden transition-transform hover:scale-105">
                        <div class="w-full h-full rounded-full flex items-center justify-center overflow-hidden relative shadow-inner bg-gradient-to-br from-neutral-200 to-neutral-300">
                            @if(Auth::user()->profile_photo_url)
                                <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover" id="idCardAvatar">
                            @else
                                <span class="text-5xl font-serif font-bold text-neutral-500 absolute">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                            @endif
                        </div>
                        
                        <!-- Upload Overlay -->
                        <div class="absolute inset-0 m-1.5 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </label>
                    <input type="file" id="profilePhotoInput" class="hidden" accept="image/*">
                </div>

                <!-- Body Text -->
                <div class="pt-5 px-6 text-center">
                    <h1 class="font-black text-lg text-[#0F172A] leading-tight mb-1 uppercase tracking-tight">{{ Auth::user()->name }}</h1>
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <div class="h-px w-8 bg-neutral-200"></div>
                        <p class="text-xs font-bold text-[#64748B] italic">{{ Auth::user()->role ?? 'Administrator' }}</p>
                        <div class="h-px w-8 bg-neutral-200"></div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="px-8 pb-6 text-left space-y-3.5">
                    <div class="grid grid-cols-12 gap-1 items-start">
                        <span class="col-span-3 text-[11px] font-bold text-[#334155] tracking-wide pt-0.5">EMAIL</span>
                        <span class="col-span-1 text-[11px] font-bold text-[#334155] text-center pt-0.5">:</span>
                        <span class="col-span-8 text-[12px] font-medium text-[#475569] break-all pt-0.5">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="grid grid-cols-12 gap-1 items-start">
                        <span class="col-span-3 text-[11px] font-bold text-[#334155] tracking-wide pt-1">STATUS</span>
                        <span class="col-span-1 text-[11px] font-bold text-[#334155] text-center pt-1">:</span>
                        <span class="col-span-8 text-[11px] font-bold text-emerald-600 flex items-center gap-1.5">
                            <span class="px-2 py-0.5 bg-emerald-100 rounded text-emerald-700">Aktif</span>
                        </span>
                    </div>
                    <div class="grid grid-cols-12 gap-1 items-start">
                        <span class="col-span-3 text-[11px] font-bold text-[#334155] tracking-wide pt-0.5">ROLE</span>
                        <span class="col-span-1 text-[11px] font-bold text-[#334155] text-center pt-0.5">:</span>
                        <span class="col-span-8 text-[12px] font-medium text-[#475569] capitalize pt-0.5">{{ Auth::user()->role ?? 'Admin' }}</span>
                    </div>
                </div>

                <!-- Action Buttons & Info (Will be excluded on download) -->
                <div id="action-buttons-section" class="px-6 pb-8 space-y-3">
                    <!-- B23: Tombol Edit Profil Saya telah dihapus karena fitur belum tersedia (Dead Button) -->
                    
                    @if(Auth::user()->login_token)
                    <button onclick="downloadIDCard()" id="btnDownloadID" class="w-full py-3 bg-[#10B981] hover:bg-[#059669] text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download ID Card</span>
                    </button>
                    <p class="text-[10px] text-[#64748B] text-center leading-relaxed mt-4 px-2">
                        Gunakan QR Code pada ID Card ini untuk login instan tanpa menggunakan password (Sign in with ID Card).
                    </p>

                    <!-- Hidden QR for Download generation -->
                    <img id="qr-image" src="{!! \App\Helpers\QrCodeHelper::generate(Auth::user()->login_token, 300) !!}" crossorigin="anonymous" class="hidden" alt="Login QR">
                    @else
                    <div class="w-full p-3 bg-rose-50 text-rose-600 rounded-lg text-center border border-rose-100">
                        <p class="text-[10px] font-bold">Token Login Belum Tersedia</p>
                        <p class="text-[9px] mt-0.5 opacity-80">Harap hubungi Superadmin.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Data -->
        <div class="md:col-span-7 lg:col-span-8 space-y-6">
            
            <!-- Keamanan 2FA Panel -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 flex items-center space-x-2 bg-neutral-50/50">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h3 class="font-bold text-sm text-neutral-800">Keamanan Dua Langkah (2FA)</h3>
                </div>
                
                @if(session('success'))
                <div class="px-6 py-3 bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('info'))
                <div class="px-6 py-3 bg-blue-50 text-blue-700 text-xs font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
                @endif

                <div class="p-6 md:flex items-center justify-between gap-6">
                    <div class="flex-1 mb-4 md:mb-0">
                        <p class="text-[11px] text-neutral-500 leading-relaxed mb-3">
                            Tambahkan lapisan keamanan ekstra pada akun Anda. Setelah diaktifkan, masuk ke sistem memerlukan password dan kode verifikasi satu kali (OTP) dari aplikasi Google Authenticator di perangkat seluler Anda.
                        </p>
                        <div class="flex items-center space-x-2 text-[11px] font-bold">
                            <span class="text-neutral-500 uppercase tracking-wider">Status 2FA:</span>
                            @if(Auth::user()->two_factor_confirmed_at)
                                <span class="text-emerald-600">AKTIF</span>
                            @else
                                <span class="text-rose-600">TIDAK AKTIF</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 shrink-0">
                        @if(Auth::user()->two_factor_confirmed_at)
                            <form action="{{ route('admin.2fa.disable') }}" method="POST" onsubmit="return confirmDisable2FA(this)">
                                @csrf
                                <input type="hidden" name="password" id="disable_2fa_password">
                                <button type="submit" class="px-4 py-2 bg-[#E14848] hover:bg-[#c93b3b] text-white text-xs font-semibold rounded-lg transition-colors shadow-sm flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    <span>Nonaktifkan 2FA</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.2fa.setup') }}" class="px-4 py-2 bg-[#006A8E] hover:bg-[#005877] text-white text-xs font-semibold rounded-lg transition-colors shadow-sm flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Aktifkan 2FA Sekarang</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informasi Lengkap Panel -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-100 bg-neutral-50/50">
                    <h3 class="font-bold text-sm text-neutral-800">Informasi Lengkap Administrator</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Grid Items -->
                        <div class="p-4 border border-neutral-100 rounded-xl bg-white shadow-sm">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Nama Lengkap</p>
                            <p class="text-sm font-semibold text-neutral-800">{{ Auth::user()->name }}</p>
                        </div>
                        
                        <div class="p-4 border border-neutral-100 rounded-xl bg-white shadow-sm">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Email Utama</p>
                            <p class="text-sm font-semibold text-[#006A8E]">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="p-4 border border-neutral-100 rounded-xl bg-white shadow-sm">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Role Jabatan</p>
                            <p class="text-sm font-semibold text-neutral-800 capitalize">{{ Auth::user()->role ?? 'Admin Pusat' }}</p>
                        </div>

                        <div class="p-4 border border-neutral-100 rounded-xl bg-white shadow-sm">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Terdaftar Sejak</p>
                            <p class="text-sm font-semibold text-neutral-800">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : '10 Aug 2026' }}</p>
                        </div>
                        
                        <div class="col-span-1 sm:col-span-2 p-4 border border-neutral-100 rounded-xl bg-white shadow-sm">
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Hak Akses Sistem</p>
                            <p class="text-sm font-semibold text-neutral-800">Semua Fitur (Manajemen Pesanan, POS Kasir, Laporan Keuangan)</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal Cropper -->
<div id="cropperModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-opacity opacity-0 pointer-events-none">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform" id="cropperModalContent">
        <div class="p-4 border-b border-neutral-100 flex justify-between items-center">
            <h3 class="font-bold text-neutral-800">Sesuaikan Foto Profil</h3>
            <button type="button" id="closeCropperBtn" class="text-neutral-400 hover:text-rose-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 flex justify-center bg-neutral-900" style="height: 350px;">
            <!-- Target image for Cropper.js -->
            <img id="imageToCrop" src="" class="max-w-full max-h-full">
        </div>
        <div class="p-4 bg-neutral-50 flex justify-end gap-3">
            <button type="button" id="cancelCropBtn" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-600 text-sm font-semibold rounded-lg hover:bg-neutral-100 transition-colors">Batal</button>
            <button type="button" id="saveCropBtn" class="px-4 py-2 bg-[#006A8E] hover:bg-[#005877] text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                <span id="saveCropText">Simpan Foto</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function confirmDisable2FA(form) {
    const pwd = prompt('Masukkan password Anda untuk menonaktifkan 2FA:');
    if (pwd) {
        document.getElementById('disable_2fa_password').value = pwd;
        return true;
    }
    return false;
}

// --- CROPPER JS LOGIC ---
let cropper;
const inputImage = document.getElementById('profilePhotoInput');
const modal = document.getElementById('cropperModal');
const modalContent = document.getElementById('cropperModalContent');
const imageToCrop = document.getElementById('imageToCrop');
const saveBtn = document.getElementById('saveCropBtn');
const cancelBtn = document.getElementById('cancelCropBtn');
const closeBtn = document.getElementById('closeCropperBtn');

function openModal() {
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modalContent.classList.remove('scale-95');
    modalContent.classList.add('scale-100');
}

function closeModal() {
    modal.classList.add('opacity-0', 'pointer-events-none');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    inputImage.value = '';
}

[closeBtn, cancelBtn].forEach(btn => btn.addEventListener('click', closeModal));

inputImage.addEventListener('change', function (e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const url = URL.createObjectURL(file);
        imageToCrop.src = url;
        
        openModal();
        
        // Inisialisasi cropper setelah modal terbuka
        setTimeout(() => {
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1, // Rasio 1:1 wajib
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }, 150);
    }
});

saveBtn.addEventListener('click', function () {
    if (!cropper) return;
    
    const saveText = document.getElementById('saveCropText');
    const originalText = saveText.innerHTML;
    saveText.innerHTML = 'Menyimpan...';
    saveBtn.disabled = true;

    // Get cropped canvas
    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
    });
    
    // Get base64 string
    const base64Image = canvas.toDataURL('image/png');

    // Upload to server
    fetch('{{ route("admin.profile.photo") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            photo: base64Image
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to reflect new photo everywhere (sidebar, ID card, navbar)
            window.location.reload();
        } else {
            alert('Gagal mengunggah foto profil.');
        }
    })
    .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan saat mengunggah foto.');
    })
    .finally(() => {
        saveText.innerHTML = originalText;
        saveBtn.disabled = false;
        closeModal();
    });
});

// --- ID CARD GENERATOR LOGIC ---
function downloadIDCard() {
    const btn = document.getElementById('btnDownloadID');
    const card = document.getElementById('id-card-element');
    const qrImg = document.getElementById('qr-image');
    
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="animate-pulse">Menyiapkan...</span>';
    btn.disabled = true;

    if (qrImg && !qrImg.complete) {
        qrImg.onload = () => generateIDCanvas(card, btn, originalText);
    } else {
        generateIDCanvas(card, btn, originalText);
    }
}

function generateIDCanvas(card, btn, originalText) {
    // Generate an off-screen clone for proper download formatting
    const downloadCard = card.cloneNode(true);
    // Make sure it has specific dimensions for high-quality export
    downloadCard.style.width = '340px';
    downloadCard.style.padding = '0';
    downloadCard.style.backgroundColor = 'white';
    downloadCard.style.borderRadius = '32px';
    downloadCard.style.overflow = 'hidden';

    // Hapus tombol aksi dari versi cetak
    const actionSection = downloadCard.querySelector('#action-buttons-section');
    if (actionSection) {
        actionSection.remove();
    }

    // Tambahkan QR Code di dasar ID Card
    const qrSection = document.createElement('div');
    qrSection.className = 'w-full flex justify-center pb-8 pt-2 bg-white';
    const qrImg = document.getElementById('qr-image').cloneNode(true);
    qrImg.className = 'w-32 h-32 block mx-auto border border-neutral-100 p-1.5 rounded-xl shadow-sm';
    qrImg.style.display = 'block';
    
    downloadCard.appendChild(qrSection);
    qrSection.appendChild(qrImg);

    // Hide it but put in DOM
    downloadCard.style.position = 'absolute';
    downloadCard.style.left = '-9999px';
    document.body.appendChild(downloadCard);

    // For html2canvas, scale up to make it crisp
    html2canvas(downloadCard, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'ID-Card-RasoMandeh-{{ str_replace(' ', '', Auth::user()->name) }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        btn.innerHTML = originalText;
        btn.disabled = false;
        downloadCard.remove();
    }).catch(err => {
        console.error(err);
        alert('Gagal mengunduh ID Card.');
        btn.innerHTML = originalText;
        btn.disabled = false;
        downloadCard.remove();
    });
}
</script>
@endsection
