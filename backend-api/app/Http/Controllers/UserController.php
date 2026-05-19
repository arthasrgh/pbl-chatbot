<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

public function index()
{
    $admins = DB::table('admins')
        ->select('id', 'username', 'email', 'created_at')
        ->get();
        
    return response()->json([
        'success' => true,
        'data' => $admins
    ]);
}

public function store(Request $req)
{
    $req->validate([
        'username' => ['required', 'string', 'max:255', 'unique:admins,username'],
        'email'    => ['nullable', 'email', 'max:255', 'unique:admins,email'],
        'password' => [
            'required',
            'min:8',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[!@#$%^&*(),.?":{}|<>_]/'
        ]
    ]);

    $id = DB::table('admins')->insertGetId([
        'username' => $req->username,
        'email'    => $req->email,
        'password' => Hash::make($req->password),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Admin berhasil ditambahkan',
        'data' => ['id' => $id, 'username' => $req->username, 'email' => $req->email]
    ], 201);
}

public function update(Request $req, $id)
{
    $admin = DB::table('admins')->where('id', $id)->first();

    if (!$admin) {
        return response()->json([
            'success'=>false,
            'message'=>'Admin tidak ditemukan'
        ],404);
    }

    $req->validate([
        'username'=>[
            'required',
            'string',
            'max:255',
            'unique:admins,username,'.$id
        ],

        'email'=>[
            'nullable',
            'email',
            'max:255',
            'unique:admins,email,'.$id
        ],

        'password'=>[
            'nullable',
            'min:8',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[!@#$%^&*(),.?":{}|<>_]/'
        ]
    ]);

    $data = [
        'username'=>$req->username,
        'email'=>$req->email,
        'updated_at'=>now()
    ];

    if ($req->filled('password')) {
        $data['password'] = Hash::make($req->password);
    }

    DB::table('admins')
        ->where('id',$id)
        ->update($data);

    return response()->json([
        'success'=>true,
        'message'=>'Admin berhasil diupdate'
    ]);
}
    public function delete($id)
    {
        $admin = DB::table('admins')->where('id', $id)->first();
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ], 404);
        }

        DB::table('admins')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil dihapus'
        ]);
    }
}