<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $users = DB::table('messages')
            ->select('nomor')
            ->groupBy('nomor')
            ->orderByRaw('MAX(created_at) DESC')
            ->get();

        return view('admin.chats.index', compact('users'));
    }

    public function show($nomor)
    {
        $users = DB::table('messages')
            ->select('nomor')
            ->groupBy('nomor')
            ->orderByRaw('MAX(created_at) DESC')
            ->get();

        $messages = DB::table('messages')
            ->where('nomor', $nomor)
            ->orderBy('created_at')
            ->get();

        return view(
            'admin.chats.index',
            compact('users', 'messages', 'nomor')
        );
    }
}