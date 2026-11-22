<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一旦中身を削除する
        DB::table('places')->delete();

        DB::table('places')->insert([
            'place' => '神戸常盤アリーナ 研修室A（スリッパ要）',
            'default_place' => false,
            'created_at' => '2026-11-22 14:00:01',
            'updated_at' => '2026-11-22 14:00:01'
        ]);

        DB::table('places')->insert([
            'place' => '兵庫県立文化体育館 研修室B',
            'default_place' => true,
            'created_at' => '2026-11-22 14:00:02',
            'updated_at' => '2026-11-22 14:00:02'
        ]);

        DB::table('places')->insert([
            'place' => '兵庫県立文化体育館 会議室A',
            'default_place' => false,
            'created_at' => '2026-11-22 14:00:03',
            'updated_at' => '2026-11-22 14:00:03'
        ]);

        DB::table('places')->insert([
            'place' => '兵庫県立文化体育館 小ホール',
            'default_place' => false,
            'created_at' => '2026-11-22 14:00:04',
            'updated_at' => '2026-11-22 14:00:04'
        ]);

        DB::table('places')->insert([
            'place' => '西部市民会館（魚住）',
            'default_place' => false,
            'created_at' => '2026-11-22 14:00:05',
            'updated_at' => '2026-11-22 14:00:05'
        ]);

        DB::table('places')->insert([
            'place' => '瀬楽スタジオ',
            'default_place' => false,
            'created_at' => '2026-11-22 14:00:06',
            'updated_at' => '2026-11-22 14:00:06'
        ]);

        // DB::table('places')->insert([
        //     'place' => '兵庫県立文化体育館 研修室C',
        //     'default_place' => false,
        //     'created_at' => '2026-11-22 14:00:07',
        //     'updated_at' => '2026-11-22 14:00:07'
        // ]);

        // DB::table('places')->insert([
        //     'place' => 'ふたば学舎音楽室①②',
        //     'default_place' => false,
        //     'created_at' => '2026-11-22 14:00:08',
        //     'updated_at' => '2026-11-22 14:00:08'
        // ]);

        // DB::table('places')->insert([
        //     'place' => 'ふたば学舎音楽室①',
        //     'default_place' => false,
        //     'created_at' => '2026-11-22 14:00:09',
        //     'updated_at' => '2026-11-22 14:00:09'
        // ]);

        // DB::table('places')->insert([
        //     'place' => '中央区文化センター 音楽室①',
        //     'default_place' => false,
        //     'created_at' => '2026-11-22 14:00:10',
        //     'updated_at' => '2026-11-22 14:00:10'
        // ]);
    }
}
