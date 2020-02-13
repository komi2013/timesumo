<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class ShopController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $redirect='', $language='') {
        if (!$request->session()->get('usr_id')) {
            return redirect('/Auth/Sign/in/0/');
        }
        $usr_id = $request->session()->get('usr_id');
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);
        
        $obj = DB::table('r_group_relate')->where('usr_id',$usr_id)->where('owner_flg',1)->get();
//        $shop = json_decode($obj, true);
//        $arr_group_id = [];
        $is_group_relate = false;
        foreach ($obj as $d) {
            $arr_group_id[] = $d->group_id;
            $is_group_relate = true;
        }
//        if (!isset($arr_group_id[0])) {
//            return redirect('/Auth/Sign/in/0/');
//        }
//        $group = DB::table('m_group')->whereIn('group_id', $arr_group_id)->get();
        if ($is_group_relate) {
            $obj = DB::connection('salon')->table('t_shop')->whereIn('group_id', $arr_group_id)->get();
            $shop = json_decode($obj, true);
            $obj = DB::table('r_group_relate')->where('group_id',$arr_group_id)->get();
            $arr_facility_id = [];
            foreach ($obj as $d) {
                $arr_facility_id[] = $d->facility_id;
            }
            $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
            $facility = json_decode($obj, true);
        } else {
            $shop = [];
            $facility = [];
        }
        
        return view('hair_salon.shop', compact('shop','facility'));
    }
}

