<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailCheckController extends Controller {

    public function lessuri(Request $request) {
        $usr = DB::table('t_usr')
            ->where('email',$request->email)
            ->first();
        
        if (isset($usr->usr_id) AND Hash::check($request->password, $usr->password)) {
            $login = true;
            new \App\My\AfterLogin($usr->usr_id);
            $redirect = session('redirect') ?: '/';
            $request->session()->forget('redirect');
        } else {
            $login = false;
            $redirect = '/';
        }

        $res[0] = 1;
        $res[1] = $login;
        $res[2] = $redirect;
        return json_encode($res);
    }
}

