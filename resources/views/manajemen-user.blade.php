<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Portal Bukti Dukung</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb; 
            --primary-hover: #1d4ed8;
            --danger: #ef4444; 
            --bg-light: #f8fafc;
            --text-main: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }

        .main-panel { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; width: 100%; }
        
        .top-header { background: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.3rem; color: var(--text-main); cursor: pointer; }
        .breadcrumb { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }
        .breadcrumb span { color: var(--text-main); font-weight: 600; }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .btn-logout { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;}
        .btn-logout:hover { background: #ef4444; color: white; }

        .content { padding: 25px; }
        .page-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 25px; color: #0f172a; }

        .dashboard-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }
        .card { background: white; border-radius: 10px; box-shadow: var(--card-shadow); border: 1px solid var(--border); overflow: hidden; }
        .card-header { padding: 18px 20px; border-bottom: 1px solid var(--border); background: #fdfdfd; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .card-header h3 { font-size: 1.1rem; font-weight: 600; color: #0f172a; }
        .card-body { padding: 20px; }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 12px; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        select.form-control { appearance: none; background: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E') no-repeat right 12px center; background-size: 10px; }
        
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 15px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; flex: 1; text-align: center; color: white;}
        .btn-primary { background-color: var(--primary); }
        .btn-secondary { background-color: #64748b; display: none; }

        .search-box { position: relative; width: 100%; max-width: 250px; }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem; }
        .search-box input { padding-left: 35px; width: 100%; }

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { text-align: left; padding: 12px 15px; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px; font-size: 0.9rem; border-bottom: 1px solid var(--border); vertical-align: middle; white-space: nowrap; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
        .badge-superadmin { background-color: #fee2e2; color: #b91c1c; }
        .badge-admin { background-color: #dbeafe; color: #1d4ed8; }
        .badge-user { background-color: #f3f4f6; color: #4b5563; }
        .badge-active { background-color: #dcfce3; color: #166534; }

        .action-btns { display: flex; gap: 8px; }
        .btn-sm { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; }
        .btn-edit { background-color: #f59e0b; }
        .btn-delete { background-color: var(--danger); }

        .pagination-container { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: var(--text-muted); }
        .pagination { display: flex; list-style: none; gap: 5px; margin: 0; padding: 0; }
        .pagination li a, .pagination li span { padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); text-decoration: none; background: white; }
        .pagination li.active span { background-color: var(--primary); color: white; border-color: var(--primary); }
        .pagination li a:hover { background-color: #f1f5f9; }

        @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .breadcrumb { display: none; }
            .user-info { display: none; }
            .content { padding: 15px; }
            .page-title { font-size: 1.25rem; margin-bottom: 15px; }
            .card-header h3 { font-size: 1rem; }
            .search-box { max-width: 100%; margin-top: 10px; }
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
                <div class="breadcrumb"><span>MANAJEMEN USER</span></div>
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
            @if($errors->any())
                <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <ul style="margin-left: 20px;">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif
            <h1 class="page-title">Manajemen & Registrasi Pengguna</h1>
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header"><h3 id="formTitle">Registrasi Akun Baru</h3></div>
                    <div class="card-body">
                        <form id="userForm" action="{{ route('user.store') }}" method="POST">
                            @csrf 
                            <input type="hidden" name="_method" id="methodInput" value="POST">                            
                            <div class="form-group">
                                <label>Nama Lengkap <span style="color:red">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span style="color:red">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="nama@instansi.go.id" required>
                            </div>
                            <div class="form-group">
                                <label>Kata Sandi <span id="passwordAsterisk" style="color:red">*</span></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                <small id="passwordHelp" style="display:none; color: #64748b; margin-top: 5px;">*Kosongkan jika tidak ingin mengubah kata sandi.</small>
                            </div>
                            <div class="form-group">
                                <label>Peran/Role <span style="color:red">*</span></label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="" disabled selected>Pilih hak akses</option>
                                    <option value="user">User / Unit Kerja</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Unit Kerja / Instansi</label>
                                <input type="text" id="unit_kerja" name="unit_kerja" class="form-control" placeholder="Contoh: Pusat Kearsipan">
                            </div>                            
                            <div class="btn-group">
                                <button type="submit" id="btnSubmit" class="btn btn-primary">Daftarkan</button>
                                <button type="button" id="btnCancel" class="btn btn-secondary" onclick="resetForm()">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Pengguna Terdaftar</h3>                        
                        <form action="" method="GET" style="display: inline-block;">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}" onchange="this.form.submit()">
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama Lengkap</th>
                                        <th width="25%">Email</th>
                                        <th width="15%">Peran</th>
                                        <th width="15%">Unit Kerja</th>
                                        <th width="5%">Status</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role == 'super_admin') <span class="badge badge-superadmin">Super Admin</span>
                                            @elseif($user->role == 'admin') <span class="badge badge-admin">Admin</span>
                                            @else <span class="badge badge-user">User</span> @endif
                                        </td>
                                        <td>{{ $user->unit_kerja ?? '-' }}</td>
                                        <td><span class="badge badge-active">Aktif</span></td>
                                        <td>
                                            <div class="action-btns">
                                                <button type="button" class="btn-sm btn-edit" title="Edit Data" onclick="editUser('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', '{{ addslashes($user->unit_kerja ?? '') }}')">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-sm btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data pengguna yang ditemukan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>                        
                        <div class="pagination-container">
                            {{ $users->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function editUser(id, name, email, role, unitKerja) {
            document.getElementById('formTitle').innerText = 'Edit Pengguna';
            document.getElementById('userForm').action = '/user/' + id; 
            document.getElementById('methodInput').value = 'PUT'; 
            
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('role').value = role;
            document.getElementById('unit_kerja').value = unitKerja;
            
            document.getElementById('password').required = false;
            document.getElementById('passwordAsterisk').style.display = 'none';
            document.getElementById('passwordHelp').style.display = 'block';
            
            document.getElementById('btnSubmit').innerText = 'Simpan Perubahan';
            document.getElementById('btnSubmit').style.backgroundColor = '#f59e0b';
            document.getElementById('btnCancel').style.display = 'block';
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('formTitle').innerText = 'Registrasi Akun Baru';
            document.getElementById('userForm').action = '{{ route('user.store') }}';
            document.getElementById('methodInput').value = 'POST';
            
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('role').value = '';
            document.getElementById('unit_kerja').value = '';
            document.getElementById('password').value = '';
            
            document.getElementById('password').required = true;
            document.getElementById('passwordAsterisk').style.display = 'inline';
            document.getElementById('passwordHelp').style.display = 'none';
            
            document.getElementById('btnSubmit').innerText = 'Daftarkan';
            document.getElementById('btnSubmit').style.backgroundColor = 'var(--primary)';
            document.getElementById('btnCancel').style.display = 'none';
        }
    </script>
</body>
</html>