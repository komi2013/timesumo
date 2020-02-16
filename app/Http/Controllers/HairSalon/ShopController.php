<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class ShopController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,
            $action=null, $redirect='', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        \Cookie::queue('lang', $lang);
//        \App::setLocale($lang);
        
        $obj = DB::table('r_group_relate')->where('usr_id',$usr_id)->where('owner_flg',1)->get();
        $arr_group_id = [];
        $is_group_relate = false;
        foreach ($obj as $d) {
            $arr_group_id[] = $d->group_id;
            $is_group_relate = true;
        }
        $request->session()->put('group_ids', json_encode($arr_group_id));
//        if (!isset($arr_group_id[0])) {
//            return redirect('/Auth/Sign/in/0/');
//        }
//        $group = DB::table('m_group')->whereIn('group_id', $arr_group_id)->get();
        
        $shop = [];
        $shop[0]['shop_name'] = '';
        $shop[0]['seat'] = 1;
        $shop[0]['shampoo_seat'] = 0;
        $shop[0]['perm_dry'] = 0;
        
        if ($is_group_relate) {
            $obj = DB::table('m_group')->whereIn('group_id', $arr_group_id)->get();
            foreach ($obj as $d) {
                $arr['shop_name'] = $d->group_name;
                $shop[$d->group_id] = $arr;
            }
//            $shop = json_decode($obj, true);
            $obj = DB::table('t_facility')
                    ->whereIn('group_id', $arr_group_id)
                    ->orderBy('group_id','desc')->get();
            $group_id = 0;
            foreach ($obj as $d) {
                if ($group_id != $d->group_id ) {
//                    $arr = [];
                    $arr['shampoo_seat'] = 0;
                    $arr['perm_dry'] = 0;
                }
                if ($d->facility_name == 'seat') {
                    $arr['seat'] = $d->amount;
                }
                if ($d->facility_name == 'shampoo_seat') {
                    $arr['shampoo_seat'] = $d->amount;
                }
                if ($d->facility_name == 'perm_dry') {
                    $arr['perm_dry'] = $d->amount;
                }
                $arr['shop_name'] = $shop[$d->group_id]['shop_name'];
//                echo "<pre>"; var_dump($arr); echo "</pre>";
                $shop[$d->group_id] = $arr;
                $group_id = $d->group_id;
            }
//            $facility = json_decode($obj, true);
        }
//        dd($shop);
        krsort($shop);
        return view('hair_salon.shop', compact('shop'));
    }
}

