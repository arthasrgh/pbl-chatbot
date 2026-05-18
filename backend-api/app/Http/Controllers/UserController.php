<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(
            DB::table('users')->get()
        );
    }

    public function store(Request $req)
    {

        $req->validate([

            'name'=>'required',

            'email'=>[
                'required',
                'email',
                'unique:users,email'
            ],

            'password'=>[
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/'
            ]
        ]);

        DB::table('users')->insert([

            'name'=>$req->name,

            'email'=>$req->email,

            'password'=>Hash::make(
                $req->password
            ),

            'created_at'=>now(),
            'updated_at'=>now()

        ]);

        return response()->json([
            'success'=>true,
            'message'=>'User berhasil dibuat'
        ]);
    }

    public function update(Request $req,$id)
    {

        $req->validate([

            'name'=>'required',

            'email'=>[
                'required',
                'email',
                'unique:users,email,'.$id
            ]
        ]);

        $data=[

            'name'=>$req->name,

            'email'=>$req->email,

            'updated_at'=>now()
        ];

        if($req->password){

            $data['password']=Hash::make(
                $req->password
            );
        }

        DB::table('users')
            ->where('id',$id)
            ->update($data);

        return response()->json([
            'success'=>true,
            'message'=>'User berhasil diupdate'
        ]);
    }

    public function delete($id)
    {

        DB::table('users')
            ->where('id',$id)
            ->delete();

        return response()->json([
            'success'=>true
        ]);
    }
}