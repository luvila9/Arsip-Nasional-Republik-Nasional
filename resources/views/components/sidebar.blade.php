<style>
    .sidebar { width: 260px !important; min-width: 260px !important; flex-shrink: 0 !important; background-color: #0095da; color: #f8fafc; display: flex; flex-direction: column; z-index: 1000; transition: transform 0.3s ease; height: 100vh; }
    .sidebar-brand { padding: 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #ffffff; }
    .sidebar-brand img { width: 80px; padding: 4px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .sidebar-brand div { font-weight: 700; font-size: 0.9rem; line-height: 1.2; color: #ffffff;}
    .sidebar-profile { padding: 20px; display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #ffffff; }
    .sidebar-profile img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #2563eb; }
    .sidebar-profile h4 { font-size: 0.95rem; font-weight: 600; margin: 0; color: #ffffff;}
    .sidebar-profile p { font-size: 0.8rem; color: #000; margin: 2px 0 0 0; text-transform: capitalize; }
    
    .nav-menu { padding: 15px 10px; list-style: none; flex-grow: 1; overflow-y: auto; margin: 0; }
    .nav-menu li a { display: flex; align-items: center; gap: 15px; padding: 12px 15px; color: #cbd5e1; text-decoration: none; border-radius: 8px; margin-bottom: 5px; font-size: 0.9rem; font-weight: 500; transition: background-color 0.2s, color 0.2s;}
    .nav-menu li a i { width: 20px; text-align: center; font-size: 1.1rem; }
    .nav-menu li a:hover, .nav-menu li a.active { background-color: #2563eb; color: white; }
    
    .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
    
    @media (max-width: 768px) {
        .sidebar { position: fixed; left: -260px; height: 100%; }
        .sidebar.show { transform: translateX(260px); }
        .sidebar-overlay.show { display: block; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    div:where(.swal2-container) { font-family: 'Inter', sans-serif; }
</style>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo 1.png') }}" alt="Logo ANRI">
        <div>PORTAL BUKTI DUKUNG<br><span style="font-weight:400; font-size:0.75rem;">Arsip Nasional RI</span></div>
    </div>    
    <div class="sidebar-profile">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=2563eb&color=fff" alt="Profile">
        <div>
            <h4>{{ Auth::user()->name ?? 'Administrator' }}</h4>
            <p style="text-transform: capitalize;">{{ str_replace('_', ' ', Auth::user()->role ?? 'admin') }}</p>
        </div>
    </div>
    <ul class="nav-menu">
        <li>
            <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/master-aspek" class="{{ Request::is('*aspek*') ? 'active' : '' }}">
                <i class="fa-solid fa-sitemap"></i> Kelola Aspek & Sub
            </a>
        </li>
        <li>
            <a href="/log-aktivitas" class="{{ Request::is('log-aktivitas*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas
            </a>
        </li>
        <li>
            <a href="{{ route('bot-faq.index') }}" class="{{ Request::is('*bot-faq*') ? 'active' : '' }}">
                <i class="fa-solid fa-robot"></i> Kelola Chatbot
            </a>
        </li>
        <li>
            <a href="{{ route('profile.edit') }}" class="{{ Request::is('profile*') || Request::is('password*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Pengaturan
            </a>
        </li>
    </ul>
</aside>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (mobileToggle && sidebar && overlay) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        const logoutForms = document.querySelectorAll('form[action="{{ route("logout") }}"]');
        
        logoutForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();                 
                Swal.fire({
                    title: 'Akhiri Sesi?',
                    text: "Anda akan keluar dari Portal Bukti Dukung ANRI.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', 
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 5px;"></i> Ya, Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>