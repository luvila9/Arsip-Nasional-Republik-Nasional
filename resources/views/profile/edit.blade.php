<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Portal Bukti Dukung</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        div:where(.swal2-container) { font-family: 'Inter', sans-serif; }
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
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .btn-logout { background: transparent; border: 1px solid #ef4444; color: #ef4444; padding: 6px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;}
        .btn-logout:hover { background: #ef4444; color: white; }

        .content { padding: 25px; }
        
        .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; align-items: start; }
        
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; border: 1px solid var(--border); }
        .card-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .card-header h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 5px; }
        .card-header p { font-size: 0.85rem; color: var(--text-muted); }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #475569; }
        .form-control { width: 100%; padding: 12px 15px; font-size: 0.95rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        
        .form-control.locked { background-color: #f1f5f9; border-color: #e2e8f0; color: #64748b; cursor: not-allowed; }
        .form-control.locked:focus { box-shadow: none; border-color: #e2e8f0; }

        .error-text { color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; }
        
        .btn { padding: 12px 20px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; color: white; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background-color: var(--primary); }
        .btn-primary:hover { background-color: #1d4ed8; }
        
        @media (max-width: 1024px) { .settings-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .mobile-toggle { display: block; } .user-info { display: none; } .content { padding: 15px; } }
    </style>
</head>
<body>
    @include('components.sidebar')    
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
                <div class="breadcrumb">Pengaturan Akun</div>
            </div>
            <div class="header-actions">
                <span class="user-info">Halo, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Keluar</span></button>
                </form>
            </div>
        </header>
        <div class="content">
            @if (session('status') === 'profile-updated')
                <script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Informasi profil Anda berhasil diperbarui.', confirmButtonColor: '#2563eb' });</script>
            @endif
            @if (session('status') === 'password-updated')
                <script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Kata sandi Anda berhasil diperbarui.', confirmButtonColor: '#2563eb' });</script>
            @endif
            <div class="settings-grid">                
                <div class="card">
                    <div class="card-header">
                        <h3>Informasi Profil</h3>
                        <p>Perbarui identitas dan alamat email Anda. Pastikan email tetap aktif.</p>
                    </div>                    
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>                        
                        <div class="form-group">
                            <label>Hak Akses (Role)</label>
                            <input type="text" class="form-control locked" value="{{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}" readonly title="Hak akses hanya bisa diubah melalui menu Manajemen User">
                        </div>
                        <div style="margin-top: 25px;">
                            <button type="submit" class="btn btn-primary"> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Keamanan Akun</h3>
                        <p>Ubah kata sandi bawaan sistem demi keamanan akses data Anda.</p>
                    </div>                    
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <label for="update_password_current_password">Kata Sandi Saat Ini</label>
                            <input type="password" id="update_password_current_password" name="current_password" class="form-control" placeholder="Masukkan sandi lama..." required>
                            @error('current_password', 'updatePassword') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="update_password_password">Kata Sandi Baru</label>
                            <input type="password" id="update_password_password" name="password" class="form-control" placeholder="Minimal 8 karakter acak..." required>
                            @error('password', 'updatePassword') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="update_password_password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang sandi baru..." required>
                            @error('password_confirmation', 'updatePassword') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div style="margin-top: 30px;">
                            <button type="submit" class="btn btn-primary" style="background-color: #0f172a;"> Perbarui Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>