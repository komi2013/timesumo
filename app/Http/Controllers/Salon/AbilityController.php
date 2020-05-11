<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;


class AbilityController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $group = DB::table('m_group')->where('group_id',$group_id)->first();
        $arr_area_id = json_decode($group->area_id,true);
        $obj = DB::table('m_service')->whereIn('area_id',$arr_area_id)->get();
        $service = [];
        $i = 0;
        foreach ($obj as $d) {
            $arr['service_name'] = $d->service_name;
            if ( $i < 6 ) {
                $arr['ability'] = 'ability';
            } else {
                $arr['ability'] = '';
            }
            $service[$d->service_id] = $arr;
            ++$i;
        }
        $obj = DB::table('r_ability')->where('usr_id',$usr_id)->get();
        foreach ($obj as $d) {
            $service[$d->service_id]['ability'] = 'ability';
        }
        $service = json_encode($service);
        return view('salon.ability', compact('service'));
    }
}

