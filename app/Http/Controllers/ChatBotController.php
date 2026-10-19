<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BotFaq;
use App\Models\BotHistory;
use App\Models\BuktiDukung;
use App\Models\SubSubAspek;
use App\Models\SubAspek;
use App\Models\Aspek;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $query = strip_tags($request->input('message'));
        $ip = $request->ip();
        $queryLowerCase = strtolower(trim($query));
        $jawaban = "";

        if (preg_match('/\[ASPEK_(\d+)\]/', $query, $matches)) {
            $aspekId = $matches[1];
            $cekAspek = Aspek::find($aspekId);
            
            if ($cekAspek) {
                $subAspeks = SubAspek::where('aspek_id', $cekAspek->id)->get(); 
                if ($subAspeks->count() > 0) {
                    $jawaban = "Di dalam <strong>{$cekAspek->nama_aspek}</strong>, terdapat sub-aspek berikut:<br><br>";
                    $jawaban .= "<div style='display: flex; flex-direction: column; gap: 8px;'>";
                    foreach ($subAspeks as $index => $sub) {
                        $no = $index + 1;
                        $jawaban .= "<button @click=\"send('[SUB_{$sub->id}]')\" style='text-align: left; background: white; border: 1px solid #cbd5e1; color: #1e293b; padding: 10px 12px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: 0.2s;' onmouseover=\"this.style.backgroundColor='#f1f5f9'\" onmouseout=\"this.style.backgroundColor='white'\"><strong>{$no}.</strong> {$sub->nama_sub_aspek}</button>";
                    }
                    $jawaban .= "</div>";
                } else {
                    $jawaban = "Saat ini belum ada data sub-aspek yang dimasukkan untuk <strong>{$cekAspek->nama_aspek}</strong>.";
                }
            } else {
                $jawaban = "Data aspek tidak ditemukan.";
            }
        } 
        elseif (preg_match('/\[SUB_(\d+)\]/', $query, $matches)) {
            $subAspekId = $matches[1];
            $cekSubAspek = SubAspek::find($subAspekId);

            if ($cekSubAspek) {
                $subSubAspeks = SubSubAspek::where('sub_aspek_id', $cekSubAspek->id)->where('is_active', true)->get();
                if ($subSubAspeks->count() > 0) {
                    $jawaban = "Berikut adalah daftar kriteria / topik dari <strong>{$cekSubAspek->nama_sub_aspek}</strong>:<br><br>";
                    foreach ($subSubAspeks as $subsub) {
                        $tombolAksi = "";
                        if (!empty($subsub->link)) {
                            $urlTautan = url("/track-link/subsub/{$subsub->id}"); 
                            $tombolAksi = "<a href='{$urlTautan}' target='_blank' style='display: inline-block; background-color: #b45309; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; margin-top: 12px;'>Unduh Dokumen</a>";
                        } else {
                            $tombolAksi = "<div style='font-size: 0.8rem; color: #64748b; font-style: italic; margin-top: 12px;'>Tutup chat untuk melihat detail ini di layar utama.</div>";
                        }
                        $jawaban .= "
                        <div style='border: 1px solid #e2e8f0; background-color: #ffffff; padding: 15px; margin-bottom: 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);'>
                            <span style='font-size: 0.65rem; background-color: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; display: inline-block;'>TOPIK ASKE</span>
                            <strong style='color: #0f172a; font-size: 0.95rem; display: block; line-height: 1.4;'>{$subsub->nama_sub_sub_aspek}</strong>
                            {$tombolAksi}
                        </div>";
                    }
                } else {
                    $jawaban = "Saat ini belum ada dokumen aktif di dalam <strong>{$cekSubAspek->nama_sub_aspek}</strong>.";
                }
            } else {
                $jawaban = "Data sub-aspek tidak ditemukan.";
            }
        } 
        
        else {
            $pencarianFaq = trim(str_replace(['?', '.', ','], '', $queryLowerCase));
            $faq = BotFaq::whereRaw('LOWER(pertanyaan) LIKE ?', ["%{$pencarianFaq}%"])
                         ->where('pertanyaan', '!=', 'default_fallback_response')
                         ->first();
            
            $cleanQuery = trim(str_ireplace(['aspek', 'sub'], '', $queryLowerCase));
            $cekAspek = null;
            $cekSubAspek = null;
            
            if (strlen($cleanQuery) >= 3) {
                $cekAspek = Aspek::where('nama_aspek', 'LIKE', "%{$cleanQuery}%")->first();
                $cekSubAspek = SubAspek::where('nama_sub_aspek', 'LIKE', "%{$cleanQuery}%")->first();
            }

            if ($faq) {
                $jawaban = nl2br($faq->jawaban);
            } 
            elseif (str_contains($queryLowerCase, 'aspek') && str_contains($queryLowerCase, 'apa')) {
                $aspeks = Aspek::all();
                $jawaban = "Terdapat <strong>{$aspeks->count()} Aspek Utama</strong> di portal ini. Silakan klik salah satu:<br><br>";
                $jawaban .= "<div style='display: flex; flex-direction: column; gap: 8px;'>";
                foreach ($aspeks as $index => $a) {
                    $no = $index + 1;
                    $jawaban .= "<button @click=\"send('[ASPEK_{$a->id}]')\" style='text-align: left; background: white; border: 1px solid #cbd5e1; color: #1e293b; padding: 10px 12px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: 0.2s;' onmouseover=\"this.style.backgroundColor='#f1f5f9'\" onmouseout=\"this.style.backgroundColor='white'\"><strong>{$no}.</strong> {$a->nama_aspek}</button>";
                }
                $jawaban .= "</div>";
            }
            elseif ($cekAspek) {
                $subAspeks = SubAspek::where('aspek_id', $cekAspek->id)->get(); 
                if ($subAspeks->count() > 0) {
                    $jawaban = "Di dalam <strong>{$cekAspek->nama_aspek}</strong>, terdapat sub-aspek berikut. Silakan klik salah satu:<br><br>";
                    $jawaban .= "<div style='display: flex; flex-direction: column; gap: 8px;'>";
                    foreach ($subAspeks as $index => $sub) {
                        $no = $index + 1;
                        $jawaban .= "<button @click=\"send('[SUB_{$sub->id}]')\" style='text-align: left; background: white; border: 1px solid #cbd5e1; color: #1e293b; padding: 10px 12px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: 0.2s;' onmouseover=\"this.style.backgroundColor='#f1f5f9'\" onmouseout=\"this.style.backgroundColor='white'\"><strong>{$no}.</strong> {$sub->nama_sub_aspek}</button>";
                    }
                    $jawaban .= "</div>";
                } else {
                    $jawaban = "Saat ini belum ada data sub-aspek yang dimasukkan untuk <strong>{$cekAspek->nama_aspek}</strong>.";
                }
            }
            elseif ($cekSubAspek) {
                $subSubAspeks = SubSubAspek::where('sub_aspek_id', $cekSubAspek->id)->where('is_active', true)->get();
                if ($subSubAspeks->count() > 0) {
                    $jawaban = "Berikut adalah daftar kriteria / topik dari <strong>{$cekSubAspek->nama_sub_aspek}</strong>:<br><br>";
                    foreach ($subSubAspeks as $subsub) {
                        $tombolAksi = "";
                        if (!empty($subsub->link)) {
                            $urlTautan = url("/track-link/subsub/{$subsub->id}"); 
                            $tombolAksi = "<a href='{$urlTautan}' target='_blank' style='display: inline-block; background-color: #b45309; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; margin-top: 12px;'>Unduh Dokumen</a>";
                        } else {
                            $tombolAksi = "<div style='font-size: 0.8rem; color: #64748b; font-style: italic; margin-top: 12px;'>Tutup chat untuk melihat detail ini di layar utama.</div>";
                        }
                        $jawaban .= "
                        <div style='border: 1px solid #e2e8f0; background-color: #ffffff; padding: 15px; margin-bottom: 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);'>
                            <span style='font-size: 0.65rem; background-color: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; display: inline-block;'>TOPIK ASKE</span>
                            <strong style='color: #0f172a; font-size: 0.95rem; display: block; line-height: 1.4;'>{$subsub->nama_sub_sub_aspek}</strong>
                            {$tombolAksi}
                        </div>";
                    }
                } else {
                    $jawaban = "Saat ini belum ada dokumen aktif di dalam <strong>{$cekSubAspek->nama_sub_aspek}</strong>.";
                }
            }
            elseif (str_contains($queryLowerCase, 'unduh') || str_contains($queryLowerCase, 'download') || str_contains($queryLowerCase, 'cara')) {
                $jawaban = "Klik tombol <strong>Unduh Dokumen</strong> di samping dokumen pada daftar utama hasil pencarian.<br><br>Atau Anda bisa langsung mengetikkan nama dokumen di kotak chat ini, nanti saya akan tampilkan tombol unduhnya langsung di sini.";
            }
            else {
                $stopwords = ['apa', 'itu', 'bagaimana', 'cara', 'yang', 'dan', 'di', 'ke', 'dari', 'tentang', 'dokumen', 'aspek', 'topik', 'buatkan', 'carikan'];
                $words = explode(' ', $queryLowerCase);
                $keywords = array_diff($words, $stopwords);

                $queryBukti = BuktiDukung::query();
                $queryTopik = SubSubAspek::where('is_active', true)->whereNotNull('link')->where('link', '!=', '');

                foreach ($keywords as $word) {
                    if (strlen($word) > 2) { 
                        $queryBukti->where('nama_dokumen', 'LIKE', "%{$word}%");
                        $queryTopik->where('nama_sub_sub_aspek', 'LIKE', "%{$word}%");
                    }
                }

                $hasilBukti = $queryBukti->take(3)->get();
                $hasilTopik = $queryTopik->take(3)->get();
                $totalDitemukan = $hasilBukti->count() + $hasilTopik->count();

                if ($totalDitemukan > 0) {
                    $jawaban = "Saya menemukan <strong>{$totalDitemukan} data</strong> yang relevan dengan pertanyaan Anda:<br><br>";

                    foreach ($hasilBukti as $doc) {
                        $urlDownload = asset('storage/' . $doc->file_path); 
                        $jawaban .= "
                        <div style='border: 1px solid #e2e8f0; background-color: #ffffff; padding: 15px; margin-bottom: 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);'>
                            <span style='font-size: 0.65rem; background-color: #dbeafe; color: #1d4ed8; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; display: inline-block;'>DOKUMEN FISIK</span>
                            <strong style='color: #0f172a; font-size: 0.95rem; display: block; line-height: 1.4;'>{$doc->nama_dokumen}</strong>
                            <a href='{$urlDownload}' target='_blank' style='display: inline-block; background-color: #2563eb; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; margin-top: 12px;'>Unduh Dokumen</a>
                        </div>";
                    }

                    foreach ($hasilTopik as $topik) {
                        $urlTautan = url("/track-link/subsub/{$topik->id}"); 
                        $jawaban .= "
                        <div style='border: 1px solid #e2e8f0; background-color: #ffffff; padding: 15px; margin-bottom: 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);'>
                            <span style='font-size: 0.65rem; background-color: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; display: inline-block;'>TOPIK ASKE</span>
                            <strong style='color: #0f172a; font-size: 0.95rem; display: block; line-height: 1.4;'>{$topik->nama_sub_sub_aspek}</strong>
                            <a href='{$urlTautan}' target='_blank' style='display: inline-block; background-color: #b45309; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; margin-top: 12px;'>Unduh Dokumen</a>
                        </div>";
                    }
                    $jawaban .= "<div style='font-size: 0.75rem; color: #64748b; margin-top: 10px; font-style: italic;'>Gunakan kata kunci spesifik jika data yang dicari belum muncul.</div>";
                    
                } else {
                    $defaultFaq = BotFaq::where('pertanyaan', 'LIKE', '%default_fallback_response%')->first();
                    $teksAdmin = $defaultFaq ? nl2br($defaultFaq->jawaban) : "Pertanyaan Anda telah otomatis dicatat ke dalam sistem dan akan diteruskan kepada Admin ANRI. Silakan periksa kembali beberapa saat lagi.";
                    $prefixSistem = "Maaf, saya tidak dapat menemukan dokumen atau jawaban yang cocok dengan kata kunci <strong>'{$query}'</strong>.<br><br>";
                    
                    $jawaban = $prefixSistem . $teksAdmin;
                }
            }
        }

        $cleanHistoryQuery = $query;
        if (preg_match('/\[ASPEK_(\d+)\]/', $query)) { $cleanHistoryQuery = "Memilih Aspek Utama"; }
        elseif (preg_match('/\[SUB_(\d+)\]/', $query)) { $cleanHistoryQuery = "Memilih Sub-Aspek"; }

        BotHistory::create([
            'pertanyaan' => $cleanHistoryQuery,
            'jawaban_bot' => $jawaban,
            'ip_address' => $ip
        ]);

        return response()->json(['reply' => $jawaban]);
    }
}