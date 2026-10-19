<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\AspekController;
use App\Http\Controllers\SubAspekController;
use App\Http\Controllers\SubSubAspekController;
use App\Http\Controllers\BuktiDukungController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\PasswordController;

Route::get('/', function (Request $request) {
    $lastUpdated = \Carbon\Carbon::now()->translatedFormat('d F Y');    
    $ip = $request->ip();
    $date = \Carbon\Carbon::now()->format('Y-m-d');
    
    \App\Models\Visitor::firstOrCreate([
        'ip_address' => $ip,
        'visit_date' => $date
    ]);
    $aspeks = \App\Models\Aspek::with(['subAspeks' => function ($query) {
        $query->where('is_active', true)->with(['subSubAspeks' => function ($q) {
            $q->where('is_active', true);
        }]);
    }])->get();

    $aspekList = [];
    $nomorAspek = 1;

    foreach ($aspeks as $aspek) {
        $topikList = [];
        $nomorTopik = 1;

        foreach ($aspek->subAspeks as $sub) {
            $dokumenList = [];
            
            foreach ($sub->subSubAspeks as $subsub) {
                $dokumenList[] = [
                    'nama' => $subsub->nama_sub_sub_aspek,
                    'url' => !empty($subsub->link) ? route('track.link', ['level' => 'subsub', 'id' => $subsub->id]) : null
                ];
            }
            $topikList[] = [
                'kode' => $nomorTopik . '.', 
                'judul' => $sub->nama_sub_aspek,
                'dokumen' => $dokumenList
            ];
            $nomorTopik++;
        }
        $aspekList[] = [
            'id' => 'aspek-' . $aspek->id,
            'kode' => $nomorAspek, 
            'judul' => $aspek->nama_aspek,
            'topik' => $topikList
        ];
        $nomorAspek++;
    }
    
    return view('welcome', compact('lastUpdated', 'aspekList'));
});

Route::get('/go/{level}/{id}', [DashboardController::class, 'trackLink'])->name('track.link');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bukti-dukung', [BuktiDukungController::class, 'index'])->name('buktidukung.index');
    Route::post('/bukti-dukung', [BuktiDukungController::class, 'store'])->name('buktidukung.store');
    Route::delete('/bukti-dukung/{buktiDukung}', [BuktiDukungController::class, 'destroy'])->name('buktidukung.destroy');
    Route::get('/get-sub-aspeks/{aspek_id}', [BuktiDukungController::class, 'getSubAspeks']);
    Route::get('/get-sub-sub-aspeks/{sub_aspek_id}', [BuktiDukungController::class, 'getSubSubAspeks']);
});

Route::middleware(['auth', 'verified', 'role:admin,super_admin'])->group(function () {
    
    Route::get('/master-aspek', [AspekController::class, 'index'])->name('aspek.index');
    Route::post('/master-aspek', [AspekController::class, 'store'])->name('aspek.store');
    Route::put('/master-aspek/{aspek}', [AspekController::class, 'update'])->name('aspek.update');
    Route::delete('/master-aspek/{aspek}', [AspekController::class, 'destroy'])->name('aspek.destroy');
    Route::get('/master-aspek/{aspek}/sub', [SubAspekController::class, 'index'])->name('subaspek.index');
    Route::post('/master-aspek/{aspek}/sub', [SubAspekController::class, 'store'])->name('subaspek.store');
    Route::put('/sub-aspek/{subAspek}', [SubAspekController::class, 'update'])->name('subaspek.update');
    Route::delete('/sub-aspek/{subAspek}', [SubAspekController::class, 'destroy'])->name('subaspek.destroy');
    Route::post('/sub-aspek/{subAspek}/toggle', [SubAspekController::class, 'toggleActive'])->name('subaspek.toggle');
    Route::get('/sub-aspek/{subAspek}/subsub', [SubSubAspekController::class, 'index'])->name('subsubaspek.index');
    Route::post('/sub-aspek/{subAspek}/subsub', [SubSubAspekController::class, 'store'])->name('subsubaspek.store');
    Route::put('/sub-sub-aspek/{subSubAspek}', [SubSubAspekController::class, 'update'])->name('subsubaspek.update');
    Route::delete('/sub-sub-aspek/{subSubAspek}', [SubSubAspekController::class, 'destroy'])->name('subsubaspek.destroy');
    Route::post('/sub-sub-aspek/{subSubAspek}/toggle', [SubSubAspekController::class, 'toggleActive'])->name('subsubaspek.toggle');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/manajemen-user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/store', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => $request->role,
            'unit_kerja' => $request->unit_kerja,
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil didaftarkan!');
    })->name('user.store');
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('log.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});

    Route::resource('bot-faq', \App\Http\Controllers\BotFaqController::class);
    Route::delete('/bot-history/{id}', [\App\Http\Controllers\BotFaqController::class, 'destroyHistory'])->name('bothistory.destroy');
    Route::post('/chat/ask', [\App\Http\Controllers\ChatbotController::class, 'ask']);
    Route::get('/track-link/{level}/{id}', [\App\Http\Controllers\DashboardController::class, 'trackLink'])->name('track.link');

require __DIR__.'/auth.php';