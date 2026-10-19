<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AspekController extends Controller
{
    public function index()
    {
        $aspeks = Aspek::withCount('subAspeks')->paginate(10);
        return view('aspek.index', compact('aspeks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_aspek' => 'required|string|max:255',
            'link' => 'nullable|url|max:255'
        ], [
            'nama_aspek.required' => 'Nama Aspek Utama wajib diisi.',
            'nama_aspek.max' => 'Nama Aspek maksimal 255 karakter.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $nama_aspek_bersih = strip_tags($validated['nama_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $aspek = Aspek::create([
            'nama_aspek' => $nama_aspek_bersih,
            'link' => $link_bersih
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah',
            'description' => 'Menambahkan Aspek Utama (Level 1): ' . $aspek->nama_aspek
        ]);

        return redirect()->back()->with('success', 'Aspek berhasil ditambahkan!');
    }

    public function update(Request $request, Aspek $aspek)
    {
        $validated = $request->validate([
            'nama_aspek' => 'required|string|max:255',
            'link' => 'nullable|url|max:255'
        ], [
            'nama_aspek.required' => 'Nama Aspek Utama wajib diisi.',
            'nama_aspek.max' => 'Nama Aspek maksimal 255 karakter.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $oldName = $aspek->nama_aspek;
        $oldLink = $aspek->link;

        $nama_aspek_bersih = strip_tags($validated['nama_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $aspek->fill([
            'nama_aspek' => $nama_aspek_bersih,
            'link' => $link_bersih
        ]);

        if ($aspek->isDirty()) {
            $perubahan = [];
            
            if ($aspek->isDirty('nama_aspek')) {
                $perubahan[] = "Nama Aspek (dari '{$oldName}' menjadi '{$aspek->nama_aspek}')";
            }
            if ($aspek->isDirty('link')) {
                $teksLinkLama = $oldLink ? $oldLink : 'Kosong';
                $teksLinkBaru = $aspek->link ? $aspek->link : 'Kosong';
                $perubahan[] = "Link Publik (dari '{$teksLinkLama}' menjadi '{$teksLinkBaru}')";
            }
            $aspek->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Update',
                'description' => 'Memperbarui ' . implode(' dan ', $perubahan) . ' pada Aspek Utama (Level 1).'
            ]);
        }

        return redirect()->back()->with('success', 'Data Aspek berhasil diperbarui!');
    }

    public function destroy(Aspek $aspek)
    {
        $nama_dihapus = $aspek->nama_aspek;
        
        $aspek->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus',
            'description' => 'Menghapus Aspek Utama (Level 1): ' . $nama_dihapus
        ]);

        return redirect()->back()->with('success', 'Aspek berhasil dihapus!');
    }
}