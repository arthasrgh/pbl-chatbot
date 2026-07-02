<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersBotController extends Controller
{
    /**
     * Daftar seluruh user chatbot
     */
    public function index(Request $request)
    {
        $query = DB::table('messages');

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('filter')) {

            switch ($request->filter) {

                case 'today':

                    $query->whereDate('created_at', today());

                    break;

                case 'week':

                    $query->whereBetween(
                        'created_at',
                        [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]
                    );

                    break;

                case 'month':

                    $query
                        ->whereMonth(
                            'created_at',
                            now()->month
                        )
                        ->whereYear(
                            'created_at',
                            now()->year
                        );

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH NOMOR
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $query->where(
                'nomor',
                'like',
                '%' . $request->search . '%'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP USER
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->select(
                'nomor',

                DB::raw('COUNT(*) as total_chat'),

                DB::raw('MIN(created_at) as first_chat'),

                DB::raw('MAX(created_at) as last_chat')
            )

            ->groupBy('nomor')

            ->orderByDesc('last_chat')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | STATUS USER
        |--------------------------------------------------------------------------
        */

        $users = $users->map(function ($item) {

            $lastChat = Carbon::parse($item->last_chat);

            $days = $lastChat->diffInDays(now());

            if ($days == 0) {

                $status = "Aktif";

            } elseif ($days <= 7) {

                $status = "Tidak Aktif";

            } else {

                $status = "Lama Tidak Aktif";

            }

            return [

                "nomor"      => $item->nomor,

                "total_chat" => $item->total_chat,

                "first_chat" => $item->first_chat,

                "last_chat"  => $item->last_chat,

                "status"     => $status

            ];

        });

        return response()->json($users);
    }

    /**
     * Detail chat berdasarkan nomor
     */
    public function show($nomor)
    {
        $chat = DB::table('messages')

            ->where('nomor', $nomor)

            ->orderBy('created_at')

            ->get();

        return response()->json($chat);
    }

    /**
     * Ringkasan Statistik User
     */
    public function summary()
    {
        $todayUsers = DB::table('messages')

            ->whereDate('created_at', today())

            ->distinct()

            ->count('nomor');

        $weekUsers = DB::table('messages')

            ->whereBetween(
                'created_at',
                [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]
            )

            ->distinct()

            ->count('nomor');

        $monthUsers = DB::table('messages')

            ->whereMonth(
                'created_at',
                now()->month
            )

            ->whereYear(
                'created_at',
                now()->year
            )

            ->distinct()

            ->count('nomor');

        $totalUsers = DB::table('messages')

            ->distinct()

            ->count('nomor');

        return response()->json([

            "today_users" => $todayUsers,

            "week_users"  => $weekUsers,

            "month_users" => $monthUsers,

            "total_users" => $totalUsers

        ]);
    }
}