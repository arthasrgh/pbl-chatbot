<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
public function index()
{
    $totalUsers = DB::table('messages')
        ->distinct('nomor')
        ->count('nomor');

    $totalChat = DB::table('messages')->count();

    $todayChat = DB::table('messages')
        ->whereDate('created_at', today())
        ->count();

    $hotLeads = DB::table('messages')
        ->where(function ($q) {
            $q->where('pesan', 'like', '%daftar%')
              ->orWhere('pesan', 'like', '%mitra%')
              ->orWhere('pesan', 'like', '%gabung%');
        })
        ->count();

    $totalAdmin = DB::table('admins')
        ->where('role', 'admin')
        ->count();

    $totalCs = DB::table('admins')
        ->where('role', 'cs')
        ->count();

    return response()->json([

        'total_users' => $totalUsers,

        'total_chat' => $totalChat,

        'today_chat' => $todayChat,

        'hot_leads' => $hotLeads,

        'total_admin' => $totalAdmin,

        'total_cs' => $totalCs

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

    public function wordcloud()
    {
        $messages = DB::table('messages')
            ->pluck('pesan')
            ->implode(' ');

        $text = strtolower($messages);

        // hapus simbol & angka
        $text = preg_replace('/[^a-zA-Z\s]/', ' ', $text);

        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = [
            'yang','dan','di','ke','dari','untuk','dengan','atau','pada',
            'adalah','itu','ini','saya','kami','anda','kamu','iya','ya',
            'tidak','bisa','agar','dalam','mohon','terima','kasih',
            'selamat','siang','pagi','sore','malam','halo','hai','menu'
        ];

        $filtered = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });

        $counts = array_count_values($filtered);
        arsort($counts);

        $result = [];
        foreach (array_slice($counts, 0, 30, true) as $word => $count) {
            $result[] = [
                'text' => $word,
                'value' => $count
            ];
        }

        return response()->json($result);
    }

    public function hotLeads()
{
    $data = DB::table('messages')
        ->select(
            'nomor',
            DB::raw('COUNT(*) as total_chat'),
            DB::raw('MAX(created_at) as last_chat')
        )
        ->groupBy('nomor')
        ->orderByDesc('total_chat')
        ->get();

    return response()->json($data);
}

}