<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\AuthData;

class EmailVerifyController extends Controller {

    public function code(Request $request, $directory=null, $controller=null,
            $action=null, $auth) {
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
                    "password" => session('password')
                    ,"updated_at" => now()
                ]);
            $usr_id = $obj->usr_id;
            $message = __('successfully password re-issued');
        } else {
            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            DB::table('t_usr')->insert([
                "usr_id" => $usr_id
                ,"oauth_type" => 3
                ,"updated_at" => now()
                ,"email" => session('email')
                ,"password" => session('password')
            ]);
            $message = __('successfully registered');
        }
        
        $request->session()->put('usr_id', $usr_id);
        $authdata = new AuthData();
        if ($request->cookie('after_signin')) {
            
        }
        $redirect = $authdata->arr_redirect[$request->cookie('after_signin')] ?? '/';
        return view('auth.email_complete', compact('redirect','message'));
//        return redirect('/Auth/Applicant/index/');
    }
}

