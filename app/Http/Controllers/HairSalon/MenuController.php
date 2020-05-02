<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class MenuController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $group_id='', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);
        $obj = DB::table('r_group_relate')->where('usr_id',$usr_id)->where('owner_flg',1)->get();
        $arr_group_id = [];
        foreach ($obj as $d) {
            $arr_group_id[] = $d->group_id;
        }
        $request->session()->put('group_ids', json_encode($arr_group_id));
        $obj = DB::table('m_group')->whereIn('group_id',$arr_group_id)->get();
        $shops = [];
        foreach ($obj as $d) {
            $shops[$d->group_id] = $d->group_name;
            $group_id = $group_id ?: $d->group_id;
        }

        $obj = DB::table('t_menu')->where('group_id',$group_id)->get();
        $menu = [];
        $arr_menu_id = [0];
        foreach ($obj as $d) {
            $arr_menu_id[] = $d->menu_id;
            $arr = [];
            $arr['menu_name'] = $d->menu_name;
            $arr['necessary'] = [];
            $menu[$d->menu_id] = $arr;
        }

        $obj = DB::table('t_menu_necessary')->whereIn('menu_id', $arr_menu_id)->get();
        $arr_facility_id = [];

        foreach ($obj as $d) {
            $arr = [];
            $arr['service_id'] = $d->service_id;  // from m_service_id
            $arr['facility_id'] = $d->facility_id; // from m_service_id
            $arr['start_minute'] = $d->start_minute;
            $arr['end_minute'] = $d->end_minute;
            $menu[$d->menu_id]['necessary'][] = $arr;
            $arr_facility_id[] = $d->facility_id;
        }
        $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
        $facilitys = [];
        foreach ($obj as $d) {
            $facilitys[$d->facility_id] = $d->facility_name;
        }
        $obj = DB::table('m_service')->get();
        $services = [];
        foreach ($obj as $d) {
            $services[$d->service_id] = $d->service_name;
        }

        foreach ($menu as $menu_id => $d) {
            $final_end_min = 0;
            foreach ($d['necessary'] as $k => $d2) {
                $arr = [];
                $arr['service'] = $services[$d2['service_id']] ?? '';
                $arr['facility'] = $facilitys[$d2['facility_id']] ?? '';
                $arr['start_minute'] = $d2['start_minute'];
                $arr['end_minute'] = $d2['end_minute'];
                $menu[$menu_id]['necessary'][$k] = $arr;
                if ($final_end_min < $d2['end_minute']) {
                    $final_end_min = $d2['end_minute'];
                }
                $menu[$menu_id]['final_end_min'] = $final_end_min;
            }
        }
        $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
//dd($menu);
        return view('hair_salon.menu', compact('menu','shops','group_id'));
    }
}

