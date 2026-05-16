<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $totalUsers = DB::table('users')->count();

        $totalChat = DB::table('messages')->count();

        $hotLeads = DB::table('messages')
            ->where('pesan', 'like', '%daftar%')
            ->orWhere('pesan', 'like', '%mitra%')
            ->orWhere('pesan', 'like', '%gabung%')
            ->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_chat' => $totalChat,
            'hot_leads' => $hotLeads
        ]);
    }

    public function chart()
    {
        $data = DB::table('messages')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(7)
            ->get();

        return response()->json($data);
    }
}