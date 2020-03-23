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

        $obj = DB::table('t_usr')
            ->where('email',session('email'))
            ->first();
        
        if (isset($obj->usr_id) AND $obj->oauth_type == 3) {
            DB::table('t_usr')
                ->where("usr_id",$obj->usr_id)
                ->update([
                    "password" => Hash::make(session('password'))
                    ,"updated_at" => now()
                ]);
            $usr_id = $obj->usr_id;
            $message = __('email_verify.reissued');
        } else {
            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            DB::table('t_usr')->insert([
                "usr_id" => $usr_id
                ,"oauth_type" => 3
                ,"updated_at" => now()
                ,"email" => session('email')
                ,"password" => Hash::make(session('password'))
            ]);
            $message = __('email_verify.registered');
        }

        $request->session()->put('usr_id', $usr_id);
        $redirect = $request->cookie('redirect') ?? '/';
        return view('auth.email_complete', compact('redirect','message'));
    }
}

