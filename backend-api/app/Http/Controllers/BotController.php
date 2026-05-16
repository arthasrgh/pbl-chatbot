<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BotController extends Controller
{
    public function handover(Request $req)
    {
        DB::table('sessions')
            ->where('nomor', $req->nomor)
            ->update([
                'human_mode' => $req->status,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true
        ]);
    }
}