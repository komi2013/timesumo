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
        $obj = DB::connection('salon')->table('t_shop')->whereIn('group_id',$arr_group_id)->get();
        $shops = [];
        foreach ($obj as $d) {
            $shops[$d->group_id] = $d->shop_name;
            $group_id = $group_id ?: $d->group_id;
        }

        $obj = DB::connection('salon')->table('t_menu')->where('group_id',$group_id)->get();
        $menu = [];
        $menu[0]['menu_name'] = 'test';
        $arr['service_id'] = '';
        $arr['facility_id'] = '';
        $arr['service'] = 'servie';
        $arr['facility'] = 'faciii';
        $arr['start_minute'] = 0;
        $arr['end_minute'] = 30;
        $menu[0]['necessary'][0] = $arr;
        $arr_menu_id = [0];
        foreach ($obj as $d) {
            $arr_menu_id[] = $d->menu_id;
            $arr = [];
            $arr['menu_name'] = $d->menu_name;
            $arr['necessary'] = [];
            $menu[$d->menu_id] = $arr;
        }
        $obj = DB::connection('salon')->table('t_menu_necessary')->whereIn('menu_id', $arr_menu_id)->get();
        $arr_facility_id = [];
        foreach ($obj as $d) {
            $arr = [];
            $arr['service_id'] = $d->service_id;
            $arr['facility_id'] = $d->facility_id;
            $arr['start_minute'] = $d->start_minute;
            $arr['end_minute'] = $d->end_minute;
            $menu[$d->menu_id]['necessary'][$d->menu_necessary_id] = $arr;
            $arr_facility_id[] = $d->facility_id;
        }
        $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
        $facilitys = [];
        foreach ($obj as $d) {
            $facilitys[$d->facility_id] = $d->facility_name;
        }
        $obj = DB::connection('salon')->table('m_service')->get();
        $services = [];
        foreach ($obj as $d) {
            $services[$d->service_id] = $d->service_name;
        }
        foreach ($menu as $menu_id => $d) {
            foreach ($d['necessary'] as $necessary_id => $d2) {
                $d['service'] = $services[$d2['service_id']] ?? '';
                $d['facility'] = $facilitys[$d2['facility_id']] ?? '';
            }
        }
        krsort($menu);
//        dd($menu);
        return view('hair_salon.menu', compact('menu','shops','group_id'));
    }
}

