<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Aspek Utama - Portal Bukti Dukung</title>
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
        .breadcrumb { font-size: 1rem; color: var(--text-main); font-weight: 600; text-transform: uppercase; }
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
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background-color: #dbeafe; color: #1d4ed8; white-space: nowrap; display: inline-block; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-sm { height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; text-decoration: none;}
        .btn-view { background-color: #10b981; padding: 0 12px; font-size: 0.8rem; font-weight: 600; }
        .btn-edit { background-color: #f59e0b; width: 32px; }
        .btn-delete { background-color: #ef4444; width: 32px;}

        .pagination-container { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: var(--text-muted); }
        .pagination { display: flex; list-style: none; gap: 5px; margin: 0; padding: 0; }
        .pagination li a, .pagination li span { padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); text-decoration: none; background: white; }
        .pagination li.active span { background-color: var(--primary); color: white; border-color: var(--primary); }
        .pagination li a:hover { background-color: #f1f5f9; }

        /* --- MOBILE RESPONSIVE --- */
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
                <div class="breadcrumb">Master Data / Aspek Utama (Level 1)</div>
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
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header"><h3 id="formTitle">Tambah Aspek Baru</h3></div>
                    <form id="aspekForm" action="{{ route('aspek.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="methodInput" value="POST">
                        <div class="form-group">
                            <label>Nama Aspek (Level 1)</label>
                            <textarea name="nama_aspek" id="nama_aspek" class="form-control" placeholder="Contoh: Penciptaan Arsip..." required>{{ old('nama_aspek') }}</textarea>
                            @error('nama_aspek')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Link Publik (Opsional)</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-link" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                                <input type="url" name="link" id="link" value="{{ old('link') }}" class="form-control" style="padding-left: 35px;" placeholder="https://contoh.com/dokumen">
                            </div>
                            @error('link')
                                <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="btn-group">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Simpan Aspek</button>
                            <button type="button" id="btnCancel" class="btn btn-secondary" onclick="resetForm()">Batal</button>
                        </div>
                    </form>
                </div>
                <div class="card" style="overflow-x: auto;">
                    <div class="card-header"><h3>Daftar Aspek Pengawasan</h3></div>
                    <table>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="50%">Deskripsi Aspek Utama</th>
                                <th width="15%" style="text-align: center;">Jumlah Sub</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aspeks as $index => $aspek)
                            <tr>
                                <td>{{ $aspeks->firstItem() + $index }}</td>
                                <td class="text-wrap">
                                    <strong>{{ $aspek->nama_aspek }}</strong>
                                    @if($aspek->link)
                                        <br>
                                        <a href="{{ $aspek->link }}" target="_blank" style="display: inline-block; margin-top: 8px; font-size: 0.8rem; color: #2563eb; text-decoration: none;" title="Buka Link Publik">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Link Publik
                                        </a>
                                    @endif
                                </td>
                                <td style="text-align: center;"><span class="badge">{{ $aspek->sub_aspeks_count }} Sub Aspek</span></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('subaspek.index', $aspek->id) }}" class="btn-sm btn-view" title="Masuk ke Level 2">
                                            <i class="fa-solid fa-list" style="margin-right: 5px;"></i> Kelola Sub
                                        </a>
                                        <button class="btn-sm btn-edit" onclick="editAspek('{{ $aspek->id }}', '{{ addslashes($aspek->nama_aspek) }}', '{{ $aspek->link ?? '' }}')" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ route('aspek.destroy', $aspek->id) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, this, 'Semua Sub Aspek di bawahnya akan ikut terhapus secara permanen')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        {{ $aspeks->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function editAspek(id, nama, link) {
            document.getElementById('formTitle').innerText = 'Edit Data Aspek';
            document.getElementById('aspekForm').action = '/master-aspek/' + id;
            document.getElementById('methodInput').value = 'PUT';
            document.getElementById('nama_aspek').value = nama;
            document.getElementById('link').value = link; 
            document.getElementById('btnSubmit').innerText = 'Simpan Perubahan';
            document.getElementById('btnSubmit').style.backgroundColor = '#f59e0b';
            document.getElementById('btnCancel').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function resetForm() {
            document.getElementById('formTitle').innerText = 'Tambah Aspek Baru';
            document.getElementById('aspekForm').action = '{{ route('aspek.store') }}';
            document.getElementById('methodInput').value = 'POST';
            document.getElementById('nama_aspek').value = '';
            document.getElementById('link').value = '';      
            document.getElementById('btnSubmit').innerText = 'Simpan Aspek';
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
                confirmButtonText: '<i class="fa-solid fa-trash" style="margin-right: 5px;"></i> Hapus',
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