<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class MenuEditController extends Controller {

    public function edit(Request $request,$directory,$controller,$action,
            $menu_id) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $menu = DB::table('m_menu')->where('menu_id',$menu_id)->first();
        $shop_group = DB::table('r_group_relate')
                ->where('group_id',$menu->group_id)
                ->where('usr_id',$usr_id)
                ->first();
        $request->session()->put('group_id', $menu->group_id);
        if (!$shop_group->group_relate_id) {
            $msg = 'menu_id is wrong:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));
        }
        $obj = DB::table('m_menu_necessary')->where('menu_id', $menu_id)->get();
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
        $group = DB::table('m_group')->where('group_id', $menu->group_id)->first();
        $obj = DB::table('m_service')->whereIn('area_id', json_decode($group->area_id,true))->get();
        $services = [];
        foreach ($obj as $d) {
            $services[$d->service_id] = $d->service_name;
        }
        $necessary = json_encode($necessary);
        $facilitys = json_encode($facilitys);
        $services = json_encode($services);
        $add = json_encode($add);
        return view('salon.menu_edit', compact('menu','necessary','facilitys','services','add'));
    }
}

