<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aspek; 
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $aspekList = Aspek::with(['topik' => function ($query) {
            $query->where('is_active', true)->with(['pilihan' => function ($q) {
                $q->where('is_active', true);
            }, 'dokumen']);
        }])->get(); 

        $latestUpdate = Aspek::max('updated_at'); 
        if ($latestUpdate) {
            $lastUpdated = Carbon::parse($latestUpdate)->timezone('Asia/Jakarta')->translatedFormat('d F Y');
        } else {
            $lastUpdated = Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('d F Y');
        }

        return view('welcome', compact('aspekList', 'lastUpdated'));
    }
}