@include('components.sidebar')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Bukti Dukung - Portal Arsip</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0f172a; --sidebar-hover: #1e293b; --primary: #2563eb; --bg-color: #f8fafc; --text-main: #334155; --text-muted: #64748b; --card-bg: #ffffff; --border: #e2e8f0; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .main-panel { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        
        .top-header { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.3rem; color: var(--text-main); cursor: pointer; }
        .breadcrumb { font-size: 1rem; color: var(--text-main); font-weight: 600; text-transform: uppercase; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .btn-logout { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;}
        .btn-logout:hover { background: #ef4444; color: white; }
        
        .content { padding: 25px; }
        .dashboard-grid { display: grid; grid-template-columns: 400px 1fr; gap: 25px; align-items: start; }
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 20px; border: 1px solid var(--border); }
        .card-header h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #475569; }
        .form-control { width: 100%; padding: 10px 12px; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: white; }
        .form-control:focus { border-color: var(--primary); }
        select.form-control { cursor: pointer; }
        select.form-control:disabled { background-color: #f1f5f9; cursor: not-allowed; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        input[type="file"] { padding: 8px; font-size: 0.85rem; }
        .btn-primary { width: 100%; padding: 12px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; background-color: var(--primary); color: white; transition: 0.3s; margin-top: 10px;}
        .btn-primary:hover { background-color: #1d4ed8; }

        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 12px 15px; font-size: 0.85rem; color: var(--text-muted); border-bottom: 2px solid var(--border); }
        td { padding: 15px; font-size: 0.85rem; border-bottom: 1px solid var(--border); vertical-align: top; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; white-space: nowrap; }
        .status-wait { background-color: #fef08a; color: #854d0e; }
        .status-ok { background-color: #dcfce3; color: #166534; }
        .status-rev { background-color: #fee2e2; color: #991b1b; }
        .btn-download { display: inline-flex; align-items: center; gap: 5px; background: #10b981; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold; }
        .btn-delete { background: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.8rem;}

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
            .user-info { display: none; }
            .content { padding: 15px; }
        }
    </style>
</head>
<body>
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="breadcrumb">Bukti Dukung</div>
            </div>
            <div class="header-actions">
                <span class="user-info">Halo, {{ Auth::user()->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Keluar</span></button>
                </form>
            </div>
        </header>
        <div class="content">
            @if(session('success'))
                <div style="background-color: #dcfce3; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                    <i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
                </div>
            @endif
            <div class="dashboard-grid" style="{{ Auth::user()->role !== 'user' ? 'grid-template-columns: 1fr;' : '' }}">                
                @if(Auth::user()->role === 'user')
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-cloud-arrow-up text-primary"></i> Form Unggah Dokumen</h3>
                    </div>
                    <form action="{{ route('buktidukung.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>1. Pilih Aspek Utama <span style="color:red">*</span></label>
                            <select name="aspek_id" id="aspek_id" class="form-control" required>
                                <option value="">-- Pilih Aspek Utama --</option>
                                @foreach($aspeks as $aspek)
                                    <option value="{{ $aspek->id }}" {{ old('aspek_id') == $aspek->id ? 'selected' : '' }}>{{ $aspek->nama_aspek }}</option>
                                @endforeach
                            </select>
                            @error('aspek_id') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>                        
                        <div class="form-group">
                            <label>2. Pilih Sub Aspek</label>
                            <select name="sub_aspek_id" id="sub_aspek_id" class="form-control" disabled><option value="">-- Pilih Aspek Utama Dulu --</option></select>
                        </div>                        
                        <div class="form-group">
                            <label>3. Pilih Sub-sub Aspek</label>
                            <select name="sub_sub_aspek_id" id="sub_sub_aspek_id" class="form-control" disabled><option value="">-- Pilih Sub Aspek Dulu --</option></select>
                        </div>                        
                        <div class="form-group" style="margin-top: 25px; border-top: 1px dashed #cbd5e1; padding-top: 15px;">
                            <label>Nama / Judul Dokumen <span style="color:red">*</span></label>
                            <input type="text" name="nama_dokumen" class="form-control" placeholder="Contoh: SK Tata Naskah Dinas 2024" value="{{ old('nama_dokumen') }}" required>
                            @error('nama_dokumen') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>                        
                        <div class="form-group">
                            <label>Unggah File Dokumen <span style="color:red">*</span></label>
                            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.xls,.xlsx,.doc,.docx,.zip,.rar" required>
                            <small style="color: #94a3b8; display: block; margin-top: 4px;">Format: PDF/Word/Excel/ZIP/RAR (Maks 10MB)</small>
                            @error('file_dokumen') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block; font-weight: bold;">{{ $message }}</span> @enderror
                        </div>                        
                        <div class="form-group">
                            <label>Catatan Tambahan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" placeholder="Tuliskan keterangan jika perlu...">{{ old('keterangan') }}</textarea>
                            @error('keterangan') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>                        
                        <button type="submit" class="btn-primary">Unggah Dokumen</button>
                    </form>
                </div>
                @endif
                <div class="card" style="overflow-x: auto;">
                    <div class="card-header">
                        <h3>{{ Auth::user()->role === 'user' ? 'Riwayat Dokumen Anda' : 'Daftar Seluruh Bukti Dukung Unit Kerja' }}</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Nama Dokumen</th>
                                <th width="35%">Kategori Aspek</th>
                                <th width="15%">Unit Kerja</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buktiDukungs as $dokumen)
                            <tr>
                                <td>
                                    <strong>{{ $dokumen->nama_dokumen }}</strong><br>
                                    <span style="font-size: 0.75rem; color: #94a3b8;"><i class="fa-regular fa-clock"></i> {{ $dokumen->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td style="line-height: 1.4;">
                                    <span style="font-size: 0.75rem; font-weight: bold; color: #2563eb;">Lv 1:</span> {{ Str::limit($dokumen->aspek->nama_aspek ?? '-', 40) }}<br>
                                    @if($dokumen->subAspek)
                                        <span style="font-size: 0.75rem; font-weight: bold; color: #7e22ce;">Lv 2:</span> {{ Str::limit($dokumen->subAspek->nama_sub_aspek, 40) }}<br>
                                    @endif
                                    @if($dokumen->subSubAspek)
                                        <span style="font-size: 0.75rem; font-weight: bold; color: #10b981;">Lv 3:</span> {{ Str::limit($dokumen->subSubAspek->nama_sub_sub_aspek, 40) }}
                                    @endif
                                </td>
                                <td>{{ $dokumen->user->unit_kerja ?? 'Pusat' }}<br><small>({{ $dokumen->user->name }})</small></td>
                                <td>
                                    @if($dokumen->status == 'Disetujui')
                                        <span class="status-badge status-ok">Disetujui</span>
                                    @elseif($dokumen->status == 'Revisi')
                                        <span class="status-badge status-rev">Revisi</span>
                                    @else
                                        <span class="status-badge status-wait">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="btn-download" title="Lihat/Download">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        @if(Auth::user()->role !== 'user')
                                            <button class="btn-delete" style="background:#10b981;" title="Setujui"><i class="fa-solid fa-check"></i></button>
                                            <button class="btn-delete" style="background:#f59e0b;" title="Minta Revisi"><i class="fa-solid fa-rotate-left"></i></button>
                                        @endif
                                        @if(Auth::user()->role !== 'user' || (Auth::user()->role === 'user' && $dokumen->status !== 'Disetujui'))
                                        <form action="{{ route('buktidukung.destroy', $dokumen->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini secara permanen?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada dokumen.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top: 15px;">{{ $buktiDukungs->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
            </div>
        </div>
    </main>

    <script>
        const aspekSelect = document.getElementById('aspek_id');
        const subAspekSelect = document.getElementById('sub_aspek_id');
        const subSubAspekSelect = document.getElementById('sub_sub_aspek_id');
        aspekSelect.addEventListener('change', function() {
            const aspekId = this.value;
            
            subAspekSelect.innerHTML = '<option value="">-- Memuat Data... --</option>';
            subAspekSelect.disabled = true;
            subSubAspekSelect.innerHTML = '<option value="">-- Pilih Sub Aspek Dulu --</option>';
            subSubAspekSelect.disabled = true;

            if (aspekId) {
                fetch(`/get-sub-aspeks/${aspekId}`)
                    .then(response => response.json())
                    .then(data => {
                        subAspekSelect.innerHTML = '<option value="">-- Pilih Sub Aspek --</option>';
                        if (data.length > 0) {
                            data.forEach(sub => {
                                subAspekSelect.innerHTML += `<option value="${sub.id}">${sub.nama_sub_aspek}</option>`;
                            });
                            subAspekSelect.disabled = false;
                        } else {
                            subAspekSelect.innerHTML = '<option value="">-- Tidak ada Sub Aspek --</option>';
                        }
                    });
            } else {
                subAspekSelect.innerHTML = '<option value="">-- Pilih Aspek Utama Dulu --</option>';
            }
        });

        subAspekSelect.addEventListener('change', function() {
            const subAspekId = this.value;
            
            subSubAspekSelect.innerHTML = '<option value="">-- Memuat Data... --</option>';
            subSubAspekSelect.disabled = true;

            if (subAspekId) {
                fetch(`/get-sub-sub-aspeks/${subAspekId}`)
                    .then(response => response.json())
                    .then(data => {
                        subSubAspekSelect.innerHTML = '<option value="">-- Pilih Sub-sub Aspek --</option>';
                        if (data.length > 0) {
                            data.forEach(subsub => {
                                subSubAspekSelect.innerHTML += `<option value="${subsub.id}">${subsub.nama_sub_sub_aspek}</option>`;
                            });
                            subSubAspekSelect.disabled = false;
                        } else {
                            subSubAspekSelect.innerHTML = '<option value="">-- Tidak ada Sub-sub Aspek --</option>';
                        }
                    });
            } else {
                subSubAspekSelect.innerHTML = '<option value="">-- Pilih Sub Aspek Dulu --</option>';
            }
        });
    </script>
</body>
</html>