<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::latest()->get();

        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);

        Faq::create([
            'keyword' => $request->keyword,
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect('/admin/faqs')
            ->with('success', 'FAQ berhasil ditambahkan');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);

        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $faq->update([
            'keyword' => $request->keyword,
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect('/admin/faqs')
            ->with('success', 'FAQ berhasil diupdate');
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();

        return redirect('/admin/faqs')
            ->with('success', 'FAQ berhasil dihapus');
    }
}