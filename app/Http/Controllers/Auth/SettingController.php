<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SettingController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null) {
        \App::setLocale(\Cookie::get('lang') ?: 'ja');
        $group_id = 0;
        return view('auth.setting', compact('group_id'));
    }
    public function factory(Request $request, $directory=null, $controller=null,$action=null, 
            $usr_id=0,$group_id=0) {
        echo '<pre>';
        var_dump(session('usr_id'),session('group_id'));
        
        $request->session()->put('usr_id', $usr_id);
        $request->session()->put('group_id', $group_id);
        
        var_dump(session('usr_id'),session('group_id'));
        echo '</pre> set';

    }

}

