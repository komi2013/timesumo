<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class EmailLoginController extends Controller {

    public function index(Request $request) {

        return view('auth.email_login');
    }
    public function staff(Request $request, $directory=null, $controller=null,$action=null,
            $group_id,$password,$sample_usr) {
        if ($request->cookie('lang')) {
            $lang = $request->cookie('lang');
        } else {
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
            $lang = (strpos($lang,'en') !== false) ? 'en' : $lang;
        }
        \Cookie::queue('lang', $lang);
        \App::setLocale($lang);
        $group = DB::table('m_group')->where('group_id',$group_id)->first();
        if ($group->password != $password) {
            return view('errors.404');
        }
        $request->session()->put('group_id', $group_id);
        $request->session()->put('sample_usr', $sample_usr);
        return view('auth.email_login');

    }
    public function owner(Request $request, $directory=null, $controller=null,$action=null,
            $area_id) {
        if ($request->cookie('lang')) {
            $lang = $request->cookie('lang');
        } else {
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
            $lang = (strpos($lang,'en') !== false) ? 'en' : $lang;
        }
        \Cookie::queue('lang', $lang);
        \App::setLocale($lang);
        $request->session()->put('area_id', $area_id);
        return view('auth.email_login');

    }
}

