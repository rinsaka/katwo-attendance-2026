<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MailinfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一旦中身を削除する
        DB::table('mailinfos')->delete();

        DB::table('mailinfos')->insert([
            'key' => 'signiture',
            'mailinfo' => "※このメールに返信すると、全員に配信されます。注意ください。\n＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋\n神戸学園都市吹奏楽団\nml@ml.kat-wind.com\n\n出欠登録は　→　https://kat-wind.com/attendance\n練習日程（カレンダー）は　→　https://kat-wind.com/schedule/\n\n配信を停止する場合は　→　ml@kat-wind.com\n＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋＋",
            'created_at' => '2026-11-22 15:45:01',
            'updated_at' => '2026-11-22 15:45:01'
        ]);
    }
}
