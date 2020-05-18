<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoutineEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $req_ro = $request->routine;
        $req_ru = $request->rule;
        $target_usr = $req_ru[0]['usr_id'];
        if ($req_ro[0]['usr_id'] != $req_ru[0]['usr_id']) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('request usr is different:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'request usr is different']);
        }
        $rule = DB::table('r_rule')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->get();
        $rule = json_decode($rule,true);
        if ($rule[0]['approver1'] != $usr_id AND $rule[0]['approver2'] != $usr_id) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no approver:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no approver']);
        }
        $now = date('Y-m-d H:i:s');
        foreach ($rule as $k => $d) {
            $rule[$k]['action_by'] = $usr_id;
            $rule[$k]['action_at'] = $now;
            $rule[$k]['action_flg'] = 1;
        }
        $obj = DB::table('r_routine')
                ->where('group_id', $group_id)
                ->where('usr_id', $target_usr)
                ->get();
        $routine = json_decode($obj,true);
        foreach ($routine as $k => $d) {
            $routine[$k]['action_by'] = $usr_id;
            $routine[$k]['action_at'] = $now;
            $routine[$k]['action_flg'] = 1;
        }
        $upd_ro = [];
        foreach ($req_ro as $k => $d) {
            $upd_ro = $d;
            $i = 0;
            while ($i < 7) {
                if ($upd_ro['disable_'.$i]) {
                    $upd_ro['start_'.$i] = null;
                    $upd_ro['end_'.$i] = null;
                } else {
                    $upd_ro['start_'.$i] .= ':00';
                    $upd_ro['end_'.$i] .= ':00';
                }
                unset($upd_ro['disable_'.$i]);
                ++$i;
            }
            $upd_ro['updated_at'] = $now;
        }
        $upd_ru = [];
        foreach ($req_ru as $k => $d) {
            $upd_ru = $d;
            $upd_ru['updated_at'] = $now;
            $upd_ru['holiday_flg'] = ($d['holiday_flg'] == 'true' OR $d['holiday_flg'] == 1) ? 1 : 0;
        }

        DB::beginTransaction();

        DB::table('h_routine')->insert($routine);
        DB::table('h_rule')->insert($rule);
        DB::table('r_routine')
                ->where("usr_id", $target_usr)
                ->where("group_id", $group_id)
                ->update($upd_ro);
        DB::table('r_rule')
                ->where("usr_id", $target_usr)
                ->where("group_id", $group_id)
                ->update($upd_ru);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

