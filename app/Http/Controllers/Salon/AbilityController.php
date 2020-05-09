<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;


class AbilityController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,$action=null,
            $areas='日本', $language='') {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        \App::setLocale($request->cookie('lang'));
        Cookie::queue('areas',$areas, 60 * 24 * 365);
        $arr_area = explode(",", $areas);
        $obj = DB::table('m_service')->whereIn('area',$arr_area)->get();
        $service = [];
        $key_area = [];
        $i = 0;
        foreach ($obj as $d) {
            $arr['service_name'] = $d->service_name;
            $arr['area'] = $d->area;
            if ( $i < 6 ) {
                $arr['ability'] = 'ability';
            } else {
                $arr['ability'] = '';
            }
            $service[$d->service_id] = $arr;
            $key_area[$d->area] = 1;
            ++$i;
        }
        foreach ($key_area as $k => $d) {
            $area[] = $k;
        }
        $area = json_encode($area);
        $obj = DB::table('r_ability')->where('usr_id',$usr_id)->get();
        foreach ($obj as $d) {
            $service[$d->service_id]['ability'] = 'ability';
        }
        $service = json_encode($service);
        return view('salon.ability', compact('service','area'));
    }
}

