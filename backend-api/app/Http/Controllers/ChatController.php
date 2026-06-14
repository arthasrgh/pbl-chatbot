<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        public function store(Request $request)
    {
        DB::table('messages')->insert([
            'nomor' => $request->nomor,
            'sender' => $request->sender,
            'pesan' => $request->pesan,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}