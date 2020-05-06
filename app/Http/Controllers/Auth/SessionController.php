<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller {

    public function lang(Request $request) {
        \Cookie::queue('lang', $request->lang);
        $res[0] = 1;
        return json_encode($res);
    }
    public function signout(Request $request) {
        $request->session()->flush();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
        $res[0] = 1;
        return json_encode($res);
    }
}

