<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailVerifyController extends Controller {

    public function code(Request $request, $directory=null, $controller=null,$action=null,
            $auth) {
        \App::setLocale($request->cookie('lang'));
        if ($auth != session('email_auth')) {
            return view('errors.404');
        }

        $usr = DB::table('t_usr')
            ->where('email',session('email'))
            ->first();
        
        if (isset($usr->usr_id) AND $usr->oauth_type == 3) {
            DB::table('t_usr')
                ->where("usr_id",$usr->usr_id)
                ->update([
                    "password" => Hash::make(session('password'))
                    ,"updated_at" => now()
                ]);
            new \App\My\AfterLogin($usr->usr_id);
        } else {
            $arr_email = explode("@", session('email'));
            new \App\My\AfterLogin($arr_email[0]);
        }
        $request->session()->forget('email_auth');
        $request->session()->forget('email');
        $request->session()->forget('password');
        $request->session()->put('usr_id', $usr_id);
        $redirect = session('redirect') ?: '/';
        $request->session()->forget('redirect');
        return redirect($redirect);
        
//        return view('auth.email_complete', compact('redirect','message'));
    }
}

