<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Activity;
// use App\Models\Mailinfo;
// use Carbon\Carbon;

class MenuController extends Controller
{
    public function show($id)
    {
        $menu = Menu::where('id', '=', $id)->first();
        if (!$menu) {
            return redirect('/dashboard')->with('status', '練習メニューが見つかりません');
        }
        return view('menu.edit')
                ->with('menu', $menu);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:30'],
            'menu' => ['required'],
        ]);

        $menu = Menu::where('id', '=', $request->mid)->first();
        if (!$menu) {
            return redirect('/dashboard')->with('status', '練習メニューが見つかりません');
        }
        $menu->name = $request->name;
        $menu->menu = $request->menu;
        $menu->save();

        return redirect($request->url)->with('status', '練習メニューを更新しました');
    }

    public function create(Request $request)
    {
        $activity = Activity::where('id', '=', $request->aid)->first();
        if (!$activity) {
            return redirect('/dashboard')->with('status', 'アクティビティが見つかりません');
        }
        return view('menu.create')
                ->with('activity', $activity);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:30'],
            'menu' => ['required'],
        ]);

        $menu = new Menu();
        $menu->activity_id = $request->aid;
        $menu->name = $request->name;
        $menu->menu = $request->menu;
        $menu->save();

        return redirect($request->url)->with('status', '練習メニューを登録しました');
    }

    /*
    *  指定したメニューの案内メール本文を生成する
    *
    */
    public function mail($mid)
    {
        dd("mail", $mid);
    }

}
