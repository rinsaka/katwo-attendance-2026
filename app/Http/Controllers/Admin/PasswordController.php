<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\User;

class PasswordController extends Controller
{
    public function passwd()
    {
        $admin = \Auth::user();
        return view('admin.passwd')
                ->with('id', $admin->id);
    }

    public function passwd_update(Request $request)
    {
        $admin = \Auth::user();
        // 現在のパスワードを確認
        if (!password_verify($request->current_password, $admin->password)) {
            return redirect('/admin/password')
                ->with('error', 'パスワードが違います');
        }
        // ここはありえないはず
        if ($request->id != $admin->id) {
            return redirect('/admin/password')
                ->with('error', 'IDが不正です');
        }

        // Validation（6文字以上あるかなどのチェック）
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ]);


        // Validation（2つが一致しているかのチェック）
        if ($request->new_password != $request->new_password_confirm) {
            return redirect('/admin/password')
                ->with('error', '新しいパスワード（と確認用）が一致しません');
        }

        // パスワードを保存
        $admin->password = bcrypt($request->new_password);
        $admin->save();
        return redirect('/admin/dashboard')
                ->with('success', 'パスワードを変更しました');
    }

    /*
     *   ユーザ（団員）パスワードの変更画面
     */
    public function userpasswd()
    {
        $admin = \Auth::user();
        return view('admin.userpasswd')
                ->with('id', $admin->id);
    }

    public function userpasswd_update(Request $request)
    {
        $admin = \Auth::user();
        // 管理者パスワードを確認
        if (!password_verify($request->password, $admin->password)) {
            return redirect('/admin/userpassword')
                ->with('error', '管理者パスワードが違います');
        }
        // Validation（6文字以上あるかなどのチェック）
        $validated = $request->validate([
            'login_id' => ['required'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        // Validation（2つが一致しているかのチェック）
        if ($request->new_password != $request->new_password_confirm) {
            return redirect('/admin/userpassword')
                ->with('error', '新しいパスワード（と確認用）が一致しません');
        }

        $user = User::where('login_id', '=', $request->login_id)->first();
        if (!$user) {
            return redirect('/admin/userpassword')
                ->with('error', '団員のログインIDが違います');
        }
        // パスワードを保存
        $user->password = bcrypt($request->new_password);
        $user->save();
        return redirect('/admin/dashboard')
                ->with('success', '団員 '. $user->login_id . ' のパスワードを変更しました');
    }
}
