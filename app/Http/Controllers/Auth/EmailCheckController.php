<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailCheckController extends Controller {

    public function lessuri(Request $request) {
        $obj = DB::table('t_usr')
            ->where('email',session('email'))
            ->first();
        
        if (isset($obj->usr_id) AND Hash::check($request->password, $obj->password)) {
            $login = true;
            $request->session()->put('usr_id', $obj->usr_id);
            $redirect = $request->cookie('redirect');
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

