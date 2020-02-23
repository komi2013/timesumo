<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class MenuEditController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $menu_id, $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);
        $menu = DB::connection('salon')->table('t_menu')->where('menu_id',$menu_id)->first();
        $request->session()->put('group_id', $menu->group_id);
        if (!in_array($usr_id,json_decode($menu->usr_ids,true))) {
            die("which menu are you ?");
            return redirect('/Auth/Sign/in/0/');
        }
        $obj = DB::connection('salon')->table('t_menu_necessary')->where('menu_id', $menu_id)->get();
        $arr_facility_id = [0];
        $final_end_min = 0;
        foreach ($obj as $d) {
            $add = [];
            $add['service_id'] = $d->service_id;
            $add['facility_id'] = $d->facility_id;
            $add['start_minute'] = $d->start_minute;
            $add['end_minute'] = $d->end_minute;
            $necessary[] = $add;
            $arr_facility_id[] = $d->facility_id;
        }
        $add['start_minute'] = $add['end_minute'];
        $add['end_minute'] = $add['end_minute'] + 20; 
        $obj = DB::table('t_facility')->where('group_id', $menu->group_id)->get();
        $facilitys = [];
        foreach ($obj as $d) {
            $facilitys[$d->facility_id] = $d->facility_name;
        }
        $obj = DB::connection('salon')->table('m_service')->get();
        $services = [];
        foreach ($obj as $d) {
            $services[$d->service_id] = $d->service_name;
        }
//        krsort($necessary);
        $necessary = json_encode($necessary);
        $facilitys = json_encode($facilitys);
        $services = json_encode($services);
        $add = json_encode($add);
        return view('hair_salon.menu_edit', compact('menu','necessary','facilitys','services','add'));
    }
}

