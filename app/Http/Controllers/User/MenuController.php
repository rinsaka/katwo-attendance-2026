<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Menu;
// use App\Models\Activity;
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
        dd('update', $request);
    }

    public function create(Request $request)
    {
        dd('create', $request);
    }

    public function store(Request $request)
    {
        dd('store', $request);
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
