<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->delete();
        User::create([
            'name' => 'A. Sample',
            'email' => 'a@sample.com',
            'password' => bcrypt('abc'),
            'created_at' => '2026-11-21 00:01:01',
            'updated_at' => '2026-11-21 00:01:01'
        ]);
        User::create([
            'name' => 'B. Sample',
            'email' => 'b@sample.com',
            'password' => bcrypt('abc'),
            'created_at' => '2026-11-21 00:02:01',
            'updated_at' => '2026-11-21 00:02:01'
        ]);
        User::create([
            'name' => 'C. Sample',
            'email' => 'c@sample.com',
            'password' => bcrypt('abc'),
            'created_at' => '2026-11-21 00:03:01',
            'updated_at' => '2026-11-21 00:03:01'
        ]);
    }
}
