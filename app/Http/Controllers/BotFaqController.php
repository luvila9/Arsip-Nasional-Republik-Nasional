<?php

namespace App\Http\Controllers;

use App\Models\BotFaq;
use App\Models\BotHistory; 
use Illuminate\Http\Request;

class BotFaqController extends Controller
{
    public function index()
    {
        $faqs = BotFaq::where('pertanyaan', '!=', 'default_fallback_response')->latest()->paginate(10);
        
        $defaultFaq = BotFaq::where('pertanyaan', 'default_fallback_response')->first();

        $unansweredQueries = BotHistory::where('jawaban_bot', 'LIKE', '%<!--FALLBACK-->%')
                                        ->orWhere('jawaban_bot', 'LIKE', '%Maaf, saya tidak dapat menemukan%')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();

        return view('bot.index', compact('faqs', 'unansweredQueries', 'defaultFaq'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'history_id' => 'nullable|integer'
        ]);

        if ($validated['pertanyaan'] === 'default_fallback_response') {
            BotFaq::updateOrCreate(
                ['pertanyaan' => 'default_fallback_response'],
                ['jawaban' => strip_tags($validated['jawaban'], '<b><i><strong><em><a><br><p>')]
            );
            return redirect()->back()->with('success', 'Pesan template default bot berhasil diperbarui!');
        }

        BotFaq::create([
            'pertanyaan' => strip_tags($validated['pertanyaan']),
            'jawaban' => strip_tags($validated['jawaban'], '<b><i><strong><em><a><br><p>') 
        ]);

        if ($request->filled('history_id')) {
            BotHistory::find($request->history_id)?->delete();
        }

        return redirect()->back()->with('success', 'Pertanyaan Bot berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string'
        ]);

        BotFaq::findOrFail($id)->update([
            'pertanyaan' => strip_tags($validated['pertanyaan']),
            'jawaban' => strip_tags($validated['jawaban'], '<b><i><strong><em><a><br><p>')
        ]);

        return redirect()->back()->with('success', 'Pertanyaan Bot berhasil diperbarui!');
    }

    public function destroy($id)
    {
        BotFaq::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pertanyaan Bot berhasil dihapus!');
    }

    public function destroyHistory($id)
    {
        BotHistory::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Riwayat pertanyaan berhasil diabaikan/dihapus dari daftar!');
    }
}