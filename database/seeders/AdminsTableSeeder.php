<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->delete();
        Admin::create([
            'name' => 'Taro Admin',
            'email' => 'taro@sample.com',
            'login_id' => 'taro',
            'password' => bcrypt('admin'),
            'created_at' => '2026-11-21 16:01:01',
            'updated_at' => '2026-11-21 16:01:01'
        ]);
        Admin::create([
            'name' => 'Jiro Admin',
            'email' => 'jiro@sample.com',
            'login_id' => 'jiro',
            'password' => bcrypt('admin'),
            'created_at' => '2026-11-21 16:02:01',
            'updated_at' => '2026-11-21 16:02:01'
        ]);
    }
}
