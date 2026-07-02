<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiUsage;
use Carbon\Carbon;

class AiUsageController extends Controller
{
    public function checkLimit(Request $request)
    {
        $request->validate([
            'nomor' => 'required'
        ]);

        $today = Carbon::today()->toDateString();

        $usage = AiUsage::firstOrCreate(
            [
                'nomor' => $request->nomor
            ],
            [
                'jumlah' => 0,
                'tanggal' => $today
            ]
        );

        // Reset jika hari sudah berganti
        if ($usage->tanggal != $today) {
            $usage->jumlah = 0;
            $usage->tanggal = $today;
            $usage->save();
        }

        // Maksimal 20 request per hari
        if ($usage->jumlah >= 20) {

            return response()->json([
                'success' => false,
                'message' => 'Limit AI hari ini telah habis.',
                'remaining' => 0
            ]);

        }

        // Tambah penggunaan
        $usage->increment('jumlah');

        return response()->json([
            'success' => true,
            'remaining' => 20 - $usage->jumlah
        ]);
    }

    public function index()
{
    return response()->json(
        AiUsage::orderBy('tanggal', 'desc')
            ->orderBy('jumlah', 'desc')
            ->get()
    );
}
}