<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $req->validate([
            'email'=>'required|email',
            'password'=>[
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/'
            ]
        ]);

        $admin = DB::table('admins')
            ->where('email',$req->email)
            ->first();

        if(!$admin){
            return response()->json([
                'success'=>false,
                'message'=>'Email tidak ditemukan'
            ],401);
        }

        if(!Hash::check($req->password,$admin->password)){
            return response()->json([
                'success'=>false,
                'message'=>'Password salah'
            ],401);
        }

        return response()->json([
            'success'=>true,
            'message'=>'Login berhasil'
        ]);
    }
}