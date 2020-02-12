<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SignController extends Controller {

    public function in(Request $request, $directory=null, $controller=null,
            $action=null, $redirect='none', $language='') {

        $fb_id = '593374818166961';

        $fb_url = 'https://www.facebook.com/dialog/oauth'
          .'?client_id='.$fb_id
          .'&redirect_uri=https://'.$_SERVER['HTTP_HOST'].'/Auth/Fboauth/'
          ;

        $gp_url = 'https://accounts.google.com/o/oauth2/auth'
          .'?client_id=1001190811901-sj2dd1vcledc4i8hfnb3qmrt63t7ubvi.apps.googleusercontent.com'
          .'&response_type=code'
          .'&scope=https://www.googleapis.com/auth/userinfo.profile'
          .'&redirect_uri=https://'.$_SERVER['HTTP_HOST'].'/Auth/Gpcallback/'
          ;
        if ($redirect != 'none') {
            \Cookie::queue('after_signin',$redirect,60 * 24 * 10);
        }
        if ($language){
            $lang = $language;
        } elseif ($request->cookie('lang')) {
            $lang = $request->cookie('lang');
        } else {
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
            $lang = (strpos($lang,'en') !== false) ? 'en' : $lang;
        }
        \Cookie::queue('lang', $lang);
        \App::setLocale($lang);
        return view('auth.signin', compact('fb_url','gp_url'));
    }
}

