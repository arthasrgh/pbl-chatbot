<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {

            $table->string('nama')
                  ->after('id');

            $table->enum('role',[
                'admin',
                'cs'
            ])
            ->default('cs')
            ->after('password');

        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {

            $table->dropColumn([
                'nama',
                'role'
            ]);

        });
    }
};