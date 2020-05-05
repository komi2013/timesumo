<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;


class AbilityController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $areas='日本', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
//        $usr_id = $request->session()->get('usr_id');
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);

        $usr_id = 5;
        \App::setLocale('ja');
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
//        $service = json_encode(json_decode($service,true));
        $obj = DB::table('t_ability')->where('usr_id',$usr_id)->get();
        foreach ($obj as $d) {
            $service[$d->service_id]['ability'] = 'ability';
        }
        $service = json_encode($service);
//        $ability = json_encode(json_decode($ability,true));
        return view('hair_salon.ability', compact('service','area'));
    }
}

