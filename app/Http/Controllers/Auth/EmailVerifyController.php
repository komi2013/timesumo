<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailVerifyController extends Controller {

    public function code(Request $request, $directory=null, $controller=null,$action=null,
            $auth) {
        if ($auth != session('email_auth')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        \App::setLocale($request->cookie('lang'));
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
            if ($_SERVER['SERVER_NAME'] == 'timebook.quigen.info') {
                $request->session()->put('usr_id', $usr->usr_id);
                $request->session()->put('group_id', 0);
            } else {
                new \App\My\AfterLogin($usr->usr_id);
            }
        } else {
            $arr_email = explode("@", session('email'));
            if ($_SERVER['SERVER_NAME'] == 'timebook.quigen.info') {
                $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
                DB::table('t_usr')->insert([
                    "usr_id" => $usr_id
                    ,"oauth_type" => 3
                    ,"updated_at" => now()
                    ,"email" => session('email')
                    ,"password" => Hash::make(session('password'))
                    ,"usr_name" => $arr_email[0]
                ]);
                $request->session()->put('usr_id', $usr_id);
                $request->session()->put('group_id', 0);
            } else {
                $obj = new \App\My\AfterRegister($arr_email[0]);
                new \App\My\AfterLogin($obj->usr_id);
            }
        }
        $request->session()->forget('email_auth');
        $request->session()->forget('email');
        $request->session()->forget('password');
        $redirect = session('redirect') ?: '/';
        $request->session()->forget('redirect');
        return redirect($redirect);
    }
}

