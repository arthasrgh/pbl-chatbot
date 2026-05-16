<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $admin = DB::table('admins')
            ->where('username', $req->username)
            ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ]);
        }

        if (password_verify($req->password, $admin->password)) {
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Password salah'
        ]);
    }
}