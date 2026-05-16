<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return DB::table('users')->get();
    }

    public function toggle(Request $req)
    {
        DB::table('users')
            ->where('id',$req->id)
            ->update([
                'is_active'=>$req->status
            ]);

        return ['success'=>true];
    }

    public function delete($id)
    {
        DB::table('users')->where('id',$id)->delete();

        return ['success'=>true];
    }
}