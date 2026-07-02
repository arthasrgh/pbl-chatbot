<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // LIST RIWAYAT CHAT
    public function index()
    {
        $chats = DB::table('messages as m1')
            ->select(
                'm1.nomor',
                'm1.pesan as last_message',
                'm1.sender as last_sender',
                'm1.created_at as last_time'
            )
            ->whereRaw('m1.id = (
                SELECT m2.id
                FROM messages m2
                WHERE m2.nomor = m1.nomor
                ORDER BY m2.created_at DESC, m2.id DESC
                LIMIT 1
            )')
            ->orderBy('m1.created_at', 'desc')
            ->get();

        return response()->json($chats);
    }

public function recent()
{
    $recent = DB::table('messages as m1')
        ->select(
            'm1.nomor',
            'm1.pesan',
            'm1.sender',
            'm1.created_at'
        )
        ->whereRaw('m1.id = (
            SELECT m2.id
            FROM messages m2
            WHERE m2.nomor = m1.nomor
            ORDER BY m2.created_at DESC, m2.id DESC
            LIMIT 1
        )')
        ->orderBy('m1.created_at', 'desc')
        ->limit(8)
        ->get();

    return response()->json($recent);
}

    // DETAIL CHAT BERDASARKAN NOMOR
    public function show($nomor)
    {
        return DB::table('messages')
            ->where('nomor', $nomor)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // SIMPAN CHAT
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