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
//        ,perm_dry : $('#perm_dry_'+g).val()
        if ($request->group_id > 0) {
//            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            $arr_group_id = json_decode( $request->session()->get('group_ids'),true );
            if(in_array($request->group_id, $arr_group_id)){
                DB::connection('salon')->table('t_shop')
                    ->where("group_id",$request->group_id)
                    ->update([
                        "shop_name" => $request->shop_name
                        ,"updated_at" => now()
                    ]);
                $obj = DB::table('t_facility')
                        ->where('group_id', $request->group_id)
                        ->get();
                foreach ($obj as $d) {
                    if ($d->facility_name == 'seat') {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->seat
                                ,"updated_at" => now()
                            ]);
                    }
                    if ($d->facility_name == 'shampoo_seat') {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->shampoo_seat
                                ,"updated_at" => now()
                            ]);
                    }
                    if ($d->facility_name == 'perm_dry') {
                        DB::table('t_facility')
                            ->where("facility_id",$d->facility_id)
                            ->update([
                                "amount" => $request->perm_dry
                                ,"updated_at" => now()
                            ]);
                    }
                }
            } else {
                //wow this guy try to do something
            }
        } else {
            $group_id = DB::select("select nextval('m_group_group_id_seq')")[0]->nextval;
            DB::connection('salon')->table('t_shop')->insert([
                "group_id" => $group_id
                ,"shop_name" => $request->shop_name
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
                ,"facility_name" => 'seat'
                ,"updated_at" => now()
                ,"amount" => $request->seat
            ]);
            if ($request->shampoo_seat > 0) {
                DB::table('t_facility')->insert([
                    "group_id" => $group_id
                    ,"facility_name" => 'shampoo_seat'
                    ,"updated_at" => now()
                    ,"amount" => $request->shampoo_seat
                ]);   
            }
            if ($request->perm_dry > 0) {
                DB::table('t_facility')->insert([
                    "group_id" => $group_id
                    ,"facility_name" => 'perm_dry'
                    ,"updated_at" => now()
                    ,"amount" => $request->perm_dry
                ]);
            }
        }
        $res[0] = 1;
        die( json_encode($res) );
    }
}

