<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin - RADAR II ANRI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>    
    <style>
        :root {
            --bg-main: #f1f5f9;
            --text-dark: #020617; /* Lebih gelap (hitam pekat) untuk Boomer */
            --text-muted: #334155; /* Abu-abu gelap agar kontras tinggi */
            --border-color: #94a3b8; /* Border lebih tebal/terlihat */
            --primary: #1d4ed8; 
            --primary-light: #eff6ff;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-main); display: flex; height: 100vh; overflow: hidden; color: var(--text-dark); }

        .main-panel { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; width: 100%; }
        
        .top-header { background: var(--white); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .breadcrumb { font-size: 1.1rem; color: var(--text-dark); font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; display: flex; align-items: center; gap: 10px;}
        .user-info { font-size: 0.9rem; font-weight: 700; color: var(--text-dark); }
        
        /* Tombol Header */
        .header-actions { display: flex; align-items: center; gap: 15px; }
        .btn-print { background: #0f766e; color: var(--white); border: 1px solid #0b5c56; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 0 #0b5c56; transition: 0.2s; }
        .btn-print:active { transform: translateY(2px); box-shadow: none; }
        .btn-print:hover { background: #115e59; }
        
        .btn-logout { background: #fef2f2; border: 1px solid #ef4444; color: #dc2626; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 700; transition: 0.2s; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 0 #ef4444;}
        .btn-logout:active { transform: translateY(2px); box-shadow: none; }
        .btn-logout:hover { background: #ef4444; color: var(--white); }

        .content { padding: 30px; display: flex; flex-direction: column; gap: 25px; max-width: 1400px; margin: 0 auto; width: 100%;}

        .panel-welcome { background: var(--white); border: 1px solid var(--border-color); border-top: 5px solid var(--primary); border-radius: 4px; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .welcome-text h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-bottom: 6px; text-transform: uppercase; }
        .welcome-text p { font-size: 0.95rem; font-weight: 500; color: var(--text-muted); margin-bottom: 18px; }
        .alert-box { background-color: var(--primary-light); border: 1px solid #93c5fd; border-left: 5px solid var(--primary); padding: 12px 18px; border-radius: 2px; font-size: 0.9rem; color: #1e3a8a; display: inline-flex; align-items: center; gap: 12px; font-weight: 600;}

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .kpi-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 4px; display: flex; flex-direction: column; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .kpi-header { padding: 14px 15px; border-bottom: 1px solid var(--border-color); background: #f8fafc; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; }
        .kpi-body { padding: 25px 15px; display: flex; justify-content: space-between; align-items: center; }
        .kpi-value { font-size: 2.2rem; font-weight: 800; color: var(--text-dark); }
        .kpi-icon { width: 50px; height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--white); border: 2px solid rgba(0,0,0,0.1); }
        .kpi-footer { padding: 12px 15px; border-top: 1px solid var(--border-color); background: #f1f5f9; font-size: 0.8rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); }
        
        .bg-navy { background: #1e293b; }
        .bg-blue { background: #2563eb; }
        .bg-teal { background: #0d9488; }
        .bg-gray { background: #475569; }

        .panel-chart { background: var(--white); border: 1px solid var(--border-color); border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .panel-chart-header { padding: 18px 20px; border-bottom: 1px solid var(--border-color); background: #f8fafc; display: flex; flex-direction: column; gap: 15px; }
        
        /* Tambahan Petunjuk Eksplisit */
        .chart-header-top { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .panel-chart-title { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 10px;}
        .instruction-text { font-size: 0.85rem; font-weight: 600; color: var(--primary); background: #e0f2fe; padding: 6px 12px; border-radius: 4px; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 6px;}

        .form-filter { display: flex; gap: 12px; align-items: center; background: #f1f5f9; padding: 10px 15px; border-radius: 4px; border: 1px solid var(--border-color); }
        .filter-label { font-size: 0.85rem; font-weight: 700; color: var(--text-dark); }
        .filter-select { padding: 8px 12px; border-radius: 4px; border: 1px solid var(--border-color); font-size: 0.9rem; font-weight: 600; outline: none; background: var(--white); color: var(--text-dark); cursor: pointer; min-width: 150px;}
        .btn-filter { background: var(--text-dark); color: var(--white); border: 1px solid #000; padding: 8px 16px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 0 #000; }
        .btn-filter:active { transform: translateY(2px); box-shadow: none; }
        .btn-filter:hover { background: #334155; }
        
        .btn-detail-small { background: var(--white); border: 1px solid var(--border-color); color: var(--text-dark); padding: 5px 10px; border-radius: 3px; font-size: 0.75rem; font-weight: 700; cursor: pointer; box-shadow: 0 1px 0 var(--border-color); }
        .btn-detail-small:active { transform: translateY(1px); box-shadow: none; }
        .btn-detail-small:hover { background: #e2e8f0; }

        .panel-chart-body { padding: 25px; display: flex; justify-content: center; position: relative; height: 450px; width: 100%; }

        /* Modal & Tabel Gaya Excel (Banyak Garis) */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.8); display: flex; justify-content: center; align-items: center; z-index: 999; backdrop-filter: blur(3px); }
        .modal-content { background: var(--white); border-radius: 4px; width: 95%; max-width: 800px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); display: flex; flex-direction: column; max-height: 85vh; border: 2px solid var(--text-dark);}
        .modal-header { padding: 18px 20px; border-bottom: 2px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; z-index: 100; }
        .modal-title { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); text-transform: uppercase;}
        .btn-close { background: var(--white); border: 1px solid var(--border-color); padding: 4px 10px; font-weight: 700; font-size: 0.9rem; border-radius: 4px; cursor: pointer; color: var(--text-dark); box-shadow: 0 2px 0 var(--border-color); }
        .btn-close:active { transform: translateY(2px); box-shadow: none; }
        
        /* Menghilangkan padding atas agar sticky header menempel sempurna */
        .modal-body { padding: 0 20px 20px 20px; overflow-y: auto; background: #f8fafc; position: relative;}
        
        /* Perbaikan struktur garis tabel agar mendukung Sticky Header */
        .table-formal { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; background: var(--white); border: 2px solid var(--border-color); margin-top: 20px;}
        .table-formal th, .table-formal td { border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color); }
        .table-formal th:last-child, .table-formal td:last-child { border-right: none; }
        
        /* Menambahkan Z-index (50) dan Outline agar header kokoh di atas */
        .table-formal th { background: #cbd5e1; padding: 12px 15px; font-size: 0.85rem; color: var(--text-dark); font-weight: 800; text-transform: uppercase; position: sticky; top: 0; z-index: 50; outline: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0,0,0,0.05);}
        
        /* Modifikasi warna baris agar rapi */
        .table-formal td { padding: 12px 15px; font-size: 0.9rem; color: var(--text-dark); vertical-align: top; font-weight: 500;}
        .table-formal tr:nth-child(even) td { background-color: #f1f5f9; } 
        .table-formal tr:hover td { background-color: #e2e8f0; }
        
        .badge-level { font-size: 0.75rem; background: var(--primary); color: var(--white); padding: 4px 8px; border-radius: 3px; font-weight: 700; text-transform: uppercase; }

        /* Pengaturan Cetak Halaman (Print) */
        @media print {
            .sidebar, .top-header, .form-filter, .btn-detail-small, .btn-print, .instruction-text { display: none !important; }
            .main-panel { width: 100% !important; position: absolute; top: 0; left: 0; }
            body { background: white; }
            .content { padding: 0 !important; }
            .panel-welcome, .kpi-card, .panel-chart { box-shadow: none; border: 2px solid #000; }
        }

        @media (max-width: 1024px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .kpi-grid { grid-template-columns: 1fr; }
            .top-header { flex-direction: column; gap: 15px; align-items: flex-start;}
            .header-actions { width: 100%; justify-content: space-between; flex-wrap: wrap; }
            .form-filter { flex-direction: column; align-items: stretch; }
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ openModalHari: false, openModalBulan: false, openModalFilter: false }">
   
    @include('components.sidebar')
    
    <main class="main-panel">
        <header class="top-header">
            <div class="breadcrumb">
                <i class="fa-solid fa-server" style="color: var(--primary); font-size: 1.2rem;"></i>
                <span>DASBOR MONITORING DATA</span>
            </div>
            <div class="header-actions">
                <span class="user-info"><i class="fa-solid fa-user-tie" style="margin-right: 5px;"></i> {{ Auth::user()->name ?? 'Administrator' }}</span>
                <!-- Tombol Cetak Dokumen -->
                <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Cetak Laporan</button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</button>
                </form>
            </div>
        </header>        
        
        <div class="content">
            
            <div class="panel-welcome">
                <div class="welcome-text">
                    <h2>Sistem Informasi Manajemen RADAR II</h2>
                    <p>Direktorat Kearsipan Daerah II &mdash; Arsip Nasional Republik Indonesia</p>
                    
                    <div class="alert-box">
                        <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
                        <span>Hak akses <strong>Administrator</strong> Anda aktif. Seluruh rekapitulasi data ditampilkan secara otomatis dan terstruktur.</span>
                    </div>
                </div>
                <div style="opacity: 0.1; pointer-events: none; display: none; sm:display: block;">
                    <i class="fa-solid fa-building-columns" style="font-size: 6.5rem; color: #000;"></i>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-header">Total Aktivitas Hari Ini</div>
                    <div class="kpi-body">
                        <div class="kpi-value">{{ $hariIni }}</div>
                        <div class="kpi-icon bg-navy"><i class="fa-solid fa-file-arrow-down"></i></div>
                    </div>
                    <div class="kpi-footer">
                        <span style="color: #020617;"><i class="fa-regular fa-clock" style="margin-right:4px;"></i> Unduhan Dokumen</span>
                        @if($hariIni > 0)
                            <button @click="openModalHari = true" class="btn-detail-small"><i class="fa-solid fa-list"></i> Rincian</button>
                        @endif
                    </div>
                </div>                
                <div class="kpi-card">
                    <div class="kpi-header">Total {{ $labelBulan }}</div>
                    <div class="kpi-body">
                        <div class="kpi-value">{{ $bulanIni }}</div>
                        <div class="kpi-icon bg-blue"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div class="kpi-footer">
                        <span style="color: #020617;"><i class="fa-solid fa-filter" style="margin-right:4px;"></i> Berdasarkan Filter</span>
                        @if($bulanIni > 0)
                            <button @click="openModalBulan = true" class="btn-detail-small"><i class="fa-solid fa-list"></i> Rincian</button>
                        @endif
                    </div>
                </div>                
                <div class="kpi-card">
                    <div class="kpi-header">Total {{ $labelKunjungan }}</div>
                    <div class="kpi-body">
                        <div class="kpi-value">{{ $totalKunjungan }}</div>
                        <div class="kpi-icon bg-teal"><i class="fa-solid fa-users"></i></div>
                    </div>
                    <div class="kpi-footer">
                        <span style="color: #020617;"><i class="fa-solid fa-filter" style="margin-right:4px;"></i> Berdasarkan Filter</span>
                    </div>
                </div>                
                <div class="kpi-card">
                    <div class="kpi-header">Total Master Data Komponen</div>
                    <div class="kpi-body">
                        <div class="kpi-value">{{ $totalDataAspek }}</div>
                        <div class="kpi-icon bg-gray"><i class="fa-solid fa-database"></i></div>
                    </div>
                    <div class="kpi-footer">
                        <span style="color: #020617;"><i class="fa-solid fa-layer-group" style="margin-right:4px;"></i> Seluruh Tingkatan Aktif</span>
                    </div>
                </div>
            </div>

            <div class="panel-chart">
                <div class="panel-chart-header">
                    <div class="chart-header-top">
                        <div class="panel-chart-title">
                            <i class="fa-solid fa-chart-pie" style="color: var(--primary); font-size: 1.3rem;"></i>
                            GRAFIK DISTRIBUSI DATA: {{ strtoupper($chartTitle) }}
                        </div>
                        <div class="instruction-text">
                            <i class="fa-solid fa-circle-info"></i> Petunjuk: Gunakan form di bawah untuk mengubah data grafik.
                        </div>
                    </div>
                    
                    <form method="GET" action="" class="form-filter">
                        <span class="filter-label">Filter Periode:</span>
                        <select name="filter_month" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- Tampilkan Semua Bulan --</option>
                            <option value="01" {{ $filterMonth == '01' ? 'selected' : '' }}>Bulan Januari</option>
                            <option value="02" {{ $filterMonth == '02' ? 'selected' : '' }}>Bulan Februari</option>
                            <option value="03" {{ $filterMonth == '03' ? 'selected' : '' }}>Bulan Maret</option>
                            <option value="04" {{ $filterMonth == '04' ? 'selected' : '' }}>Bulan April</option>
                            <option value="05" {{ $filterMonth == '05' ? 'selected' : '' }}>Bulan Mei</option>
                            <option value="06" {{ $filterMonth == '06' ? 'selected' : '' }}>Bulan Juni</option>
                            <option value="07" {{ $filterMonth == '07' ? 'selected' : '' }}>Bulan Juli</option>
                            <option value="08" {{ $filterMonth == '08' ? 'selected' : '' }}>Bulan Agustus</option>
                            <option value="09" {{ $filterMonth == '09' ? 'selected' : '' }}>Bulan September</option>
                            <option value="10" {{ $filterMonth == '10' ? 'selected' : '' }}>Bulan Oktober</option>
                            <option value="11" {{ $filterMonth == '11' ? 'selected' : '' }}>Bulan November</option>
                            <option value="12" {{ $filterMonth == '12' ? 'selected' : '' }}>Bulan Desember</option>
                        </select>
                        <select name="filter_year" class="filter-select" onchange="this.form.submit()">
                            @php $currentYear = date('Y'); @endphp
                            @for($y = 2024; $y <= $currentYear + 1; $y++)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                        <button type="button" @click="openModalFilter = true" class="btn-filter"><i class="fa-solid fa-table"></i> Tampilkan Rincian Tabel</button>
                    </form>
                </div>
                <div class="panel-chart-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            
        </div>
    </main>

    <!-- MODAL FILTER TABEL -->
    <div x-show="openModalFilter" x-cloak class="modal-overlay" @click.self="openModalFilter = false" x-transition.opacity.duration.200ms>
        <div class="modal-content" x-show="openModalFilter" x-transition.scale.origin.bottom.duration.200ms>
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-file-lines" style="margin-right: 8px;"></i> Laporan Data: {{ $chartTitle }}</h3>
                <button @click="openModalFilter = false" class="btn-close">Tutup [X]</button>
            </div>
            <div class="modal-body">
                @if(count($detailFilterData) > 0)
                    <table class="table-formal">
                        <thead>
                            <tr>
                                <th width="8%">No.</th>
                                <th width="62%">Informasi Dokumen / Komponen yang Diakses</th>
                                <th width="30%">Waktu Pencatatan Sistem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailFilterData as $index => $log)
                            <tr>
                                <td><b>{{ $index + 1 }}</b></td>
                                <td>
                                    <div style="font-weight: 700; margin-bottom: 8px;">{{ $log->nama_dokumen }}</div>
                                    <span class="badge-level">Kategori: {{ $log->level }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 700;">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><i class="fa-regular fa-clock"></i> Pukul {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; border: 2px dashed var(--border-color); background: var(--white);">
                        <i class="fa-regular fa-folder-open" style="font-size: 3rem; color: var(--border-color); margin-bottom: 15px;"></i>
                        <p style="font-weight: 700; font-size: 1.1rem;">Tidak ada data tercatat pada periode yang dipilih.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL HARI INI -->
    <div x-show="openModalHari" x-cloak class="modal-overlay" @click.self="openModalHari = false" x-transition.opacity.duration.200ms>
        <div class="modal-content" x-show="openModalHari" x-transition.scale.origin.bottom.duration.200ms>
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-file-lines" style="margin-right: 8px;"></i> Laporan Aktivitas Hari Ini</h3>
                <button @click="openModalHari = false" class="btn-close">Tutup [X]</button>
            </div>
            <div class="modal-body">
                <table class="table-formal">
                    <thead>
                        <tr>
                            <th width="8%">No.</th>
                            <th width="62%">Informasi Dokumen / Komponen yang Diakses</th>
                            <th width="30%">Waktu Pencatatan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailHariIni as $index => $log)
                        <tr>
                            <td><b>{{ $index + 1 }}</b></td>
                            <td>
                                <div style="font-weight: 700; margin-bottom: 8px;">{{ $log->nama_dokumen }}</div>
                                <span class="badge-level">Kategori: {{ $log->level }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700;">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><i class="fa-regular fa-clock"></i> Pukul {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL BERDASARKAN FILTER BULAN -->
    <div x-show="openModalBulan" x-cloak class="modal-overlay" @click.self="openModalBulan = false" x-transition.opacity.duration.200ms>
        <div class="modal-content" x-show="openModalBulan" x-transition.scale.origin.bottom.duration.200ms>
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-file-lines" style="margin-right: 8px;"></i> Laporan Data: {{ $labelBulan }}</h3>
                <button @click="openModalBulan = false" class="btn-close">Tutup [X]</button>
            </div>
            <div class="modal-body">
                <table class="table-formal">
                    <thead>
                        <tr>
                            <th width="8%">No.</th>
                            <th width="62%">Informasi Dokumen / Komponen yang Diakses</th>
                            <th width="30%">Waktu Pencatatan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailBulanIni as $index => $log)
                        <tr>
                            <td><b>{{ $index + 1 }}</b></td>
                            <td>
                                <div style="font-weight: 700; margin-bottom: 8px;">{{ $log->nama_dokumen }}</div>
                                <span class="badge-level">Kategori: {{ $log->level }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700;">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><i class="fa-regular fa-clock"></i> Pukul {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#020617'; // Teks grafik lebih gelap
        Chart.defaults.font.weight = '600';
        
        let ctx = document.getElementById('pieChart').getContext('2d');
        
        const chartData = @json($chartData);
        const chartLabels = @json($chartLabels);
        const filterMonth = "{{ $filterMonth }}"; 
        
        const isEmpty = chartData.every(item => item === 0);

        if (isEmpty) {
            ctx.font = "bold 15px Inter";
            ctx.textAlign = "center";
            ctx.fillStyle = "#334155";
            ctx.fillText("TIDAK ADA DATA STATISTIK UNTUK DITAMPILKAN PADA PERIODE INI.", ctx.canvas.clientWidth / 2, ctx.canvas.clientHeight / 2);
        } else {
            
            if (!filterMonth) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: [
                                '#1e3a8a', '#1d4ed8', '#3b82f6', '#93c5fd', 
                                '#0f766e', '#14b8a6', '#5eead4', '#ccfbf1',
                                '#b45309', '#f59e0b', '#fcd34d', '#fef3c7',
                                '#334155', '#64748b', '#cbd5e1', '#f1f5f9'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { 
                                position: 'right',
                                labels: { boxWidth: 20, padding: 20, font: { size: 13, weight: '700' } } 
                            },
                            tooltip: {
                                backgroundColor: 'rgba(2, 6, 23, 0.95)',
                                padding: 15,
                                titleFont: { size: 14, weight: '800' },
                                bodyFont: { size: 14, weight: '600' },
                                cornerRadius: 4,
                                displayColors: true
                            }
                        },
                        layout: { padding: 20 }
                    }
                });
            } else {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Jumlah Unduhan Dokumen',
                            data: chartData,
                            backgroundColor: '#1d4ed8', 
                            borderColor: '#1e3a8a',
                            borderWidth: 1,
                            borderRadius: 2,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(2, 6, 23, 0.95)',
                                padding: 15,
                                callbacks: {
                                    title: function(context) { return 'Tanggal ' + context[0].label; }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { weight: '700' } },
                                grid: { color: '#cbd5e1' },
                                title: { display: true, text: 'JUMLAH AKTIVITAS', font: { weight: '800' } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: '700' } },
                                title: { display: true, text: 'TANGGAL PENCATATAN', font: { weight: '800' } }
                            }
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>