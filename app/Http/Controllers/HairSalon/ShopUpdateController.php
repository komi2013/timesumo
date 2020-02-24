<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ShopUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        ,group_id : g
//        ,shop_name : $('#shop_name_'+g).val()
//        ,seat : $('#seat_'+g).val()
//        ,shampoo_seat : $('#shampoo_seat_'+g).val()
//        ,digital_perm : $('#digital_perm_'+g).val()
        if ($request->group_id > 0) {
//            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            $arr_group_id = json_decode( $request->session()->get('group_ids'),true );
            if(in_array($request->group_id, $arr_group_id)){
                DB::connection('salon')->beginTransaction();
                DB::table('m_group')
                    ->where("group_id",$request->group_id)
                    ->update([
                        "group_name" => $request->shop_name
                    ]);
                $obj = DB::table('t_facility')
                        ->where('group_id', $request->group_id)
                        ->get();
                $salon_facility = new \App\Models\HairSalon\SalonFacility();
                foreach ($obj as $d) {
                    if (in_array($d->facility_name, $salon_facility->seat)) {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->seat_amount
                                ,"updated_at" => now()
                            ]);
                    }
                    if (in_array($d->facility_name, $salon_facility->shampoo_seat)) {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->shampoo_seat_amount
                                ,"updated_at" => now()
                            ]);
                    }
                    if (in_array($d->facility_name, $salon_facility->digital_perm)) {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->digital_perm_amount
                                ,"updated_at" => now()
                            ]);
                    }
                }
                DB::connection('salon')->commit();
            } else {
                //wow this guy try to do something
            }
        } else {
            $group_id = DB::select("select nextval('m_group_group_id_seq')")[0]->nextval;
            DB::connection('salon')->beginTransaction();
            DB::table('m_group')->insert([
                "group_id" => $group_id
                ,"group_name" => $request->shop_name
                ,"updated_at" => now()
            ]);
            DB::table('r_group_relate')->insert([
                "group_id" => $group_id
                ,"usr_id" => $usr_id
                ,"updated_at" => now()
                ,"owner_flg" => 1
            ]);
            DB::table('t_facility')->insert([
                "group_id" => $group_id
                ,"facility_name" => $request->seat_name
                ,"updated_at" => now()
                ,"amount" => $request->seat_amount
            ]);
            if ($request->shampoo_seat_amount > 0) {
                DB::table('t_facility')->insert([
                    "group_id" => $group_id
                    ,"facility_name" => $request->shampoo_seat_name
                    ,"updated_at" => now()
                    ,"amount" => $request->shampoo_seat_amount
                ]);   
            }
            if ($request->digital_perm_amount > 0) {
                DB::table('t_facility')->insert([
                    "group_id" => $group_id
                    ,"facility_name" => $request->digital_perm_name
                    ,"updated_at" => now()
                    ,"amount" => $request->digital_perm_amount
                ]);
            }
            DB::connection('salon')->commit();
        }
        $res[0] = 1;
        die( json_encode($res) );
    }
}

