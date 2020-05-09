<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ShopUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        if ($request->group_id > 0) {
            $arr_group_id = json_decode( $request->session()->get('group_ids'),true );
            if(in_array($request->group_id, $arr_group_id)){
                DB::beginTransaction();
                DB::table('m_group')
                    ->where("group_id",$request->group_id)
                    ->update([
                        "group_name" => $request->shop_name
                    ]);
                $obj = DB::table('t_facility')
                        ->where('group_id', $request->group_id)
                        ->get();
                $salon_facility = new \App\My\SalonFacility();
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
                DB::commit();
            } else {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('group is different:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'group is different']);
            }
        } else {
            $group_id = DB::select("select nextval('m_group_group_id_seq')")[0]->nextval;
            DB::beginTransaction();
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
            DB::commit();
        }
        $res[0] = 1;
        return json_encode($res);
    }
}

