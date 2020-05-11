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
        $group_owner = session('group_owner');

        $routine = $request->routine;
        $db_routine = json_decode(session('routine'),true);
        $now = date('Y-m-d H:i:s');
        $del_routine = [];
        foreach ($db_routine as $k => $d) {
            $db_routine[$k]['action_by'] = $usr_id;
            $db_routine[$k]['action_at'] = $now;
            $db_routine[$k]['action_flg'] = 1;
            $i = 0;
            while ($i < 7) {
                unset($db_routine[$k]['disable_'.$i]);
                ++$i;
            }
            unset($db_routine[$k]['fix_flg']);
        }
        $db_rule = json_decode(session('rule'),true);
        $del_rule = [];
        foreach ($db_rule as $k => $d) {
            $db_rule[$k]['action_by'] = $usr_id;
            $db_rule[$k]['action_at'] = $now;
            $db_rule[$k]['action_flg'] = 1;
        }
        $routine = [];
        foreach ($request->routine as $d) {
            $arr = [];
            $i = 0;
            while ($i < 7) {
                if ($d['disable_'.$i]) {
                    $arr['start_'.$i] = null;
                    $arr['end_'.$i] = null;
                } else {
                    $arr['start_'.$i] = $d['start_'.$i].':00';
                    $arr['end_'.$i] = $d['end_'.$i].':00';
                }
                ++$i;
            }
            $arr['updated_at'] = $now;
            $routine = $arr;
        }
        foreach ($request->rule as $d) {
            $arr = [];
            $arr['holiday_flg'] =  $d['holiday_flg'];
            $arr['approver1'] =  $d['approver1'];
            $arr['approver2'] =  $d['approver2'];
            $arr['compensatory_within'] =  $d['compensatory_within'];
            $arr['minimum_break'] =  $d['minimum_break'];
            $arr['break_minute'] =  $d['break_minute'];
            $arr['wage'] =  $d['wage'];
            $arr['currency'] =  $d['currency'];
            $arr['updated_at'] =  $now;
            $rule = $arr;
        }
        DB::beginTransaction();

        DB::table('h_routine')->insert($db_routine);
        DB::table('h_rule')->insert($db_rule);
        DB::table('r_routine')
                ->where("usr_id", session('target_usr'))
                ->where("group_id", $group_id)
                ->update($routine);
        DB::table('r_rule')
                ->where("usr_id", session('target_usr'))
                ->where("group_id", $group_id)
                ->update($rule);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

