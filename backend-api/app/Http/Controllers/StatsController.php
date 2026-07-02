<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Statistik Dashboard
     */
    public function index()
    {
        // Total pengguna chatbot
        $totalUsers = DB::table('messages')
            ->distinct('nomor')
            ->count('nomor');

        // Total chat
        $totalChat = DB::table('messages')->count();

        // Chat hari ini
        $todayChat = DB::table('messages')
            ->whereDate('created_at', today())
            ->count();

        // Hot Leads
        $hotLeads = DB::table('messages')
            ->where(function ($q) {
                $q->where('pesan', 'like', '%daftar%')
                    ->orWhere('pesan', 'like', '%mitra%')
                    ->orWhere('pesan', 'like', '%gabung%')
                    ->orWhere('pesan', 'like', '%izin%')
                    ->orWhere('pesan', 'like', '%ormas%')
                    ->orWhere('pesan', 'like', '%paskibraka%')
                    ->orWhere('pesan', 'like', '%penelitian%');
            })
            ->distinct('nomor')
            ->count('nomor');

        // Total Admin
        $totalAdmin = DB::table('admins')
            ->where('role', 'admin')
            ->count();

        // Total CS
        $totalCs = DB::table('admins')
            ->where('role', 'cs')
            ->count();

        // Statistik AI
        $totalAiUsers = DB::table('ai_usages')->count();

        $totalAiRequest = DB::table('ai_usages')->sum('jumlah');

        $aiHampirHabis = DB::table('ai_usages')
            ->whereBetween('jumlah', [15, 19])
            ->count();

        $aiHabis = DB::table('ai_usages')
            ->where('jumlah', '>=', 20)
            ->count();

        return response()->json([
            'total_users'      => $totalUsers,
            'total_chat'       => $totalChat,
            'today_chat'       => $todayChat,
            'hot_leads'        => $hotLeads,

            'total_admin'      => $totalAdmin,
            'total_cs'         => $totalCs,

            'total_ai_users'   => $totalAiUsers,
            'total_ai_request' => $totalAiRequest,
            'ai_hampir_habis'  => $aiHampirHabis,
            'ai_habis'         => $aiHabis,
        ]);
    }

    /**
     * Grafik Chat 7 Hari Terakhir
     */
    public function chart()
    {
        $data = DB::table('messages')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->limit(7)
            ->get();

        return response()->json($data);
    }

    /**
     * Word Cloud
     */
    public function wordcloud()
    {
        $messages = DB::table('messages')
            ->pluck('pesan')
            ->implode(' ');

        $text = strtolower($messages);

        $text = preg_replace('/[^a-zA-Z\s]/', ' ', $text);

        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = [
            'yang','dan','di','ke','dari','untuk','dengan','atau',
            'pada','adalah','itu','ini','saya','kami','anda',
            'kamu','iya','ya','tidak','bisa','agar','dalam',
            'mohon','terima','kasih','selamat','siang','pagi',
            'sore','malam','halo','hai','menu'
        ];

        $filtered = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });

        $counts = array_count_values($filtered);

        arsort($counts);

        $result = [];

        foreach (array_slice($counts, 0, 30, true) as $word => $count) {

            $result[] = [
                'text'  => $word,
                'value' => $count
            ];
        }

        return response()->json($result);
    }

    /**
     * Hot Leads
     */
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

    public function aiChart()
    {
        $data = DB::table('ai_usages')
            ->selectRaw('DATE(updated_at) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function aiStats()
    {
        $totalUserAI = DB::table('ai_usages')->count();

        $totalRequest = DB::table('ai_usages')->sum('jumlah');

        $hampirHabis = DB::table('ai_usages')
            ->whereBetween('jumlah', [15, 19])
            ->count();

        $habis = DB::table('ai_usages')
            ->where('jumlah', '>=', 20)
            ->count();

        return response()->json([
            'totalUserAI' => $totalUserAI,
            'totalRequest' => $totalRequest,
            'hampirHabis' => $hampirHabis,
            'habis' => $habis
        ]);
    }

}