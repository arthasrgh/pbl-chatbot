<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        return DB::table('messages')
            ->select('nomor')
            ->groupBy('nomor')
            ->orderByRaw('MAX(created_at) DESC')
            ->get();
    }

    public function show($nomor)
    {
        return DB::table('messages')
            ->where('nomor', $nomor)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}