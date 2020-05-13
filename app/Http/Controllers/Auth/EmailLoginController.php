<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class EmailLoginController extends Controller {
    public function __construct(Request $request) {
        if ($request->cookie('lang')) {
            $lang = $request->cookie('lang');
        } else {
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
            $lang = (strpos($lang,'en') !== false) ? 'en' : $lang;
        }
        \Cookie::queue('lang', $lang);
        \App::setLocale($lang);
    }
    public function index(Request $request) {

        return view('auth.email_login');
    }
    public function staff(Request $request,$directory,$controller,$action,
            $group_id,$password,$sample_usr) {
        $group = DB::table('m_group')->where('group_id',$group_id)->first();
        if ($group->password != $password) {
            return view('errors.404');
        }
        $request->session()->put('group_id', $group_id);
        $request->session()->put('sample_usr', $sample_usr);
        return view('auth.email_login');

    }
    public function owner(Request $request,$directory,$controller,$action,
            $area_id) {
        $request->session()->put('area_id', $area_id);
        return view('auth.email_login');

    }
}

