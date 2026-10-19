<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Models\SubAspek;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SubAspekController extends Controller
{
    public function index(Aspek $aspek)
    {
        $subAspeks = $aspek->subAspeks()->withCount('subSubAspeks')->paginate(10);
        return view('aspek.sub', compact('aspek', 'subAspeks'));
    }

    public function store(Request $request, Aspek $aspek)
    {
        $validated = $request->validate([
            'nama_sub_aspek' => 'required|string',
            'link' => 'nullable|url|max:255'
        ], [
            'nama_sub_aspek.required' => 'Nama Sub Aspek wajib diisi.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $nama_bersih = strip_tags($validated['nama_sub_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $subAspek = SubAspek::create([
            'aspek_id' => $aspek->id,
            'nama_sub_aspek' => $nama_bersih,
            'link' => $link_bersih
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah',
            'description' => 'Menambahkan Sub Aspek (Level 2): ' . $subAspek->nama_sub_aspek
        ]);

        return redirect()->back()->with('success', 'Sub Aspek berhasil ditambahkan!');
    }

    public function update(Request $request, SubAspek $subAspek)
    {
        $validated = $request->validate([
            'nama_sub_aspek' => 'required|string',
            'link' => 'nullable|url|max:255'
        ], [
            'nama_sub_aspek.required' => 'Nama Sub Aspek wajib diisi.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $oldName = $subAspek->nama_sub_aspek;
        $oldLink = $subAspek->link;

        $nama_bersih = strip_tags($validated['nama_sub_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $subAspek->fill([
            'nama_sub_aspek' => $nama_bersih,
            'link' => $link_bersih
        ]);

        if ($subAspek->isDirty()) {
            $perubahan = [];
            
            if ($subAspek->isDirty('nama_sub_aspek')) {
                $perubahan[] = "Nama Sub Aspek (dari '{$oldName}' menjadi '{$subAspek->nama_sub_aspek}')";
            }
            if ($subAspek->isDirty('link')) {
                $teksLinkLama = $oldLink ? $oldLink : 'Kosong';
                $teksLinkBaru = $subAspek->link ? $subAspek->link : 'Kosong';
                $perubahan[] = "Link Publik (dari '{$teksLinkLama}' menjadi '{$teksLinkBaru}')";
            }

            $subAspek->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Update',
                'description' => 'Memperbarui ' . implode(' dan ', $perubahan) . ' pada Sub Aspek (Level 2).'
            ]);
        }

        return redirect()->back()->with('success', 'Data Sub Aspek berhasil diperbarui!');
    }

    public function destroy(SubAspek $subAspek)
    {
        $nama_dihapus = $subAspek->nama_sub_aspek;
        
        $subAspek->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus',
            'description' => 'Menghapus Sub Aspek (Level 2): ' . $nama_dihapus
        ]);

        return redirect()->back()->with('success', 'Sub Aspek berhasil dihapus!');
    }
    public function toggleActive(SubAspek $subAspek)
    {
        $subAspek->is_active = !$subAspek->is_active;
        $subAspek->save();

        $status = $subAspek->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Status',
            'description' => "Status Sub Aspek (Level 2) '{$subAspek->nama_sub_aspek}' berhasil {$status}."
        ]);

        return redirect()->back()->with('success', "Sub Aspek berhasil {$status}!");
    }
}