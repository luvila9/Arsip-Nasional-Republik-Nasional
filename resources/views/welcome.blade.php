<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RADAR II - Ruang Diskusi Direktorat Kearsipan Daerah II</title>    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 900: '#1e293b' }, 
                        cream: '#f4f1ea' 
                    },
                    animation: {
                        'marquee': 'marquee 25s linear infinite',
                    },
                    keyframes: {
                        marquee: {
                            '0%': { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(-100%)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&display=swap');
        
        .font-source-serif { font-family: 'Source Serif 4', serif !important; }
        .font-ibm-plex { font-family: 'IBM Plex Mono', monospace !important; }
        
        .chat-scroll::-webkit-scrollbar { height: 6px; width: 6px; }
        .chat-scroll::-webkit-scrollbar-track { background: transparent; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .marquee-container {
            display: flex;
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            align-items: center;
        }

        [x-cloak] { display: none !important; } 
    </style>
</head>
<body class="antialiased bg-[#f8fafc] relative">
    @php
        $totalAspek = count($aspekList);
        $totalTopik = 0;
        $totalDokumen = 0;

        foreach ($aspekList as $aspek) {
            if (isset($aspek['topik']) && is_array($aspek['topik'])) {
                $totalTopik += count($aspek['topik']);
                foreach ($aspek['topik'] as $topik) {
                    if (!empty($topik['dokumen'])) {
                        $totalDokumen += count($topik['dokumen']);
                    }
                }
            }
        }
        $tahunPengisian = date('Y');

        $botFaqs = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('bot_faqs')) {
                $botFaqs = \App\Models\BotFaq::where('pertanyaan', '!=', 'default_fallback_response')->inRandomOrder()->limit(3)->get();
            }
        } catch (\Exception $e) {}

        // Mengambil tanggal pembaruan terakhir dari database
        $latestDates = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('aspeks')) {
                $latestDates[] = \App\Models\Aspek::max('updated_at');
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('sub_aspeks')) {
                $latestDates[] = \App\Models\SubAspek::max('updated_at');
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('sub_sub_aspeks')) {
                $latestDates[] = \App\Models\SubSubAspek::max('updated_at');
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('bukti_dukungs')) {
                $latestDates[] = \App\Models\BuktiDukung::max('updated_at');
            }
        } catch (\Exception $e) {}

        $latestDates = array_filter($latestDates);
        $maxDate = !empty($latestDates) ? max($latestDates) : now();
        
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $timestamp = strtotime($maxDate);
        $terakhirDiperbarui = date('d', $timestamp) . ' ' . $bulanIndo[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);
    @endphp    
    <script>
        window.dbAspek = @json($aspekList);
        window.dbFaqs = @json($botFaqs);
    </script>    
    <div x-data="pageSetup()" class="min-h-screen flex flex-col">
        
        <!-- HEADER UTAMA (Desain Lama + Tanggal Terakhir Diperbarui) -->
        <header class="sticky top-0 z-50 bg-white border-b-4 border-sky-500 shadow-sm">
            <div class="max-w-[1440px] w-full mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-12 py-2.5">
                <div class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('images/logo-polos.png') }}" alt="Logo ANRI" class="h-9 sm:h-11 w-auto">
                    <div class="flex flex-col justify-center mt-0.5">
                        <h1 class="font-sans italic font-bold text-sm sm:text-base md:text-lg leading-tight text-slate-900">
                            ARSIP NASIONAL REPUBLIK INDONESIA
                        </h1>
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5 font-ibm-plex">
                            Terakhir diperbarui: {{ $terakhirDiperbarui }}
                        </p>
                    </div>
                </div>
                <!-- Tombol Kembali ke Pilihan Menu saat berada di halaman Bukti Dukung -->
                <div x-show="viewState === 'dokumen'" x-cloak>
                    <button x-on:click="viewState = 'home'; window.scrollTo({top: 0, behavior: 'smooth'});" class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-navy-900 hover:text-sky-600 transition-colors border border-slate-300 px-3 py-1.5 rounded-full bg-slate-50 hover:bg-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Menu Utama</span>
                    </button>
                </div>
            </div>
        </header>        

        <!-- RUNNING TEXT (Sticky di bawah Header) -->
        <div class="sticky top-[58px] sm:top-[66px] z-40 bg-navy-900 text-white py-2 shadow-md overflow-hidden">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">
                <div class="marquee-container">
                    <div class="animate-marquee inline-block text-xs sm:text-sm font-medium tracking-wide">
                        <span class="font-bold text-amber-400">RADAR II</span> &mdash; Ruang Diskusi Direktorat Kearsipan Daerah II. Platform referensi dan konsultasi interaktif bagi pemerintah daerah binaan.
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- STATE 1: LAYAR PILIHAN AWAL (HOME)           -->
        <!-- ========================================== -->
        <section x-show="viewState === 'home'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="relative bg-cover bg-center flex-1 flex flex-col justify-center" style="background-image: url('{{ asset('images/bg-landing-page.png') }}'); min-height: 450px;">
            <div class="absolute inset-0 bg-navy-900/60"></div>
            <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 py-16 sm:py-20 relative z-10 flex flex-col items-center text-center">
                
                <h2 class="font-source-serif font-bold text-white text-5xl sm:text-6xl md:text-7xl leading-tight mb-3 drop-shadow-lg">
                    RADAR II
                </h2>
                <p class="text-slate-100 max-w-2xl text-lg sm:text-xl font-light tracking-wide mb-10 drop-shadow-md">
                    Ruang Diskusi Direktorat Kearsipan Daerah II
                </p>

                <!-- KARTU 2 PILIHAN RUANG (Desain Lama) -->
                <div class="flex flex-col sm:flex-row gap-5 max-w-3xl w-full justify-center">
                    <!-- Ruang Konsultasi -->
                    <button x-on:click="window.dispatchEvent(new CustomEvent('open-chat'))" class="group flex items-center justify-center gap-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white p-5 rounded-2xl transition-all duration-300 shadow-xl w-full sm:w-1/2">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-lg sm:text-xl mb-0.5">Ruang Konsultasi</h3>
                            <p class="text-xs text-slate-200">Tanya jawab via Asisten Virtual</p>
                        </div>
                    </button>

                    <!-- Bukti Dukung -->
                    <button x-on:click="viewState = 'dokumen'; window.scrollTo({top: 0, behavior: 'smooth'});" class="group flex items-center justify-center gap-4 bg-amber-500 hover:bg-amber-400 text-navy-900 p-5 rounded-2xl transition-all duration-300 shadow-xl w-full sm:w-1/2">
                        <div class="w-12 h-12 bg-navy-900/10 rounded-full flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-lg sm:text-xl mb-0.5">Bukti Dukung</h3>
                            <p class="text-xs text-navy-900/80">Cari & unduh referensi dokumen</p>
                        </div>
                    </button>
                </div>
            </div>
        </section>        
        

        <!-- ========================================== -->
        <!-- STATE 2: LANDING PAGE LAMA (TANPA 2 TOMBOL)  -->
        <!-- ========================================== -->
        <div x-show="viewState === 'dokumen'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex-grow flex flex-col">
            
            <!-- Banner Hero Lama (Tanpa Tombol Konsultasi & Bukti Dukung) -->
            <section class="relative bg-cover bg-center" style="background-image: url('{{ asset('images/bg-landing-page.png') }}'); min-height: 280px;">
                <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 py-12 sm:py-16 relative z-10">
                    <h2 class="font-source-serif font-bold text-white text-4xl sm:text-5xl md:text-6xl leading-tight mb-3 max-w-2xl drop-shadow">
                        RADAR II
                    </h2>
                    <p class="text-slate-100 max-w-xl leading-relaxed text-base sm:text-xl font-medium drop-shadow">
                        Ruang Diskusi Direktorat Kearsipan Daerah II
                    </p>
                </div>
            </section>

            <!-- STATISTIK LAMA (Dengan Garis Pemisah Vertikal) -->
            <section class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 py-8 sm:py-10 border-b border-slate-200">
                <div class="grid grid-cols-2 md:flex md:flex-row items-center justify-between text-center gap-6 md:gap-0">
                    <div class="flex-1 w-full">
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-1 sm:mb-2">{{ $totalAspek }}</h2>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium">Kategori Utama</p>
                    </div>
                    <div class="hidden md:block w-[2px] h-12 bg-slate-300 shrink-0"></div>
                    <div class="flex-1 w-full">
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-1 sm:mb-2">{{ $totalTopik }}</h2>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium">Komponen / Indikator</p>
                    </div>
                    <div class="hidden md:block w-[2px] h-12 bg-slate-300 shrink-0"></div>
                    <div class="flex-1 w-full">
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-1 sm:mb-2">{{ $totalDokumen }}</h2>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium">Dokumen Bukti</p>
                    </div>
                    <div class="hidden md:block w-[2px] h-12 bg-slate-300 shrink-0"></div>
                    <div class="flex-1 w-full">
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-1 sm:mb-2">{{ $tahunPengisian }}</h2>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium">Tahun Referensi</p>
                    </div>
                </div>
            </section>        

            <!-- PENCARIAN & CHOICE CHIPS LAMA (Kapsul 1 Baris) -->
            <section id="ruang-bukti-dukung" class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 lg:pr-28 pt-8 pb-6 scroll-mt-24">
                <div class="flex flex-col gap-4 w-full">
                    <div class="relative w-full shadow-sm">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="search" placeholder="Cari dokumen atau komponen..." class="w-full pl-12 pr-4 py-3.5 bg-[#e2e8f0] border border-slate-300 rounded-md text-base text-slate-800 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-navy-900 font-sans transition-all">
                    </div>
                    
                    <div class="flex w-full gap-2.5 overflow-x-auto whitespace-nowrap pb-3 chat-scroll items-center mt-2 flex-nowrap">
                        <?php foreach($aspekList as $aspek): ?>
                            <button x-on:click="activeFilter = '{{ $aspek['id'] }}'" 
                                    :class="activeFilter == '{{ $aspek['id'] }}' ? 'bg-navy-900 text-white shadow-md border-navy-900' : 'bg-white text-slate-600 border-slate-300 hover:border-navy-900'" 
                                    class="shrink-0 px-5 py-2.5 rounded-full text-xs sm:text-sm font-medium border-2 transition-all font-ibm-plex tracking-tight">
                                {{ $aspek['kode'] }}. {{ $aspek['judul'] }}
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>       

            <!-- DAFTAR KOMPONEN & DOKUMEN LAMA -->
            <main class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 lg:pr-28 pb-24 flex-grow">
                <?php foreach($aspekList as $aspek): ?>
                    @php
                        $aspekStr = ($aspek['kode'] ?? '') . ' ' . ($aspek['judul'] ?? '');
                        if (isset($aspek['topik']) && is_array($aspek['topik'])) {
                            foreach ($aspek['topik'] as $t) {
                                $aspekStr .= ' ' . ($t['kode'] ?? '') . ' ' . ($t['judul'] ?? '');
                                if (isset($t['dokumen']) && is_array($t['dokumen'])) {
                                    foreach ($t['dokumen'] as $d) {
                                        $aspekStr .= ' ' . ($d['nama'] ?? '');
                                    }
                                }
                            }
                        }
                        $aspekStr = strtolower(trim(preg_replace('/\s+/', ' ', $aspekStr)));
                    @endphp
                    
                    <section x-show="(activeFilter == '{{ $aspek['id'] }}' || search !== '') && ('{{ addslashes($aspekStr) }}'.includes(search.toLowerCase()))" class="mb-4">                   
                        <div class="space-y-4">
                            @php $daftarTopik = $aspek['topik'] ?? []; @endphp
                            <?php foreach($daftarTopik as $topik): ?>
                                @php
                                    $searchContext = ($topik['kode'] ?? '') . ' ' . ($topik['judul'] ?? '');
                                    if (!empty($topik['dokumen'])) {
                                        foreach ($topik['dokumen'] as $doc) {
                                            $searchContext .= ' ' . ($doc['nama'] ?? '');
                                        }
                                    }
                                    $searchContext = strtolower(trim(preg_replace('/\s+/', ' ', $searchContext)));
                                    $topikJudulTampil = trim(preg_replace('/\s+/', ' ', $topik['judul'] ?? ''));
                                @endphp
                                
                                <div x-data="{ open: false }" x-show="search === '' || '{{ addslashes($searchContext) }}'.includes(search.toLowerCase())" class="border border-slate-400 overflow-hidden shadow-sm rounded-sm">
                                    <button x-on:click="open = !open" class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-slate-200 hover:bg-slate-300 transition-colors px-4 sm:px-6 py-3 sm:py-4 text-left border-b border-slate-400">
                                        <span class="text-slate-900 text-base sm:text-lg font-source-serif leading-snug">
                                            <span class="font-bold mr-1 sm:mr-2">{{ $topik['kode'] ?? '' }}</span>
                                            <span class="font-normal" x-html="highlight('{{ addslashes($topikJudulTampil) }}', search)"></span>
                                        </span>
                                        <span class="flex items-center justify-between sm:justify-end gap-4 shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
                                            @if (!empty($topik['dokumen']))
                                                <span class="text-xs sm:text-sm font-serif border border-amber-600 text-amber-700 rounded-full px-3 py-1 whitespace-nowrap bg-transparent">
                                                    {{ count($topik['dokumen']) }} Dokumen
                                                </span>
                                            @endif
                                            <svg class="h-5 w-5 text-slate-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </span>
                                    </button>
                                    
                                    <div x-show="open" x-transition class="bg-white">
                                        @php $daftarDokumen =$topik['dokumen'] ?? []; @endphp
                                        @if (!empty($daftarDokumen))
                                            <div class="flex flex-col divide-y divide-slate-400">
                                                <?php foreach($daftarDokumen as$dokumen): ?>
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 hover:bg-slate-50 transition-colors">
                                                        <p class="text-sm sm:text-base text-slate-900 font-source-serif leading-relaxed" x-html="highlight('{{ addslashes(trim(preg_replace('/\s+/', ' ', $dokumen['nama'] ?? ''))) }}', search)"></p>
                                                        @if(!empty($dokumen['url']))
                                                            <a href="{{ $dokumen['url'] }}" target="_blank" rel="noopener noreferrer" class="shrink-0 flex items-center justify-center sm:justify-start w-full sm:w-auto gap-2 border border-slate-500 text-slate-800 hover:bg-slate-100 hover:text-black transition-colors px-4 py-2 sm:py-1.5 text-sm font-serif bg-white rounded-sm">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                <span>Unduh</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        @else
                                            <p class="text-slate-600 text-sm font-source-serif italic px-4 sm:px-6 py-4 sm:py-5">Belum ada dokumen yang diunggah untuk komponen ini.</p>
                                        @endif
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
                
                <div x-show="!hasResults" x-cloak class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-24 h-24 mb-5 bg-slate-200 text-slate-400 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-source-serif font-bold text-navy-900 mb-2">Pencarian Tidak Ditemukan</h3>
                    <p class="text-slate-500 max-w-md mx-auto leading-relaxed">
                        Maaf, kami tidak menemukan dokumen atau komponen yang cocok dengan kata kunci <span class="font-bold text-slate-700" x-text="`&quot;${search}&quot;`"></span>.
                    </p>
                </div>
            </main>        
        </div>

        <!-- FOOTER LAMA -->
        <footer class="bg-[#1e293b] text-slate-300 py-8 sm:py-10 border-t-4 border-sky-500 mt-auto relative z-20">
            <div class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ asset('images/logo-polos.png') }}" alt="Logo ANRI" class="h-10 sm:h-12 brightness-0 invert opacity-95">
                            <h4 class="font-sans font-bold text-base sm:text-lg text-white tracking-wide">RADAR II</h4>
                        </div>
                        <p class="font-source-serif text-xs sm:text-sm leading-relaxed text-slate-400 max-w-2xl">
                            <strong class="text-slate-200">Arsip Nasional Republik Indonesia</strong> &mdash; Portal ini memuat referensi dan instrumen dokumen kearsipan yang disusun berdasarkan materi pendampingan Direktorat Kearsipan Daerah II ANRI.
                        </p>
                    </div>
                    <div class="flex flex-col md:items-end">
                        <h5 class="text-xs sm:text-sm font-bold text-white mb-3 uppercase tracking-widest border-b border-slate-600 pb-2 w-full md:w-auto md:border-none md:pb-0">Pusat Layanan</h5>
                        <ul class="space-y-2.5 font-source-serif text-xs sm:text-sm w-full md:w-auto">
                            <li class="flex items-center md:justify-end gap-2.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg><a href="tel:+62217805851" class="hover:text-white hover:underline transition-colors">+62 21 7805851</a></li>
                            <li class="flex items-center md:justify-end gap-2.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><a href="mailto:info@anri.go.id" class="hover:text-white hover:underline transition-colors">info@anri.go.id</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-700 mt-8 pt-5 flex flex-col md:flex-row justify-between items-center gap-3 text-xs font-source-serif text-slate-500 text-center md:text-left">
                    <p>&copy; {{ date('Y') }} Arsip Nasional Republik Indonesia.</p>
                    <p class="uppercase tracking-wide">Direktorat Kearsipan Daerah II</p>
                </div>
            </div>
        </footer>
    </div>    

    <!-- CHATBOT FLOATING WIDGET (Desain Lama bg-cream) -->
    <div x-data="smartChatbot()" x-on:open-chat.window="isOpen = true" class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans">      
        <div x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-300 transform origin-bottom-right" x-transition:enter-start="opacity-0 scale-50 translate-y-10" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200 transform origin-bottom-right" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-50 translate-y-10" class="w-80 sm:w-[380px] h-[500px] max-h-[80vh] bg-cream rounded-xl shadow-2xl flex flex-col mb-4 overflow-hidden border border-slate-200" style="display: none;">
            <div class="bg-navy-900 px-4 py-3 flex items-center justify-between text-white shadow-md z-10">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo 1.png') }}" alt="Logo ANRI" class="h-10 sm:h-12 brightness-0 invert opacity-95">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm tracking-wide">Asisten Virtual RADAR II</span>
                        <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span><span class="text-[10px] text-slate-300">Aktif &middot; siap menjawab</span></div>
                    </div>
                </div>
                <button x-on:click="isOpen = false" class="text-slate-300 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>            
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto chat-scroll flex flex-col gap-3">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex w-full" :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">
                        <div x-show="msg.sender === 'bot'" class="bg-white border border-slate-200 text-slate-800 p-3 rounded-xl rounded-tl-sm text-sm shadow-sm max-w-[85%] leading-relaxed">
                            <template x-if="!msg.isHtml"><span x-text="msg.text"></span></template>
                            <template x-if="msg.isHtml">
                                <div x-html="msg.text" class="space-y-1" x-init="$nextTick(() => { 
                                    let btns = document.querySelectorAll('.bg-white button'); 
                                    for(let i = 0; i < btns.length; i++) { 
                                        let attr = btns[i].getAttribute('x-on:click'); 
                                        if(attr) { 
                                            let match = attr.match(/'([^']+)'/);
                                            if(match) btns[i].onclick = () => send(match[1]); 
                                        } 
                                    } 
                                })"></div>
                            </template>
                        </div>
                        <div x-show="msg.sender === 'user'" class="bg-navy-900 text-white p-3 rounded-xl rounded-tr-sm text-sm shadow-sm max-w-[85%] leading-relaxed"><span x-text="msg.text"></span></div>
                    </div>
                </template>                
                <div x-show="isTyping" class="flex justify-start">
                    <div class="bg-white border border-slate-200 p-3 rounded-xl rounded-tl-sm shadow-sm max-w-[85%] flex gap-1"><div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div><div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div><div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div></div>
                </div>
            </div>
            
            <div class="px-4 pb-2 pt-1 overflow-x-auto whitespace-nowrap chat-scroll flex gap-2">
                <button x-on:click="send('Cara mencari dokumen')" class="border border-slate-300 bg-white text-slate-600 hover:text-navy-900 hover:border-navy-900 px-3 py-1.5 rounded-full text-xs font-medium transition-colors shadow-sm shrink-0">Cara mencari dokumen</button>
                <template x-for="faq in faqs" :key="faq.id || faq.pertanyaan">
                    <button x-on:click="send(faq.pertanyaan)" 
                            class="border border-slate-300 bg-white text-slate-600 hover:text-navy-900 hover:border-navy-900 px-3 py-1.5 rounded-full text-xs font-medium transition-colors shadow-sm shrink-0" 
                            x-text="faq.pertanyaan">
                    </button>
                </template>
            </div>

            <div class="p-3 bg-white border-t border-slate-200">
                <form x-on:submit.prevent="send()" class="relative flex items-center">
                    <input type="text" x-model="inputText" placeholder="Tulis pertanyaan Anda..." class="w-full pl-4 pr-12 py-3 border border-slate-300 rounded-full text-sm focus:outline-none focus:border-navy-900 focus:ring-1 focus:ring-navy-900 transition-colors">
                    <button type="submit" class="absolute right-1.5 w-10 h-10 bg-navy-900 hover:bg-slate-800 text-white rounded-full flex items-center justify-center transition-colors"><svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg></button>
                </form>
            </div>
        </div>
        
        <button x-on:click="isOpen = !isOpen" class="w-14 h-14 bg-navy-900 hover:bg-slate-800 text-white rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-105 relative">
            <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>            
            <span x-show="!isOpen" class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full"></span>
        </button>
    </div>

    <script>
        function pageSetup() {
            return {
                viewState: 'home', 
                activeFilter: (window.dbAspek && window.dbAspek.length > 0) ? window.dbAspek[0].id.toString() : '',
                search: '',
                db: window.dbAspek || [],
                
                get hasResults() {
                    if (this.search.trim() === '') return true;
                    let q = this.search.toLowerCase();
                    let found = false;
                    for (let aspek of this.db) {
                        if (this.activeFilter !== 'semua' && this.activeFilter != aspek.id) continue;
                        let aspekStr = (aspek.kode || '') + ' ' + (aspek.judul || '');
                        if (aspek.topik) {
                            for (let t of aspek.topik) {
                                aspekStr += ' ' + (t.kode || '') + ' ' + (t.judul || '');
                                if (t.dokumen) {
                                    for (let d of t.dokumen) {
                                        aspekStr += ' ' + (d.nama || '');
                                    }
                                }
                            }
                        }
                        aspekStr = aspekStr.replace(/\s+/g, ' ').toLowerCase();
                        if (aspekStr.includes(q)) {
                            found = true;
                            break;
                        }
                    }
                    return found;
                },

                highlight(text, q) {
                    if (!q || q.trim() === '') return text;
                    const escapedQ = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(`(${escapedQ})`, 'gi');
                    return text.replace(regex, '<mark class="bg-amber-300 text-slate-900 font-bold px-1 rounded-sm shadow-sm">$1</mark>');
                }
            }
        }

        function smartChatbot() {
            return {
                isOpen: false,
                inputText: '',
                isTyping: false,
                faqs: window.dbFaqs || [],
                
                messages: [{ sender: 'bot', text: 'Halo! Selamat datang di Ruang Diskusi Direktorat Kearsipan Daerah II (RADAR II). Saya adalah Asisten Virtual RADAR II.<br><br>Mari wujudkan penyelenggaraan kearsipan daerah yang tertib, transparan, dan akuntabel.<br><br>Ada yang bisa saya bantu terkait penelusuran komponen atau referensi dokumen hari ini?', isHtml: true }],
                
                init() {
                    this.$watch('messages', () => {
                        this.$nextTick(() => {
                            const box = document.getElementById('chat-messages');
                            if(box) box.scrollTop = box.scrollHeight;
                        });
                    });
                },

                async send(suggestedText = null) {
                    let query = suggestedText || this.inputText.trim();
                    if(!query) return;
                    
                    let tampilanPengunjung = query;
                    if (query.match(/\[ASPEK_\d+\]/)) {
                        tampilanPengunjung = "Melihat daftar komponen...";
                    } else if (query.match(/\[SUB_\d+\]/)) {
                        tampilanPengunjung = "Melihat daftar dokumen...";
                    }
                    
                    this.messages.push({ sender: 'user', text: tampilanPengunjung, isHtml: false });
                    this.inputText = '';
                    this.isTyping = true; 

                    try {
                        let response = await fetch('/chat/ask', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message: query })
                        });

                        let data = await response.json();
                        this.isTyping = false; 
                        
                        this.messages.push({ sender: 'bot', text: data.reply, isHtml: true });
                        
                    } catch (error) {
                        this.isTyping = false;
                        this.messages.push({ sender: 'bot', text: 'Terjadi kesalahan jaringan saat menghubungi server kami.', isHtml: false });
                    }
                }
            }
        }
    </script>
</body>
</html>