<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Chatbot - Portal Bukti Dukung</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .dashboard-grid { display: grid; grid-template-columns: 350px minmax(0, 1fr); gap: 25px; align-items: start; }
        
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 20px; border: 1px solid var(--border); min-width: 0; }
        .card-header h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 12px; font-size: 0.9rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 15px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; flex: 1; color: white; transition: 0.2s;}
        .btn-primary { background-color: var(--primary); }
        .btn-secondary { background-color: #64748b; display: none; } 
        
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 12px 15px; font-size: 0.85rem; color: var(--text-muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px; font-size: 0.9rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .text-wrap { word-break: break-word; white-space: normal; min-width: 200px; line-height: 1.4; }
        .badge-status { font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; }
        
        .action-btns { display: flex; gap: 6px; flex-wrap: nowrap; align-items: center; }
        .btn-sm { height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; text-decoration: none; flex-shrink: 0; }
        .btn-edit { background-color: #f59e0b; width: 32px; }
        .btn-delete { background-color: #ef4444; width: 32px;}
        .btn-answer { background-color: #2563eb; padding: 0 12px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; }

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .dashboard-grid { grid-template-columns: minmax(0, 1fr); width: 100%; gap: 20px; } 
            .user-info { display: none; }
            .content { padding: 15px; }
            .top-header { padding: 15px; }
            .breadcrumb { font-size: 0.85rem; }
            .card { padding: 15px; overflow: hidden; }
            td { padding: 12px 10px; }
        }
        
        .swal2-html-container { overflow: visible !important; }
    </style>
</head>
<body>
    @include('components.sidebar')
    <main class="main-panel">
        <header class="top-header">
            <div class="header-left">
                <button class="mobile-toggle" id="mobileToggle"><i class="fa-solid fa-bars"></i></button>
                <div class="breadcrumb">Chatbot FAQ</div>
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
            @if(session('success'))
                <div style="background-color: #dcfce3; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                    <i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card" style="margin-bottom: 25px;">
                <div class="card-header">
                    <h3> Pesan Default </h3>
                </div>
                <form action="{{ route('bot-faq.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pertanyaan" value="default_fallback_response">
                    
                    <div class="form-group">
                        <label style="color: #64748b;">Teks Baku Sistem (Permanen & Tidak Bisa Dihapus):</label>
                        <div style="background: #f1f5f9; padding: 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.85rem; color: #475569; margin-bottom: 15px;">
                            Maaf, saya tidak dapat menemukan dokumen atau jawaban yang cocok dengan kata kunci <strong>'[Kata yang dicari pengunjung]'</strong>.&lt;br&gt;&lt;br&gt;
                        </div>

                        <label style="color: #0f172a;">Kalimat Lanjutan / Penutup (Silakan Sesuaikan):</label>
                        <textarea name="jawaban" class="form-control" style="min-height: 80px;" required placeholder="Ketik kalimat penutup di sini...">{{ $defaultFaq->jawaban ?? 'Pertanyaan Anda telah otomatis dicatat ke dalam sistem dan akan diteruskan kepada Admin ANRI. Silakan periksa kembali beberapa saat lagi.' }}</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="background-color: #475569; padding: 8px 20px; width: auto;">
                        <i class=></i> Simpan Pesan Default
                    </button>
                </form>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header"><h3 id="formTitle">Tambah Pertanyaan Bot Baru</h3></div>
                    <form id="faqForm" action="{{ route('bot-faq.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="methodInput" value="POST">                        
                        <div class="form-group">
                            <label>Pertanyaan Pemicu (Singkat)</label>
                            <input type="text" name="pertanyaan" id="pertanyaan" class="form-control" placeholder="Contoh: Apa itu Portal Aske?" required value="{{ old('pertanyaan') }}">
                        </div>                        
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Jawaban Bot</label>
                            <textarea name="jawaban" id="jawaban" class="form-control" placeholder="Jawaban panjang yang akan dikirim bot. Anda bisa menggunakan tag <br> untuk enter." required>{{ old('jawaban') }}</textarea>
                            <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 5px;">*Gunakan &lt;br&gt; untuk garis baru, atau &lt;b&gt;teks&lt;/b&gt; untuk tebal.</span>
                        </div>
                        <div class="btn-group">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Simpan Data</button>
                            <button type="button" id="btnCancel" class="btn btn-secondary" onclick="resetForm()">Batal</button>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Daftar Memori Bot Aktif</h3></div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Pertanyaan Pemicu</th>
                                    <th width="50%">Jawaban Bot</th>
                                    <th width="15%" style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqs as $index =>$faq)
                                <tr>
                                    <td>{{ $faqs->firstItem() +$index }}</td>
                                    <td class="text-wrap"><strong style="color: #0f172a;">{{ $faq->pertanyaan }}</strong></td>
                                    <td class="text-wrap" style="color: #64748b; font-size: 0.85rem;">{{ Str::limit(strip_tags($faq->jawaban), 60) }}</td>
                                    <td>
                                        <div class="action-btns" style="justify-content: center;">
                                            <button class="btn-sm btn-edit" onclick="editFaq('{{ $faq->id }}', '{{ addslashes($faq->pertanyaan) }}', '{{ addslashes($faq->jawaban) }}')" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                            <form action="{{ route('bot-faq.destroy', $faq->id) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, this, 'Hapus pertanyaan ini dari ingatan Bot?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-sm btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 20px;">{{ $faqs->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
            
            <div class="card" style="margin-top: 25px;">
                <div class="card-header">
                    <h3><i class="fa-solid fa-clipboard-list" style="margin-right: 8px; color: var(--text-main);"></i> Antrean Pertanyaan Publik</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 400; margin-top: -10px; margin-bottom: 5px;">
                        Daftar ini berfungsi sebagai "To-Do List". Klik <strong>Jawab</strong> untuk merespons, dan pertanyaan tersebut akan otomatis bersih dari antrean ini.
                    </p>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th width="20%">Waktu Pencarian</th>
                                <th width="45%">Kata Kunci / Pertanyaan Publik</th>
                                <th width="15%">Status</th>
                                <th width="20%">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($unansweredQueries as $index =>$history)
                            <tr>
                                <td style="font-size: 0.85rem; color: #64748b; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($history->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="text-wrap">
                                    <strong style="color: #0f172a; font-size: 0.95rem;">"{{ $history->pertanyaan }}"</strong>
                                </td>
                                <td>
                                    <span class="badge-status" style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171;">
                                        Menunggu Jawaban
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button type="button" class="btn-sm btn-answer" onclick="answerWithPopup('{{ $history->id }}', '{{ addslashes($history->pertanyaan) }}')" title="Berikan Jawaban Langsung">
                                            <i class=></i> Jawab
                                        </button>                                        
                                        <form action="{{ route('bothistory.destroy', $history->id) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, this, 'Abaikan / hapus riwayat ini dari antrean?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-sm btn-delete" style="background-color: #64748b;" title="Abaikan / Buang"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">
                                    <i class="fa-solid fa-check-double" style="font-size: 2rem; color: #10b981; margin-bottom: 10px; display: block;"></i>
                                    Hebat! Semua pertanyaan publik sudah terjawab. Antrean bersih.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
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

        function answerWithPopup(historyId, pertanyaan) {
            Swal.fire({
                title: 'Jawab Pertanyaan',
                html: `
                    <div style="text-align: left; margin-bottom: 15px;">
                        <label style="font-size: 0.85rem; font-weight: bold; color: #64748b;">Pertanyaan Publik:</label>
                        <div style="background: #f1f5f9; padding: 12px; border-radius: 6px; font-weight: bold; color: #0f172a; margin-top: 5px;">"${pertanyaan}"</div>
                    </div>
                    <div style="text-align: left;">
                        <label style="font-size: 0.85rem; font-weight: bold; color: #64748b;">Masukkan Jawaban Resmi:</label>
                        <textarea id="swal-jawaban" class="swal2-textarea" style="margin: 5px 0 0 0; width: 100%; height: 120px; font-size: 0.9rem; border-radius: 6px;" placeholder="Ketik jawaban bot di sini... (Gunakan <br> untuk garis baru)"></textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-paper-plane" style="margin-right: 5px;"></i> Simpan Jawaban',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                width: '600px',
                preConfirm: () => {
                    const jawaban = document.getElementById('swal-jawaban').value;
                    if (!jawaban.trim()) {
                        Swal.showValidationMessage('Jawaban tidak boleh kosong!');
                        return false;
                    }
                    return jawaban;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('bot-faq.store') }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const inputPertanyaan = document.createElement('input');
                    inputPertanyaan.type = 'hidden';
                    inputPertanyaan.name = 'pertanyaan';
                    inputPertanyaan.value = pertanyaan;

                    const inputJawaban = document.createElement('input');
                    inputJawaban.type = 'hidden';
                    inputJawaban.name = 'jawaban';
                    inputJawaban.value = result.value;

                    const inputHistoryId = document.createElement('input');
                    inputHistoryId.type = 'hidden';
                    inputHistoryId.name = 'history_id';
                    inputHistoryId.value = historyId;

                    form.appendChild(csrfToken);
                    form.appendChild(inputPertanyaan);
                    form.appendChild(inputJawaban);
                    form.appendChild(inputHistoryId);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function editFaq(id, pertanyaan, jawaban) {
            document.getElementById('formTitle').innerText = 'Edit Pertanyaan Bot';
            document.getElementById('faqForm').action = '/bot-faq/' + id;
            document.getElementById('methodInput').value = 'PUT';
            document.getElementById('pertanyaan').value = pertanyaan;
            document.getElementById('jawaban').value = jawaban; 
            document.getElementById('btnSubmit').innerText = 'Simpan Perubahan';
            document.getElementById('btnSubmit').style.backgroundColor = '#f59e0b';
            document.getElementById('btnCancel').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' }); 
        }
        
        function resetForm() {
            document.getElementById('formTitle').innerText = 'Tambah Pertanyaan Bot Baru';
            document.getElementById('faqForm').action = '{{ route('bot-faq.store') }}';
            document.getElementById('methodInput').value = 'POST';
            document.getElementById('pertanyaan').value = '';
            document.getElementById('jawaban').value = ''; 
            document.getElementById('btnSubmit').innerText = 'Simpan Data';
            document.getElementById('btnSubmit').style.backgroundColor = 'var(--primary)';
            document.getElementById('btnCancel').style.display = 'none';
        }

        function validateKeyword(event, form) {
            const textarea = form.querySelector('#fallbackJawaban');
            if (!textarea.value.includes('{keyword}')) {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    title: 'Tag {keyword} Hilang!',
                    text: 'Anda tidak boleh menghapus teks {keyword} dari kotak tersebut. Tag ini wajib ada untuk menampilkan kata yang dicari pengunjung.',
                    confirmButtonColor: '#475569',
                    confirmButtonText: 'Saya Mengerti'
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html>