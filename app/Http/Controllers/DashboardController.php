<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\BuktiDukung;
use App\Models\DownloadLog;
use App\Models\Aspek;
use App\Models\SubAspek;
use App\Models\SubSubAspek;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalDokumen = BuktiDukung::count();
        $totalUser = User::count();
        $users = User::latest()->paginate(10); 
        
        $filterMonth = $request->input('filter_month');
        $filterYear = $request->input('filter_year', date('Y'));

        $hariIni = DownloadLog::whereDate('created_at', date('Y-m-d'))->count();
        $detailHariIni = DownloadLog::whereDate('created_at', date('Y-m-d'))->latest()->get();

        if ($filterMonth) {
            $bulanIni = DownloadLog::whereMonth('created_at', $filterMonth)->whereYear('created_at', $filterYear)->count();
            $labelBulan = "Unduhan (" . Carbon::parse($filterYear . '-' . $filterMonth . '-01')->translatedFormat('M Y') . ")";
            
            $totalKunjungan = \App\Models\Visitor::whereMonth('created_at', $filterMonth)->whereYear('created_at', $filterYear)->count();
            $labelKunjungan = "Kunjungan (" . Carbon::parse($filterYear . '-' . $filterMonth . '-01')->translatedFormat('M Y') . ")";
        } else {
            $bulanIni = DownloadLog::whereYear('created_at', $filterYear)->count();
            $labelBulan = "Unduhan ($filterYear)";
            
            $totalKunjungan = \App\Models\Visitor::whereYear('created_at', $filterYear)->count();
            $labelKunjungan = "Kunjungan ($filterYear)";
        }

        $totalDataAspek = Aspek::count() + SubAspek::count() + SubSubAspek::count();

        $chartLabels = [];
        $chartData = [];
        
        if ($filterMonth) {
            $daysInMonth = Carbon::parse($filterYear . '-' . $filterMonth . '-01')->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartLabels[] = $i;
                $chartData[] = DownloadLog::whereYear('created_at', $filterYear)
                                          ->whereMonth('created_at', $filterMonth)
                                          ->whereDay('created_at', $i)
                                          ->count();
            }
            $chartTitle = "Statistik Unduhan Harian (" . Carbon::parse($filterYear . '-' . $filterMonth . '-01')->translatedFormat('F Y') . ")";
            $detailFilterData = DownloadLog::whereYear('created_at', $filterYear)
                                           ->whereMonth('created_at', $filterMonth)
                                           ->latest()->get();
        } else {
            $months = ["JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGS", "SEP", "OKT", "NOV", "DES"];
            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = $months[$i - 1];
                $chartData[] = DownloadLog::whereYear('created_at', $filterYear)
                                          ->whereMonth('created_at', $i)
                                          ->count();
            }
            $chartTitle = "Statistik Unduhan Tahunan ($filterYear)";
            $detailFilterData = DownloadLog::whereYear('created_at', $filterYear)->latest()->get();
        }

        $detailBulanIni = $detailFilterData; 

        $getNamaDokumen = function($log) {
            if ($log->level == 'aspek') {
                $item = Aspek::find($log->item_id);
                return $item ? $item->nama_aspek : 'Data Aspek telah dihapus';
            } elseif ($log->level == 'sub') {
                $item = SubAspek::find($log->item_id);
                return $item ? $item->nama_sub_aspek : 'Data Sub Aspek telah dihapus';
            } elseif ($log->level == 'subsub') {
                $item = SubSubAspek::find($log->item_id);
                return $item ? $item->nama_sub_sub_aspek : 'Data Sub-sub Aspek telah dihapus';
            }
            return 'Dokumen tidak diketahui';
        };

        foreach ($detailHariIni as $log) { $log->nama_dokumen = $getNamaDokumen($log); }
        foreach ($detailBulanIni as $log) { $log->nama_dokumen = $getNamaDokumen($log); }
        foreach ($detailFilterData as $log) { $log->nama_dokumen = $getNamaDokumen($log); }

        return view('dashboard', compact(
            'totalDokumen', 'totalUser', 'users', 
            'hariIni', 'bulanIni', 'totalKunjungan', 'totalDataAspek', 
            'chartLabels', 'chartData', 'chartTitle', 'detailFilterData', 'filterMonth', 'filterYear',
            'detailHariIni', 'detailBulanIni', 'labelBulan', 'labelKunjungan'
        ));
    }
    
    public function trackLink($level, $id)
    {
        $link = '#';
        if ($level == 'aspek') $link = Aspek::findOrFail($id)->link;
        elseif ($level == 'sub') $link = SubAspek::findOrFail($id)->link;
        elseif ($level == 'subsub') $link = SubSubAspek::findOrFail($id)->link;

        if ($link) {
            DownloadLog::create([
                'level' => $level,
                'item_id' => $id
            ]);
            return redirect()->away($link); 
        }

        return abort(404, 'Tautan Publik belum diisi oleh Admin.');
    }
}