<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sub Aspek - Portal Bukti Dukung</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        div:where(.swal2-container) {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    
    <style>
        :root { --primary: #2563eb; --bg-color: #f8fafc; --text-main: #334155; --text-muted: #64748b; --card-bg: #ffffff; --border: #e2e8f0; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .main-panel { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-header { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.3rem; color: var(--text-main); cursor: pointer; }
        .breadcrumb { font-size: 1rem; color: var(--text-main); font-weight: 600; text-transform: uppercase; display: flex; align-items: center; gap: 15px; }
        .btn-back { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; color: var(--text-main); text-decoration: none; font-size: 0.85rem; transition: 0.2s;}
        .btn-back:hover { background: #e2e8f0; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .btn-logout { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;}
        .btn-logout:hover { background: #ef4444; color: white; }

        .content { padding: 25px; }
        .dashboard-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 20px; border: 1px solid var(--border); }
        .card-header h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 12px; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        textarea.form-control { resize: vertical; min-height: 100px; line-height: 1.5; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 15px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; flex: 1; color: white; transition: 0.2s;}
        .btn-primary { background-color: var(--primary); }
        .btn-secondary { background-color: #64748b; display: none; } 

        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { text-align: left; padding: 12px 15px; font-size: 0.85rem; color: var(--text-muted); border-bottom: 2px solid var(--border); }
        td { padding: 15px; font-size: 0.9rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .text-wrap { white-space: normal; line-height: 1.5; min-width: 250px; } 
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background-color: #f3e8ff; color: #7e22ce; white-space: nowrap; display: inline-block;}
        
        .action-btns { display: flex; gap: 8px; align-items: center; flex-wrap: nowrap; }
        .btn-sm { height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; text-decoration: none; white-space: nowrap; flex-shrink: 0; }
        .btn-view { background-color: #a855f7; padding: 0 12px; font-size: 0.8rem; font-weight: 600; }
        .btn-edit { background-color: #f59e0b; width: 32px;}
        .btn-delete { background-color: #ef4444; width: 32px;}

        .pagination-container { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: var(--text-muted); }
        .pagination { display: flex; list-style: none; gap: 5px; margin: 0; padding: 0; }
        .pagination li a, .pagination li span { padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); text-decoration: none; background: white; }
        .pagination li.active span { background-color: var(--primary); color: white; border-color: var(--primary); }
        .pagination li a:hover { background-color: #f1f5f9; }

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
            .user-info { display: none; }
            .content { padding: 15px; }
            .breadcrumb { font-size: 0.85rem; line-height: 1.4; flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
                <div class="breadcrumb">
                    <a href="{{ route('aspek.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> KEMBALI</a>
                    Master Data / Sub Aspek (Level 2)
                </div>
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
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; color: #1e3a8a;">
                <span style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">MENGELOLA SUB ASPEK DARI:</span>
                <h2 style="font-size: 1.2rem; margin-top: 5px;">{{ $aspek->nama_aspek }}</h2>
            </div>
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header"><h3 id="formTitle">Tambah Sub Aspek</h3></div>
                    <form id="subAspekForm" action="{{ route('subaspek.store', $aspek->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="methodInput" value="POST">      
                        <div class="form-group">
                            <label>Nama Sub Aspek (Level 2)</label>
                            <textarea name="nama_sub_aspek" id="nama_sub_aspek" class="form-control" placeholder="Masukkan nama sub aspek..." required>{{ old('nama_sub_aspek') }}</textarea>
                            @error('nama_sub_aspek')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Link Publik (Opsional)</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-link" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                                <input type="url" name="link" id="link" class="form-control" style="padding-left: 35px;" placeholder="https://contoh.com/dokumen" value="{{ old('link') }}">
                            </div>
                            @error('link')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="btn-group">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Simpan Sub Aspek</button>
                            <button type="button" id="btnCancel" class="btn btn-secondary" onclick="resetForm()">Batal</button>
                        </div>
                    </form>
                </div>
                <div class="card" style="overflow-x: auto;">
                    <div class="card-header"><h3>Daftar Sub Aspek</h3></div>
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="50%">Deskripsi Sub Aspek (Level 2)</th>
                                <th width="15%" style="text-align: center;">Jumlah Sub-sub</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subAspeks as $index => $sub)
                            <tr style="{{ !$sub->is_active ? 'background-color: #f8fafc; opacity: 0.6;' : '' }}">
                                <td>{{ $subAspeks->firstItem() + $index }}</td>
                                <td class="text-wrap">
                                    <strong style="{{ !$sub->is_active ? 'text-decoration: line-through;' : '' }}">
                                        {{ $sub->nama_sub_aspek }}
                                    </strong>              
                                    @if(!$sub->is_active)
                                        <span style="font-size: 0.7rem; background: #ef4444; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">Nonaktif</span>
                                    @endif
                                    @if($sub->link)
                                        <br>
                                        <a href="{{ $sub->link }}" target="_blank" style="display: inline-block; margin-top: 8px; font-size: 0.8rem; color: #2563eb; text-decoration: none;" title="Buka Link Publik">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Link Publik
                                        </a>
                                    @endif
                                </td>
                                <td style="text-align: center;"><span class="badge">{{ $sub->sub_sub_aspeks_count }} Sub-sub Aspek</span></td>
                                <td>
                                    <div class="action-btns">
                                        <form action="{{ route('subaspek.toggle', $sub->id) }}" method="POST" style="display:inline;" onsubmit="confirmToggle(event, this, '{{ $sub->is_active ? 'Nonaktifkan' : 'Aktifkan' }} status Sub Aspek ini?')">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="background-color: {{ $sub->is_active ? '#64748b' : '#10b981' }}; padding: 0 10px; font-size: 0.8rem; font-weight: 600;" title="{{ $sub->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fa-solid {{ $sub->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('subsubaspek.index', $sub->id) }}" class="btn-sm btn-view" title="Masuk ke Level 3">
                                            <i class="fa-solid fa-layer-group" style="margin-right: 5px;"></i> Kelola Sub-sub
                                        </a>
                                        <button class="btn-sm btn-edit" onclick="editSubAspek('{{ $sub->id }}', '{{ addslashes($sub->nama_sub_aspek) }}', '{{ $sub->link ?? '' }}')" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ route('subaspek.destroy', $sub->id) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, this, 'Data Sub Aspek dan seluruh data di bawahnya akan dihapus!')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-sm btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px;">Belum ada Sub Aspek di dalam Aspek ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        {{ $subAspeks->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function editSubAspek(id, nama, link) {
            document.getElementById('formTitle').innerText = 'Edit Sub Aspek';
            document.getElementById('subAspekForm').action = '/sub-aspek/' + id; 
            document.getElementById('methodInput').value = 'PUT';
            document.getElementById('nama_sub_aspek').value = nama;
            document.getElementById('link').value = link; 
            document.getElementById('btnSubmit').innerText = 'Simpan Perubahan';
            document.getElementById('btnSubmit').style.backgroundColor = '#f59e0b';
            document.getElementById('btnCancel').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('formTitle').innerText = 'Tambah Sub Aspek';
            document.getElementById('subAspekForm').action = '{{ route('subaspek.store', $aspek->id) }}';
            document.getElementById('methodInput').value = 'POST';
            document.getElementById('nama_sub_aspek').value = '';
            document.getElementById('link').value = ''; 
            document.getElementById('btnSubmit').innerText = 'Simpan Sub Aspek';
            document.getElementById('btnSubmit').style.backgroundColor = 'var(--primary)';
            document.getElementById('btnCancel').style.display = 'none';
        }

        function confirmDelete(event, form, message) {
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-trash" style="margin-right: 5px;"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true 
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); 
                }
            });
        }

        function confirmToggle(event, form, message) {
            event.preventDefault(); 
            
            Swal.fire({
                title: 'Ubah Status Publikasi?',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-check" style="margin-right: 5px;"></i> Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true 
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); 
                }
            });
        }
    </script>
</body>
</html>