<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Models\SubAspek;
use App\Models\SubSubAspek;
use App\Models\BuktiDukung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuktiDukungController extends Controller
{
    public function index()
    {
        $aspeks = Aspek::all(); 
        
        if (Auth::user()->role === 'user') {
            $buktiDukungs = BuktiDukung::with(['user', 'aspek', 'subAspek', 'subSubAspek'])
                            ->where('user_id', Auth::id())
                            ->latest()
                            ->paginate(10);
        } else {
            $buktiDukungs = BuktiDukung::with(['user', 'aspek', 'subAspek', 'subSubAspek'])
                            ->latest()
                            ->paginate(15);
        }

        return view('bukti-dukung.index', compact('aspeks', 'buktiDukungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aspek_id' => 'required|exists:aspeks,id',
            'sub_aspek_id' => 'nullable|exists:sub_aspeks,id',
            'sub_sub_aspek_id' => 'nullable|exists:sub_sub_aspeks,id',
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|mimes:pdf,xls,xlsx,doc,docx,zip,rar|max:10240',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'aspek_id.required' => 'Aspek Utama wajib dipilih.',
            'aspek_id.exists' => 'Data Aspek tidak valid atau telah dimanipulasi.',
            'nama_dokumen.required' => 'Nama dokumen wajib diisi.',
            'file_dokumen.required' => 'File dokumen wajib diunggah.',
            'file_dokumen.mimes' => 'Format file tidak diizinkan. Gunakan PDF, Word, Excel, ZIP, atau RAR.',
            'file_dokumen.max' => 'Ukuran file tidak boleh lebih dari 10 MB.',
        ]);

        $filePath = $request->file('file_dokumen')->store('dokumen_arsip', 'public');

        BuktiDukung::create([
            'user_id' => Auth::id(),
            'aspek_id' => $validated['aspek_id'],
            'sub_aspek_id' => $validated['sub_aspek_id'] ?? null,
            'sub_sub_aspek_id' => $validated['sub_sub_aspek_id'] ?? null,
            
            'nama_dokumen' => strip_tags($validated['nama_dokumen']), 
            'file_path' => $filePath,
            'keterangan' => strip_tags($validated['keterangan'] ?? ''),
            'status' => 'Menunggu Verifikasi'
        ]);

        return redirect()->back()->with('success', 'Dokumen Bukti Dukung berhasil diunggah!');
    }

    public function destroy(BuktiDukung $buktiDukung)
    {
        if (Auth::user()->role === 'user') {
            if ($buktiDukung->user_id !== Auth::id()) {
                abort(403, 'Akses Ditolak: Ini bukan dokumen milik Anda.');
            }
            if ($buktiDukung->status === 'Disetujui') {
                abort(403, 'Akses Ditolak: Dokumen yang sudah disetujui tidak boleh dihapus.');
            }
        }

        if (Storage::disk('public')->exists($buktiDukung->file_path)) {
            Storage::disk('public')->delete($buktiDukung->file_path);
        }
        
        $buktiDukung->delete();
        return redirect()->back()->with('success', 'Dokumen berhasil dihapus dari sistem!');
    }
    
    public function getSubAspeks($aspek_id)
    {
        $subs = SubAspek::where('aspek_id', $aspek_id)->get();
        return response()->json($subs);
    }

    public function getSubSubAspeks($sub_aspek_id)
    {
        $subsubs = SubSubAspek::where('sub_aspek_id', $sub_aspek_id)->get();
        return response()->json($subsubs);
    }
}