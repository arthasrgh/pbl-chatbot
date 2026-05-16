<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function send(Request $req)
    {
        DB::table('messages')->insert([
            'nomor' => $req->nomor,
            'sender' => 'admin',
            'pesan' => $req->pesan,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}