<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Activity;
use App\Models\Time;
use App\Models\Place;
use App\Models\Attendance;
use App\Models\User;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::orderBy('place')->get();
        return view('admin.place.index')
                ->with('places', $places);
    }

    public function edit($pid)
    {
        $place = Place::where('id', '=', $pid)->first();
        if(!$place) {
            return redirect('/admin/place/')->with('error', "そのような活動施設がありません");
        }
        return view('admin.place.edit')
                ->with('place', $place);
    }

    public function update(Request $request)
    {
        // dd("place update", $request);
        // $this->validate($request, [
        //     'place' => [
        //         'required',
        //         'max:100',
        //         Rule::unique('places')->ignore($request->pid),
        //     ],
        // ]);

        $validated = $request->validate([
            'place' => [
                'required',
                'max:100',
                Rule::unique('places')->ignore($request->pid),
            ],
        ]);

        $place = Place::where('id', '=', $request->pid)->first();
        if (!$place) {
            return redirect('/admin/dashboard/')->with('error', "そのような活動施設がありません");
        }
        $place->place = $request->place;
        if ($place->default_place == 0 && isset($request->default_place)) {
            // デフォルト活動場所が変更された
            $prev_default = Place::where('default_place', '=', 1)->first();
            if ($prev_default) {
                $prev_default->default_place = 0;
                $prev_default->save();
            }
            $place->default_place = 1;
        }

        $place->save();
        return redirect()->route('admin.place')
            ->with('success', "活動施設情報を更新しました");
    }

    public function create()
    {
        return view('admin.place.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'place' => [
                'required',
                'max:100',
                Rule::unique('places'),
            ],
        ]);

        $place = new Place();
        $place->place = $request->place;
        if (isset($request->default_place)) {
            // デフォルト活動場所が変更された
            $prev_default = Place::where('default_place', '=', 1)->first();
            $prev_default->default_place = 0;
            $prev_default->save();
            $place->default_place = 1;
        } else {
            $place->default_place = 0;
        }
        $place->save();
        return redirect()->route('admin.place')
                    ->with('success', "活動施設情報を登録しました");
    }

    public function delete($pid)
    {
        // dd("place delete", $pid);
        $place = Place::where('id', '=', $pid)->first();
        if(!$place) {
            return redirect('/admin/place/')->with('error', "そのような活動施設がありません");
        }
        // その施設での登録済活動回数を取得する．
        $cnt = Activity::where('place_id', '=', $pid)->count();
        if ($cnt > 0) {
            return redirect('/admin/place/'.$pid)->with('error', "この施設での活動が" . $cnt .  "回登録されているので施設情報を削除できません．削除する前にこの施設での活動予定を削除してください．");
        }
        return view('admin.place.delete')
                ->with('place', $place)
                ->with('cnt', $cnt);
    }

    public function destroy($pid)
    {
        $place = Place::where('id', '=', $pid)->first();
        if(!$place) {
            return redirect('/admin/dashboard/')->with('error', "そのような活動施設がありません");
        }
        $place->delete();
        // 削除した後，default_place がなければ，どれかを default_placeにする
        // なお，データベースから削除しても $place 自体はまだ生きている
        if ($place->default_place == 1) {
            $default_place = Place::get()->first();
            if ($default_place) {
                $default_place->default_place = 1;
                $default_place->save();
                return redirect('/admin/place/')->with('success', "活動施設情報を削除し，デフォルト活動施設を変更しました");
            } else {
                // @codeCoverageIgnoreStart
                return redirect('/admin/place/')->with('success', "すべての活動施設情報が削除されました");
                // @codeCoverageIgnoreEnd
            }
        }
        return redirect('/admin/place/')->with('success', "活動施設情報を削除しました");
    }
}
