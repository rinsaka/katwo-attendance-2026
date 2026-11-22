<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一旦中身を削除する
        DB::table('parts')->delete();

        DB::table('parts')->insert([
            'part' => 'Flute and Oboe',
            's_part' => 'Fl/Ob',
            'created_at' => '2026-11-22 14:40:01',
            'updated_at' => '2026-11-22 14:40:01'
        ]);

        DB::table('parts')->insert([
            'part' => 'Clarinet',
            's_part' => 'Cl',
            'created_at' => '2026-11-22 14:40:02',
            'updated_at' => '2026-11-22 14:40:02'
        ]);

        DB::table('parts')->insert([
            'part' => 'Saxophone',
            's_part' => 'Sax',
            'created_at' => '2026-11-22 14:40:03',
            'updated_at' => '2026-11-22 14:40:03'
        ]);

        DB::table('parts')->insert([
            'part' => 'Horn',
            's_part' => 'Hr',
            'created_at' => '2026-11-22 14:40:04',
            'updated_at' => '2026-11-22 14:40:04'
        ]);

        DB::table('parts')->insert([
            'part' => 'Euphonium',
            's_part' => 'Euph',
            'created_at' => '2026-11-22 14:40:05',
            'updated_at' => '2026-11-22 14:40:05'
        ]);

        DB::table('parts')->insert([
            'part' => 'Tuba',
            's_part' => 'Tub',
            'created_at' => '2026-11-22 14:40:06',
            'updated_at' => '2026-11-22 14:40:06'
        ]);

        DB::table('parts')->insert([
            'part' => 'Trumpet',
            's_part' => 'Trp',
            'created_at' => '2026-11-22 14:40:07',
            'updated_at' => '2026-11-22 14:40:07'
        ]);

        DB::table('parts')->insert([
            'part' => 'Trombone',
            's_part' => 'Trb',
            'created_at' => '2026-11-22 14:40:08',
            'updated_at' => '2026-11-22 14:40:08'
        ]);

        DB::table('parts')->insert([
            'part' => 'Percussion',
            's_part' => 'Perc',
            'created_at' => '2026-11-22 14:40:09',
            'updated_at' => '2026-11-22 14:40:09'
        ]);
    }
}
