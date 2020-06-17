<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class EmailLoginController extends Controller {
    public function __construct() {
        if (\Cookie::get('lang')) {
            $lang = \Cookie::get('lang');
        } else {
            $lang = 'en';
            if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE'] ) AND $_SERVER['HTTP_ACCEPT_LANGUAGE']) {
                $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
            }
            $arr = ['en','ja'];
            if ( !in_array($lang, $arr) ) {
                $lang = 'en';
            }
        }
        \Config::set('session.lifetime', 60 * 24 * 365);
        \Cookie::queue('lang',$lang, 60 * 24 * 365);
        \App::setLocale($lang);
    }
    public function index(Request $request,$directory,$controller,$action) {

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
        if ($request->session()->get('group_id')) {
            $msg = 'have group_id:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));            
        }
        $request->session()->put('area_id', $area_id);
        return view('auth.email_login');

    }
    public function friend(Request $request,$directory,$controller,$action,
            $your_owner) {
        if ($request->session()->get('group_id')) {
            $msg = 'have group_id:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));            
        }
        $request->session()->put('your_owner', $your_owner);
        return view('auth.email_login');

    }
}

