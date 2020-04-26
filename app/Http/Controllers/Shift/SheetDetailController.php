<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SheetDetailController extends Controller {

    public $schedules = [];
    public $wage = [];
    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null,$target_usr=0) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        \App::setLocale('ja');

        $r_group = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($r_group->usr_id)) {
            die('you should belong group at first');
        }
        $rule = DB::connection('shift')->table('r_rule')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($rule->usr_id)) {
            die('you should go to rule page');
        }
        if ($r_group->owner_flg == 0 AND $rule->approver1 == 0 AND $rule->approver2 == 0 AND $usr_id != $target_usr) {
            die('you have no access right');
        }

        $month = $month ?? date('Y-m');
        $thisMonth = new Carbon($month);
        $thisMonth = new Carbon($thisMonth->format('Y-m-01 00:00:00'));
        $begin = $thisMonth->format('Y-m-d H:i:s');
        $date_begin = $thisMonth->format('Y-m-d');
        $i = 0;
        $monthly = [];
        $endOfMonth = $thisMonth->daysInMonth;
        while ($i < $endOfMonth) {
            $arr = [];
            $arr['time_in'] = '';
            $arr['time_out'] = '';
            $arr['break'] = 0;
            $arr['longitude'] = '';
            $arr['latitude'] = '';
            $arr['private_ip'] = '';
            $arr['public_ip'] = '';
            $arr['manual_flg'] = 0;
            $date = $thisMonth->format('Y-m-d');
            $arr['day'] = __('calendar.day'.$thisMonth->format('w'));
            $arr['date'] = $thisMonth->format('d');
            $arr['offday'] = 0;
            $arr['overwork'] = 0;
            $arr['offmin'] = 0;
            $arr['overtime'] = [];
            $monthly[$date] = $arr;
            $thisMonth->addDay();
            ++$i;
        }
        $thisMonth->addDay();
        
        $routine = DB::connection('shift')->table('r_routine')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->first();
        foreach ($monthly as $date => $d) {
            $dt = new Carbon($date);
            $start = 'start_'.$dt->format('w');
            $end = 'end_'.$dt->format('w');
            $monthly[$date]['offday'] = $routine->$start ? 0 : 1 ;
            $monthly[$date]['routine_start'] = substr($routine->$start,0,5);
            $monthly[$date]['routine_end'] = substr($routine->$end,0,5);
        }
        $obj = DB::table('c_holiday')
                ->where('country', 'jp')
                ->where('holiday_date','>', $begin)
                ->where('holiday_date','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->get();
        foreach ($obj as $d) {
            $dt = new Carbon($d->holiday_date);
            $monthly[$dt->format('Y-m-d')]['offday'] = 1;
        }

        $obj = DB::connection('shift')->table('t_timestamp')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->where('time_in','>', $begin)
                ->where('time_in','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->orderBy('time_in','ASC')
                ->get();
        $approved_id = 0;
        foreach ($obj as $d) {
            $date = date('Y-m-d',strtotime($d->time_in));
            $monthly[$date]['time_in'] = substr($d->time_in,11,5);
            $monthly[$date]['time_out'] = substr($d->time_out,11,5);
            $monthly[$date]['longitude'] = $d->longitude;
            $monthly[$date]['latitude'] = $d->latitude;
            $monthly[$date]['private_ip'] = $d->private_ip;
            $monthly[$date]['public_ip'] = $d->public_ip;
            $monthly[$date]['manual_flg'] = $d->manual_flg;
            $monthly[$date]['break'] = $d->break_amount;
//            if ($d->approved_id) {
//                $approved_id = $d->approved_id;
//                $begin = $date.' 23:59:59';
//                $date_begin = $date;
//            }
        }
//        if ($approved_id) {
//            $monthly = $this->approved($approved_id,$monthly);
//            $total_wage[] = $this->wage;
//        }
        $approveButton = 1;
        $start = new Carbon($begin);
        $end = new Carbon($thisMonth->format('Y-m-01 00:00:00'));
        if ( $start->diffInHours($end) > 24 ) {
            $schedule = DB::table('t_schedule')
                    ->where('usr_id', $target_usr)
                    ->where('group_id', $group_id)
                    ->where('time_start','>', $begin)
                    ->where('time_end','<', $thisMonth->format('Y-m-01 00:00:00'))
                    ->orderBy('time_start','ASC')
                    ->get();
        } else {
            $schedule = [];
            $approveButton = 2;
        }
        $extra = DB::connection('shift')->table('r_extra')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->get();
        $overhour = [[1000,0],[1000,0],[1000,0],[1000,0]];
        foreach ($schedule as $d) {
            $date = date('Y-m-d',strtotime($d->time_start));
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            $arr = [];
            if ($d->tag == 2) {
                $offmin = $start->diffInMinutes($end);
                $monthly[$date]['offmin'] += $offmin;
//                $monthly[$date]['todo_'.$d->schedule_id] = $d->title;
                $arr[$d->schedule_id] = $d->title;
                $monthly[$date]['schedules'][$d->schedule_id] =  $d->title;
                $this->schedules[$d->schedule_id] = 1;
            } else if ($d->tag != 6 OR $d->tag != 3) { //6 = service, 3 = out
                foreach ($extra as $d2) {
                    $extra_start = new Carbon(date('Y-m-d ',strtotime($d->time_start)).$d2->extra_start);
                    $extra_end = new Carbon(date('Y-m-d ',strtotime($d->time_start)).$d2->extra_end);
                    if ($extra_end->format('H:i:s') == '23:59:59') {
                         $extra_end->addMinute();
                    }
                    if ($date != date('Y-m-d',strtotime($d->time_end))) {
                        $next_start = new Carbon(date('Y-m-d ',strtotime($d->time_end)).$d2->extra_start);
                        $next_end = new Carbon(date('Y-m-d ',strtotime($d->time_end)).$d2->extra_end);
                    }
                    if (isset($next_end) AND $d2->over_flg == 0) {
                        $monthly = $this->punctuate($start,$end,$next_start,$next_end,$monthly,$date,$d,$d2);
                    }
                    if ($monthly[$date]['offday'] AND $d2->dayoff_flg) {
                        $monthly = $this->punctuate($start,$end,$extra_start,$extra_end,$monthly,$date,$d,$d2);
                    } else if ($d2->over_flg == 0 AND !$monthly[$date]['offday'] AND !$d2->dayoff_flg ) {
                        $monthly = $this->punctuate($start,$end,$extra_start,$extra_end,$monthly,$date,$d,$d2);
                    }
                    $arr = [];
                    if ($d2->over_flg > 0) {
                        $arr['ratio'] = $d2->extra_ratio;
                        $arr['hour_start'] = $d2->hour_start;
                        $arr['extra_id'] = $d2->extra_id;
                        $overhour[$d2->over_flg] = $arr;
//                        $overhour[$d2->over_flg] = [$d2->hour_start,$d2->extra_ratio];
                    } else {
                        $arr['ratio'] = $d2->extra_ratio;
                        $arr['extra_start'] = $d2->extra_start;
                        $arr['extra_end'] = $d2->extra_end;
                        $arr['range'] = $d2->extra_end;
                        $arr['dayoff_flg'] = $d2->dayoff_flg;
                        $range[$d2->extra_id] = $arr;
                    }
                }
            }
        }
        $request->session()->flash('monthly', json_encode($monthly));
        $worked_wage = [];
        $monthMin = 0;

        foreach ($monthly as $date => $d) {

            $arr = $d;
            $in = new Carbon($d['time_in']);
            $out = new Carbon($d['time_out']);
            $min = $in->diffInMinutes($out);
            $workMin = $min - $d['break'];

            if ($date_begin < $date) {
//                var_dump($date);
//                var_dump($d);
//                var_dump($overhour[3][0]);
//                var_dump(floor($workMin/60));
                if (isset($overhour[3][0]) AND $overhour[3]['hour_start'] * 60 < $workMin) {
                    $minute = $workMin - $overhour[3]['hour_start'];
                    if (isset($worked_wage['day']['time'])) {
                        $worked_wage['day']['time'] += $minute;
                    } else {
                        $worked_wage['day'] = $minute;
//                        $worked_wage['day'] = [$overhour[3][1],$minute];
                    }
                }
//                var_dump($date);
//                var_dump($d['overtime']);
                foreach ($d['overtime'] as $extra_id => $extra_time) {
                    if (isset($rangeFee[$extra_id])) {
                        $rangeFee[$extra_id] += $extra_time;
                    } else {
                        $rangeFee[$extra_id] = $extra_time;
                    }
                }

                $monthMin += $workMin;

            }
            $arr['overwork'] = $arr['overwork'] ? $this->min2time($arr['overwork']) : '' ;
            if ( ($rule->minimum_break * 60) < $workMin AND $rule->break_minute > $d['break']) {
                $arr['break'] = $this->min2time($rule->break_minute);
            } else if ($workMin > 0) {
                $arr['break'] = $this->min2time($d['break']);
            } else if ($workMin == 0) {
                $arr['break'] = '';
            }

            $days[$date] = $arr;
        }
        
        $month = new Carbon($month);
        $button = 0;
        if( isset($rangeFee) ){
            $arr = [];
            foreach ($rangeFee as $extra_id => $extra_time) {
                if (!$range[$extra_id]['dayoff_flg']) {
                    $arr['time'] = $this->min2time($extra_time);
                    $arr['ratio'] = $range[$extra_id]['ratio'];
                    $arr['money'] = round($extra_time / 60 * $arr['ratio'] * $rule->wage);
//                    $arr['extra_start'] = $range[$extra_id]['extra_start'];
//                    $arr['extra_end'] = $range[$extra_id]['extra_end'];
                    $arr['title'] = substr($range[$extra_id]['extra_start'],0,5).'~'.substr($range[$extra_id]['extra_end'],0,5);
                    $arr['extra_id'] = $extra_id;
                    $worked_wage[$extra_id] = $arr;
                }
            }
        }
        if (isset($worked_wage['day'])) {
            $worked_wage['day']['time'] = $this->min2time($worked_wage['day']['time']);
            $worked_wage['day']['ratio'] = $overhour[3]['ratio'];
            $worked_wage['day']['money'] = round($worked_wage['day']['time'] /60 * $worked_wage['day']['ratio'] * $rule->wage);
            $worked_wage['day']['title'] = __('calendar.day');
            $worked_wage['day']['extra_id'] = $overhour[3]['extra_id'];
        }
        
        if (isset($overhour[1]['hour_start']) AND $overhour[1]['hour_start'] * 60 < $monthMin) {
            $worked_wage['month']['time'] = $this->min2time($monthMin - $overhour[1]['hour_start']*60);
            $worked_wage['month']['ratio'] = $overhour[1]['ratio'];
            $worked_wage['month']['money'] = round(($monthMin - $overhour[1]['hour_start']*60) /60 * $overhour[1]['ratio'] * $rule->wage);
            $worked_wage['month']['title'] = __('calendar.month');
            $worked_wage['month']['extra_id'] = $overhour[1]['extra_id'];
        }

        $ot_wage = 0;
        foreach ($worked_wage as $k => $d) {
            $ot_wage += $d['money'];
        }
        var_dump($ot_wage);
        $basic = round($monthMin /60 * $rule->wage);
        $wage = $basic + $ot_wage;
        $arr = [];
        $arr['basic'] = $basic;
        $arr['worked_wage'] = $worked_wage;
        $arr['ot_wage'] = $ot_wage;
        $arr['wage'] = $wage;
        $total_wage[] = $arr;
//        $request->session()->flash('month', $month->format('Y-m'));
        $request->session()->flash('begin', $begin);
        $request->session()->flash('end', $thisMonth->format('Y-m-01 00:00:00'));
        $request->session()->flash('target_usr', $target_usr);
        $worked_wage = json_encode($worked_wage);
        $request->session()->flash('worked_wage', $worked_wage);
        $request->session()->flash('basic', $basic);
        $request->session()->flash('ot_wage', $ot_wage);
        $request->session()->flash('wage', $wage);
        $schedules = json_encode(array_flip($this->schedules));
        $request->session()->flash('schedules', $schedules);

//        }

//        dd($total_wage);
        $days = json_encode($days);
        $total_wage = json_encode($total_wage);
        return view('shift.sheet_detail', compact('month','days','total_wage','approveButton'));
    }
    public function punctuate ($start,$end,$extra_start,$extra_end,$monthly,$date,$d,$d2) {
        $time = 0;
        if        ($extra_start <= $start AND $end <= $extra_end) {
            $time = $start->diffInMinutes($end);
        } else if ($extra_start <= $start AND $start <= $extra_end AND $extra_end <= $end) {
            $time = $start->diffInMinutes($extra_end);
        } else if ($start <= $extra_start AND $extra_end <= $end) {
            $time = $extra_start->diffInMinutes($extra_end);
        } else if ($start <= $extra_start AND $extra_start <= $end AND $end <= $extra_end) {
            $time = $extra_start->diffInMinutes($end);
        }
        if ($time) {
            $monthly[$date]['overwork'] += $time;
            if (isset($monthly[$date]['overtime'][$d2->extra_id])) {
                $monthly[$date]['overtime'][$d2->extra_id] += $time;
            } else {
                $monthly[$date]['overtime'][$d2->extra_id] = $time;
            }
            $monthly[$date]['schedules'][$d->schedule_id] = $d->title;
            $this->schedules[$d->schedule_id] = 1;
        }
        
        return $monthly;
    }
    public function min2time ($minutes) {
        $time = str_pad(floor($minutes / 60), 2, 0, STR_PAD_LEFT).':'.
                str_pad(($minutes % 60), 2, 0, STR_PAD_LEFT);
        return $time;
    }
    public function approved ($approved_id,$monthly) {
        $approved = DB::connection('shift')->table('h_approved')
                ->where('approved_id', $approved_id)
                ->orderBy('approved_id','DESC')
                ->first();
        $monthly_db = json_decode($approved->monthly,true);
        foreach ($monthly_db as $date => $d) {
            $monthly[$date] = $d;
        }
        $arr = [];
        $arr['basic'] = json_decode($approved->basic,true);
        $arr['worked_wage'] = json_decode($approved->worked_wage,true);
        $arr['ot_wage'] = json_decode($approved->ot_wage,true);
        $arr['wage'] = json_decode($approved->wage,true);
        $this->wage = $arr;
        return $monthly;
    }
}

