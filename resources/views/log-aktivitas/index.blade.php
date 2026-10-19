<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - Portal Bukti Dukung</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --bg-color: #f8fafc; --text-main: #334155; --text-muted: #64748b; --card-bg: #ffffff; --border: #e2e8f0; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .main-panel { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .top-header { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.3rem; color: var(--text-main); cursor: pointer; }
        .breadcrumb { font-size: 1rem; color: var(--text-main); font-weight: 600; text-transform: uppercase; }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .btn-logout { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;}
        .btn-logout:hover { background: #ef4444; color: white; }

        .content { padding: 25px; width: 100%; max-width: 100%; }
        
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 20px; border: 1px solid var(--border); min-width: 0; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 15px;}
        .card-header h3 { font-size: 1.1rem; font-weight: 600; color: var(--text-main); margin: 0;}
        
        .search-box { position: relative; width: 100%; max-width: 300px; }
        .search-box input { width: 100%; padding: 10px 15px 10px 35px; border-radius: 20px; border: 1px solid #cbd5e1; font-size: 0.85rem; outline: none; transition: 0.2s;}
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;}

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { text-align: left; padding: 12px 15px; font-size: 0.85rem; color: var(--text-muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px; font-size: 0.9rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .text-wrap { word-break: break-word; white-space: normal; line-height: 1.5; }
        .badge-status { font-size: 0.75rem; padding: 5px 10px; border-radius: 6px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; }

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .user-info { display: none; }
            .content { padding: 15px; }
            .top-header { padding: 15px; }
            .breadcrumb { font-size: 0.85rem; }
            .card { padding: 15px; overflow: hidden; }
            td { padding: 12px 10px; }
            .card-header { flex-direction: column; align-items: flex-start; }
            .search-box { max-width: 100%; }
        }

        nav .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between { display: none !important; } 
        .pagination { display: flex; padding-left: 0; list-style: none; gap: 6px; margin-top: 20px; justify-content: center; align-items: center; flex-wrap: wrap; }
        .page-item .page-link { position: relative; display: block; padding: 6px 12px; color: var(--text-main); text-decoration: none; background-color: white; border: 1px solid var(--border); border-radius: 6px; font-size: 0.85rem; font-weight: 500; transition: 0.2s; }
        .page-item .page-link:hover { background-color: #f1f5f9; border-color: #cbd5e1; color: var(--primary); }
        .page-item.active .page-link { z-index: 3; color: white; background-color: var(--primary); border-color: var(--primary); }
        .page-item.disabled .page-link { color: #94a3b8; pointer-events: none; background-color: #f8fafc; border-color: var(--border); }
    </style>
</head>
<body>
    @include('components.sidebar')
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
                <div class="breadcrumb">Log Aktivitas</div>
            </div>
            <div class="header-actions">
                <span class="user-info">Halo, {{ Auth::user()->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span class="hidden sm:inline">Keluar</span></button>
                </form>
            </div>
        </header>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px; color: var(--primary);"></i> Daftar Aktivitas Sistem</h3>                    
                    <form action="{{ route('log.index') }}" method="GET" class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" placeholder="Cari aksi atau pengguna..." value="{{ request('search') }}">
                    </form>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Waktu</th>
                                <th width="20%">Pengguna</th>
                                <th width="15%">Aksi</th>
                                <th width="45%">Deskripsi Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $index => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>                  
                                <td style="white-space: nowrap;">
                                    <strong style="color: #0f172a; font-size: 0.85rem; display: block;">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</strong>
                                    <span style="color: #64748b; font-size: 0.75rem;">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB</span>
                                </td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 0.9rem; display: block;">{{ $log->user->name ?? 'Administrator ANRI' }}</strong>
                                    <span style="color: #64748b; font-size: 0.75rem;">{{ $log->user->unit_kerja ?? '-' }}</span>
                                </td>      
                                <td>
                                    @php
                                        $aksi = strtolower($log->aksi ?? $log->action ?? '');
                                        $bg = '#f1f5f9'; $color = '#475569'; $icon = 'fa-info-circle'; $label = 'Info';
               
                                        if (str_contains($aksi, 'hapus') || str_contains($aksi, 'delete')) {
                                            $bg = '#fee2e2'; $color = '#b91c1c'; $icon = 'fa-trash'; $label = 'Hapus';
                                        } elseif (str_contains($aksi, 'tambah') || str_contains($aksi, 'create')) {
                                            $bg = '#dcfce3'; $color = '#166534'; $icon = 'fa-plus'; $label = 'Tambah';
                                        } elseif (str_contains($aksi, 'ubah') || str_contains($aksi, 'update')) {
                                            $bg = '#fef3c7'; $color = '#b45309'; $icon = 'fa-pen'; $label = 'Ubah';
                                        }
                                    @endphp
                                    <span class="badge-status" style="background: {{ $bg }}; color: {{ $color }};">
                                        <i class="fa-solid {{ $icon }}"></i> {{ ucfirst($log->aksi ?? $label) }}
                                    </span>
                                </td>      
                                <td class="text-wrap" style="color: #475569;">
                                    {{ $log->deskripsi ?? $log->description ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                    Belum ada catatan log aktivitas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px;">
                    {{ $logs->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('mobileToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>