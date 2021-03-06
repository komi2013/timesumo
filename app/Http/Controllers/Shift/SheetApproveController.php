<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SheetApproveController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');

        $rule = DB::table('r_rule')
                ->where('usr_id', session('target_usr'))
                ->where('group_id', $group_id)
                ->get();
        $rule = json_decode($rule,true);
        if ($rule[0]['approver1'] != $usr_id AND $rule[0]['approver2'] != $usr_id) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no approver:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no approver']);
        }
        $begin = new Carbon(session('begin'));
        $begin->addSeconds();
        $end = new Carbon(session('update_end'));
        $end->addSeconds();
        $monthly = json_decode( session('monthly'),true );
        $approved_id = DB::select("select nextval('approved_id_seq')")[0]->nextval;
        $now = date('Y-m-d H:i:s');
        $timestamp = [];
        $schedules = [];
        foreach ($monthly as $date => $d) {
            $dt = new Carbon($date); 
            if ($begin <= $dt AND $dt <= $end) {
                $arr['usr_id'] = session('target_usr');
                $arr['group_id'] = $group_id;
                $arr['time_in'] = $date.' '.$d['time_in'];
                $arr['time_out'] = $date.' '.$d['time_out'];
                $arr['break_amount'] = $d['break'];
                $arr['longitude'] = $d['longitude'] ?: 0;
                $arr['latitude'] = $d['latitude'] ?: 0;
                $arr['private_ip'] = $d['private_ip'] ?: '0.0.0.0';
                $arr['public_ip'] = $d['public_ip'] ?: '0.0.0.0';
                $arr['manual_flg'] = $d['manual_flg'];
                $arr['offday'] = $d['offday'];
                $arr['overwork'] = $d['overwork'];
                $arr['offmin'] = $d['offmin'];
                $arr['overtime'] = json_encode($d['overtime']);
                $arr['routine_start'] = $d['routine_start'] ? $d['routine_start'].':00' : '00:00:00';
                $arr['routine_end'] = $d['routine_end'] ? $d['routine_end'].':00' : '00:00:00';
                $arr['schedules'] = json_encode($d['schedules'] ?? null);
                $arr['action_by'] = $usr_id;
                $arr['action_at'] = $now;
                $arr['action_flg'] = 1;
                $arr['approved_id'] = $approved_id;
                $timestamp[] = $arr;
                if (isset($d['schedules'])) {
                    foreach ($d['schedules'] as $schedule_id => $d) {
                        $schedules[] = $schedule_id;
                    }
                }
            }
        }
        $worked = json_decode( session('worked_wage'),true );
        $worked_wage = [];
        foreach ($worked as $k => $d) {
            $arr = [];
            $arr['overtime'] = $d['time'];
            $arr['extra_ratio'] = $d['ratio'];
            $arr['overtime_wage'] = $d['money'];
            $arr['overtime_title'] = $d['title'];
            $arr['extra_id'] = $d['extra_id'];
            $arr['basic'] = session('basic');
            $arr['total_overtime'] = session('ot_wage');
            $arr['total'] = session('wage');
            $arr['approved_id'] = $approved_id;
            $arr['action_at'] = $now;
            $arr['action_by'] = $usr_id;
            $worked_wage[] = $arr;
        }

        DB::beginTransaction();
        DB::table('h_timestamp')->insert($timestamp);
        DB::table('h_worked_wage')->insert($worked_wage);
        DB::table('t_schedule')
            ->whereIn("schedule_id", $schedules)
            ->update([
                'access_right' => '440'
                ,'updated_at' => $now
                ]);
        $begin = $begin->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
        DB::table('t_timestamp')
                ->where('usr_id', session('target_usr'))
                ->where('group_id', $group_id)
                ->where('time_in','>=', $begin)
                ->where('time_in','<', $end)
                ->update(['approved_id' => $approved_id]);
        DB::commit();

        $res[0] = 1;
        return json_encode($res);
    }
}

