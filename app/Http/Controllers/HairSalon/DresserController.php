<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DresserController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $redirect='', $language='') {
        if (!$request->session()->get('usr_id')) {
            return redirect('/Auth/Sign/in/0/');
        }
        $usr_id = $request->session()->get('usr_id');
        \Cookie::queue('lang', $lang);
        \App::setLocale($lang);
        $usr = DB::table('t_usr')->where('usr_id',$usr_id)->first();
        $usr_salon = DB::connection('salon')->table('t_my_style')->where('usr_id',$usr_id)->first();
        $hair_style = DB::connection('salon')->table('m_hair_style')->get();
        
        return view('hair_salon.dresser', compact('fb_url','gp_url'));
    }
}

