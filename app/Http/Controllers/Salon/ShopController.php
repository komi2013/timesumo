<?php
namespace App\Http\Controllers\Salon;

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
        \App::setLocale('ja');
        
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
        $shop[0]['seat'] = __('salon.seat');
        $shop[0]['seat_amount'] = 1;
        $shop[0]['shampoo_seat'] = __('salon.shampoo_seat');
        $shop[0]['shampoo_seat_amount'] = 0;
        $shop[0]['digital_perm'] = __('salon.digital_perm');
        $shop[0]['digital_perm_amount'] = 0;
        $salon_facility = new \App\My\SalonFacility();
        if ($is_group_relate) {
            $obj = DB::table('m_group')->whereIn('group_id', $arr_group_id)->get();
            foreach ($obj as $d) {
                $arr['shop_name'] = $d->group_name;
                $shop[$d->group_id] = $arr;
            }
            $obj = DB::table('t_facility')
                    ->whereIn('group_id', $arr_group_id)
                    ->orderBy('group_id','desc')->get();
            $group_id = 0;
            foreach ($obj as $d) {
                if ($group_id != $d->group_id ) {
                    $arr['shampoo_seat_amount'] = 0;
                    $arr['digital_perm_amount'] = 0;
                }
                if (in_array($d->facility_name, $salon_facility->seat)) {
                    $arr['seat'] = $d->facility_name;
                    $arr['seat_amount'] = $d->amount;
                }
                if (in_array($d->facility_name, $salon_facility->shampoo_seat)) {
                    $arr['shampoo_seat'] = $d->facility_name;
                    $arr['shampoo_seat_amount'] = $d->amount;
                }
                if (in_array($d->facility_name, $salon_facility->digital_perm)) {
                    $arr['digital_perm'] = $d->facility_name;
                    $arr['digital_perm_amount'] = $d->amount;
                }
                $arr['shop_name'] = $shop[$d->group_id]['shop_name'];
//                echo "<pre>"; var_dump($arr); echo "</pre>";
                $shop[$d->group_id] = $arr;
                $group_id = $d->group_id;
            }
        }
//        dd($shop);
        krsort($shop);
        return view('salon.shop', compact('shop'));
    }
}

