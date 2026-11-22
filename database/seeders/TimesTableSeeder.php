<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一旦中身を削除する
        DB::table('times')->delete();

        DB::table('times')->insert([
            'jikan' => '13:00 - 17:00',
            'default_jikan' => false,
            'created_at' => '2026-11-22 14:30:01',
            'updated_at' => '2026-11-22 14:30:01'
        ]);

        DB::table('times')->insert([
            'jikan' => '17:00 - 21:00',
            'default_jikan' => false,
            'created_at' => '2026-11-22 14:30:02',
            'updated_at' => '2026-11-22 14:30:02'
        ]);

        DB::table('times')->insert([
            'jikan' => '18:00 - 22:00',
            'default_jikan' => true,
            'created_at' => '2026-11-22 14:30:03',
            'updated_at' => '2026-11-22 14:30:03'
        ]);

        DB::table('times')->insert([
            'jikan' => '13:00 - 22:00',
            'default_jikan' => false,
            'created_at' => '2026-11-22 14:30:04',
            'updated_at' => '2026-11-22 14:30:04'
        ]);

        // DB::table('times')->insert([
        //     'jikan' => '18:00 - 21:00',
        //     'default_jikan' => false,
        //     'created_at' => '2026-11-22 14:30:05',
        //     'updated_at' => '2026-11-22 14:30:05'
        // ]);

        // DB::table('times')->insert([
        //     'jikan' => '17:30 - 21:00',
        //     'default_jikan' => false,
        //     'created_at' => '2026-11-22 14:30:06',
        //     'updated_at' => '2026-11-22 14:30:06'
        // ]);
    }
}
