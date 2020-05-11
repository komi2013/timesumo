<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OffGetController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        
        $request->session()->reflash();
        
        $timestamp = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('approved_id','>', 0)
                ->orderBy('time_in','DESC')
                ->first();
        $begin = $timestamp->time_out ?? '2000-01-01 00:00:00';
        $obj = DB::table('t_schedule')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('time_start','>', $begin)
                ->where('tag',2)
                ->orderBy('time_start','ASC')
                ->get();
                
        $schedule = [];
        $arr_schedule_id = [];
        foreach ($obj as $d) {
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            $arr = [];
            $arr['time_start'] = $d->time_start;
            $arr['time_end'] = $d->time_end;
            $schedule[$d->schedule_id] = $arr;
            $arr_schedule_id[] = $d->schedule_id;
        }
        $obj = DB::table('t_variation')->whereIn('schedule_id', $arr_schedule_id)->get();
        $leave = [];
        $leave_id = 0;
        foreach ($obj as $d) {
            $start = new Carbon($schedule[$d->schedule_id]['time_start']);
            $end = new Carbon($schedule[$d->schedule_id]['time_end']);
            $amount = $start->diffInDays($end) + 1;
            if ($d->variation_name == 'leave_id') {
                if ($d->schedule_id == $request->schedule_id) {
                    $leave_id = $d->variation_value;
                } else if ( isset($leave[$d->variation_value])  ) {
                    $leave[$d->variation_value] = $leave[$d->variation_value] + $amount;
                } else {
                    $leave[$d->variation_value] = $amount;
                }   
            }
        }
        $obj = DB::table('m_leave')
                ->where("group_id", $group_id)
                ->get();
        foreach ($obj as $d) {
            $arr = [];
            if ( !$d->leave_amount_flg ) {
                $arr['available'] = $d->leave_days;
                $arr['enable_end'] = '2099-01-01';
            } else {
                $arr['available'] = 0;
                $arr['enable_end'] = '2000-01-01';
            }
            $arr['leave_amount_flg'] = $d->leave_amount_flg;
            $arr['leave_id'] = $d->leave_id;
            $arr['leave_name'] = $d->leave_name;
            $arr['prove_flg'] = $d->prove_flg;
            $leave[$d->leave_id] = $arr;
            $arr_leave_id[] = $d->leave_id;
        }
        $obj = DB::table('t_leave_amount')
                ->where("usr_id", $usr_id)
                ->whereIn("leave_id", $arr_leave_id)
                ->get();
        $thisTime = new Carbon($request->date);
        foreach ($obj as $d) {
            $start = new Carbon($d->enable_start);
            $end = new Carbon($d->enable_end);
            $available = $d->grant_days - $d->used_days;
            if ($thisTime > $start AND $thisTime < $end AND $available > 0) {
                $leave[$d->leave_id]['available'] = $available;
                $leave[$d->leave_id]['enable_end'] = $d->enable_end;
            }
        }
        $routine = DB::table('r_routine')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        $obj = DB::table('t_compensatory')
            ->where('usr_id', $usr_id)
            ->where('group_id', $group_id)
            ->where('compensatory_start', '<', $request->date)
            ->where('compensatory_end', '>=', $request->date)
            ->get();
        foreach ($obj as $d) {
            $arr = [];
            $arr['leave_id'] = 'schedule_'.$d->schedule_id;
            if ( ('schedule_'.$d->schedule_id == $leave_id
                    OR $d->compensatory_days > 0 OR $d->compensatory_hours > 0 )
                    AND $routine->fix_flg == 0) {
                $arr['available'] = 1;
            } else {
                $arr['available'] = 0;
            }
            $arr['enable_end'] = $d->compensatory_end;
            $arr['leave_name'] = __('calendar.compensatory').'('.
                    date(__('calendar.date'),strtotime($d->compensatory_start)).')';
            $arr['prove_flg'] = 0;
            $leave[] = $arr;
        }
        $k = 0;
        $arr = $leave;
        $leave = [];
        foreach ($arr as $d) {
            if ($d['available'] > 0) {
                $leave[$k] = $d;
                $enable_ends[$k] = $d['enable_end'];
                ++$k;                
            }
        }

        array_multisort($enable_ends, SORT_ASC, $leave);

        $thisDay = new Carbon($request->date);
        $begin = $thisDay->format('Y-m-d');
        $i = 0;
        while ($i < 60) {
            $day = 'start_'.$thisDay->format('w');
            if ($routine->$day) {
                $next[] = $thisDay->format('Y-m-d');
                ++$i;
                
            }
            $thisDay->addDay();
        }
        $rule = DB::table('r_rule')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();

        if ($rule->holiday_flg == 1) {
            $obj = DB::table('c_holiday')->select('holiday_date')
                    ->where('country', 'jp')
                    ->where('holiday_date','>', $begin)
                    ->where('holiday_date','<=', $thisDay->format('Y-m-d'))
                    ->get();
            $holidays = [];
            foreach ($obj as $d) {
                $holidays[] = $d->holiday_date;
            }
            
            $arr = $next;
            $next = [];
            foreach ($arr as $d) {
                if (!in_array($d,$holidays)) {
                    $next[] = [date('Y-m-d',strtotime($d)),
                        date(__('calendar.date'),strtotime($d))];
                }
            }
        }

        $res[0] = 1;
        $res[1] = $leave;
        $res[2] = $leave_id > 0 ? $leave_id : $leave[0]['leave_id'];
        $res[3] = $next;
        echo json_encode($res);

    }
}

