<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // ===========================================
    // LOGIN
    // ===========================================
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/'
            ]
        ]);

        $admin = DB::table('admins')
            ->whereRaw('BINARY email = ?', [$req->email])
            ->first();

        if (!$admin) {

            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan'
            ], 401);

        }

        if (!Hash::check($req->password, $admin->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 401);

        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',

            'user' => [

                'id' => $admin->id,
                'nama' => $admin->nama,
                'email' => $admin->email,
                'role' => $admin->role

            ]

        ]);
    }

    // ===========================================
    // FORGOT PASSWORD
    // ===========================================
    public function forgotPassword(Request $req)
    {
        $req->validate([
            'email' => 'required|email'
        ]);

        $admin = DB::table('admins')
            ->where('email', $req->email)
            ->first();

        if (!$admin) {

            return response()->json([
                'message' => 'Email tidak ditemukan'
            ], 404);

        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')
            ->updateOrInsert(

                ['email' => $req->email],

                [
                    'token' => $token,
                    'created_at' => now()
                ]

            );

        $link =
            "http://localhost:5173/reset-password?token=$token";

        Mail::raw(

            "Klik link berikut untuk reset password:\n$link",

            function ($message) use ($req) {

                $message
                    ->to($req->email)
                    ->subject('Reset Password');

            }

        );

        return response()->json([
            'message' => 'Link reset berhasil dikirim'
        ]);
    }

    // ===========================================
    // RESET PASSWORD
    // ===========================================
    public function resetPassword(Request $req)
    {
        $req->validate([

            'token' => 'required',

            'password' => [

                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/'

            ]

        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('token', $req->token)
            ->first();

        if (!$reset) {

            return response()->json([
                'message' => 'Token tidak valid'
            ], 400);

        }

        DB::table('admins')
            ->where('email', $reset->email)
            ->update([

                'password' => Hash::make($req->password),

                'updated_at' => now()

            ]);

        DB::table('password_reset_tokens')
            ->where('email', $reset->email)
            ->delete();

        return response()->json([
            'message' => 'Password berhasil direset'
        ]);
    }
}