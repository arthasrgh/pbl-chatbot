<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return response()->json(
            Faq::all()
        );
    }

    public function search($keyword)
    {
        $faq = Faq::where(
            'keyword',
            'like',
            '%' . $keyword . '%'
        )->first();

        if (!$faq) {
            return response()->json([
                'found' => false
            ]);
        }

        return response()->json([
            'found' => true,
            'data' => $faq
        ]);
    }
}