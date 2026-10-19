<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = \App\Models\ActivityLog::with('user'); 

        if ($search) {
            $keyword = strtolower($search);

            $actionKeywords = [$search]; 

            if (str_contains($keyword, 'tambah')) {
                $actionKeywords = array_merge($actionKeywords, ['create', 'store', 'tambah']);
            } elseif (str_contains($keyword, 'ubah') || str_contains($keyword, 'edit')) {
                $actionKeywords = array_merge($actionKeywords, ['update', 'edit', 'ubah']);
            } elseif (str_contains($keyword, 'hapus') || str_contains($keyword, 'delete')) {
                $actionKeywords = array_merge($actionKeywords, ['delete', 'destroy', 'hapus']);
            }

            $query->where(function($q) use ($search, $actionKeywords) {
                
                $q->where(function($subQ) use ($actionKeywords) {
                    foreach ($actionKeywords as $ak) {
                        $subQ->orWhere('action', 'LIKE', "%{$ak}%");
                    }
                })
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                });
                
            });
        }

        $logs = $query->latest()->paginate(10)->withQueryString();
        
        return view('log-aktivitas.index', compact('logs'));
    }
}