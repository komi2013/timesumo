<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');

        $now = date('Y-m-d H:i:s');
        $targets = [];
        foreach ($request->extra as $k => $d) {
            if( $d['group_id'] != $group_id ) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('group is different:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'group is different']);
            }
            $add[$k] = $d;
            $add[$k]['dayoff_flg'] = ($d['dayoff_flg'] == 'true' OR $d['dayoff_flg'] == 1) ? 1 : 0;
            $add[$k]['over_flg'] = ($d['over_flg'] == 'true' OR $d['over_flg'] == 1) ? 1 : 0;
            if ($d['extra_start']) {
                $add[$k]['extra_start'] = $d['extra_start'].':00';
                $add[$k]['extra_end'] = $d['extra_end'].':00';                
            } else {
                $add[$k]['extra_start'] = null;
                $add[$k]['extra_end'] = null;
            }
            $add[$k]['updated_at'] = $now;
            $target_usr = $d['usr_id'];
            $targets[$d['usr_id']] = 0;
        }
        if (count($targets) != 1) {
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
        $del = DB::table('r_extra')
                ->where('group_id', $group_id)
                ->where('usr_id', $target_usr)
                ->get();
        $del = json_decode($del,true);
        $arr_extra_id = [];
        foreach ($del as $k => $d) {
            $del[$k]['action_by'] = $usr_id;
            $del[$k]['action_at'] = $now;
            $del[$k]['action_flg'] = 1;
            $del[$k]['original_by'] = 'ExtraEditController';
            $arr_extra_id[] = $d['extra_id'];
        }
        DB::beginTransaction();
        DB::table('h_extra')->insert($del);
        DB::table('r_extra')
                ->whereIn("extra_id", $arr_extra_id)->delete();
        DB::commit();
        DB::table('r_extra')->insert($add);
        $res[0] = 1;
        return json_encode($res);
    }
}

