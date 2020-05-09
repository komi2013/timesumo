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
        

        $facilities = json_decode(session('facilities'),true);
        $upd = [];
        $del = [];
        foreach ($request->facilities as $id => $d) {
            if ($facilities[$id]['amount'] != $d['amount']) {
                $del[] = $facilities[$id];
                $upd[] = $d;
            }
        }
        $now = date('Y-m-d H:i:s');
        foreach ($del as $k => $d) {
            $del[$k] = $d;
            $del[$k]['action_by'] = $usr_id;
            $del[$k]['action_at'] = $now;
            $del[$k]['action_flg'] = 1;
        }
        $obj = DB::table('m_group')->where("group_id",$group_id)->get();
        $group = json_decode($obj,true);
        foreach ($group as $k => $d) {
            $group[$k] = $d;
            $group[$k]['action_by'] = $usr_id;
            $group[$k]['action_at'] = $now;
            $group[$k]['action_flg'] = 1;
        }
        DB::beginTransaction();
        if ($group[0]['group_name'] != $request->shop_name) {
            DB::table('h_group')->insert($group);
            DB::table('m_group')
                ->where("group_id",$group_id)
                ->update([
                    "group_name" => $request->shop_name
                    ,"updated_at" => $now
                ]);
        }
        if (count($del)) {
            DB::table('h_facility')->insert($del);
        }
        foreach ($upd as $d) {
           DB::table('t_facility')
                ->where("facility_id",$d['facility_id'])
                ->update([
                    "amount" => $d['amount']
                    ,"updated_at" => $now
                ]);
        }
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

