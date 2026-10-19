<?php

namespace App\Http\Controllers;

use App\Models\SubAspek;
use App\Models\SubSubAspek;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SubSubAspekController extends Controller
{
    public function index(SubAspek $subAspek)
    {
        $subAspek->load('aspek'); 
        $subSubAspeks = $subAspek->subSubAspeks()->paginate(10);
        
        return view('aspek.subsub', compact('subAspek', 'subSubAspeks'));
    }

    public function store(Request $request, SubAspek $subAspek)
    {
        $validated = $request->validate([
            'nama_sub_sub_aspek' => 'required|string',
            'link' => 'nullable|url|max:255' 
        ], [
            'nama_sub_sub_aspek.required' => 'Nama Sub-sub Aspek wajib diisi.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $nama_bersih = strip_tags($validated['nama_sub_sub_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $subSubAspek = SubSubAspek::create([
            'sub_aspek_id' => $subAspek->id,
            'nama_sub_sub_aspek' => $nama_bersih,
            'link' => $link_bersih 
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah',
            'description' => 'Menambahkan Sub-sub Aspek (Level 3): ' . $subSubAspek->nama_sub_sub_aspek
        ]);
        
        return redirect()->back()->with('success', 'Sub-sub Aspek berhasil ditambahkan!');
    }

    public function update(Request $request, SubSubAspek $subSubAspek)
    {
        $validated = $request->validate([
            'nama_sub_sub_aspek' => 'required|string',
            'link' => 'nullable|url|max:255'
        ], [
            'nama_sub_sub_aspek.required' => 'Nama Sub-sub Aspek wajib diisi.',
            'link.url' => 'Format link tidak valid. Pastikan menggunakan http:// atau https://',
            'link.max' => 'Link terlalu panjang (maksimal 255 karakter).'
        ]);

        $oldName = $subSubAspek->nama_sub_sub_aspek;
        $oldLink = $subSubAspek->link;

        $nama_bersih = strip_tags($validated['nama_sub_sub_aspek']);
        $link_bersih = !empty($validated['link']) ? strip_tags($validated['link']) : null;

        $subSubAspek->fill([
            'nama_sub_sub_aspek' => $nama_bersih,
            'link' => $link_bersih
        ]);

        if ($subSubAspek->isDirty()) {
            $perubahan = [];
            
            if ($subSubAspek->isDirty('nama_sub_sub_aspek')) {
                $perubahan[] = "Nama Sub-sub Aspek (dari '{$oldName}' menjadi '{$subSubAspek->nama_sub_sub_aspek}')";
            }
            if ($subSubAspek->isDirty('link')) {
                $teksLinkLama = $oldLink ? $oldLink : 'Kosong';
                $teksLinkBaru = $subSubAspek->link ? $subSubAspek->link : 'Kosong';
                $perubahan[] = "Link Publik (dari '{$teksLinkLama}' menjadi '{$teksLinkBaru}')";
            }

            $subSubAspek->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Update',
                'description' => 'Memperbarui ' . implode(' dan ', $perubahan) . ' pada Sub-sub Aspek (Level 3).'
            ]);
        }

        return redirect()->back()->with('success', 'Data Sub-sub Aspek berhasil diperbarui!');
    }

    public function destroy(SubSubAspek $subSubAspek)
    {
        $nama_dihapus = $subSubAspek->nama_sub_sub_aspek;
        
        $subSubAspek->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus',
            'description' => 'Menghapus Sub-sub Aspek (Level 3): ' . $nama_dihapus
        ]);

        return redirect()->back()->with('success', 'Sub-sub Aspek berhasil dihapus!');
    }

    public function toggleActive(SubSubAspek $subSubAspek)
    {
        $subSubAspek->is_active = !$subSubAspek->is_active;
        $subSubAspek->save();

        $status = $subSubAspek->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update Status',
            'description' => "Status Sub-sub Aspek (Level 3) '{$subSubAspek->nama_sub_sub_aspek}' berhasil {$status}."
        ]);

        return redirect()->back()->with('success', "Sub-sub Aspek berhasil {$status}!");
    }
}