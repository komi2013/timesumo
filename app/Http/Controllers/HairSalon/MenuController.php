<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class MenuController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $group_id='', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);
        
        $obj = DB::table('t_menu')->where('group_id',$group_id)->get();
        $menu = [];
        $menu[0]['menu_name'] = '';
        $arr['service_id'] = '';
        $arr['facility_id'] = '';
        $arr['start_minute'] = '';
        $arr['end_minute'] = '';
        $menu[0]['necessary'][0] = $arr;
        $arr_menu_id = [0];
//        $is_menu = false;
        foreach ($obj as $d) {
            $arr_menu_id[] = $d->menu_id;
//            $is_menu = true;
            $arr = [];
            $arr['menu_name'] = $d->menu_name;
            $arr['necessary'] = [];
            $menu[$d->menu_id] = $arr;
        }
        $obj = DB::table('t_menu_necessary')->whereIn('menu_id', $arr_menu_id)->get();
        $arr_facility_id = [0];
        foreach ($obj as $d) {
            $arr = [];
            $arr['service_id'] = $d->service_id;  // from m_service_id
            $arr['facility_id'] = $d->facility_id; // from m_service_id
            $arr['start_minute'] = $d->start_minute;
            $arr['end_minute'] = $d->end_minute;
            $menu[$d->menu_id]['necessary'][$d->menu_necessary_id] = $arr;
            $arr_facility_id[] = $d->facility_id;
        }
        $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
        krsort($menu);
        return view('hair_salon.menu', compact('menu'));
    }
}

