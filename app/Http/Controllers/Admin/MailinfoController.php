<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mailinfo;

class MailinfoController extends Controller
{
    public function edit()
    {
        // dd("edit mailinfo");
        $mailinfo = Mailinfo::where('key', '=', 'signiture')
                        ->first();
        if (!$mailinfo) {
            $mailinfo = new Mailinfo();
            $mailinfo->key = 'signiture';
            $mailinfo->mailinfo = 'メールフッタをここに';
            $mailinfo->save();
        }
        return view('admin.mailinfo.edit')
                ->with('mailinfo', $mailinfo);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mailinfo' => ['required'],
        ]);

        $mailinfo = Mailinfo::where('id', '=', $request->id)
                            ->where('key', '=', $request->key)
                            ->first();
        if (!$mailinfo) {
            return redirect('/admin/dashboard')->with('error', '不正なパラメータです');
        }
        $mailinfo->mailinfo = $request->mailinfo;
        $mailinfo->save();
        return redirect('/admin/mailinfo')->with('success', "メールフッタを更新しました");
    }
}
