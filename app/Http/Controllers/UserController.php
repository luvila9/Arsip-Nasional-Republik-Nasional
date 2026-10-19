<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('unit_kerja', 'like', '%' . $search . '%');
        }

        $users = $query->paginate(10);
        
        return view('manajemen-user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->unit_kerja = $request->unit_kerja;
        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Tambah',
            'description' => 'Mendaftarkan akun pengguna baru: ' . $user->name
        ]);

        return back()->with('success', 'Pengguna baru berhasil didaftarkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->unit_kerja = $request->unit_kerja;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Update',
            'description' => 'Memperbarui data akun pengguna: ' . $user->name
        ]);
        
        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $nama_user = $user->name; 
        
        $user->delete();
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Hapus',
            'description' => 'Menghapus akun pengguna: ' . $nama_user
        ]);
        
        return back()->with('success', 'Data pengguna berhasil dihapus!');
    }
}