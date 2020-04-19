<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SheetDetailController extends Controller {

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
        $routine = DB::connection('shift')->table('r_routine')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($routine->usr_id)) {
            die('you should go to routine page');
        }
        if ($r_group->owner_flg == 0 AND $routine->approver1 == 0 AND $routine->approver2 == 0) {
            die('you have no access right');
        }

        $month = $month ?? date('Y-m');
        $thisMonth = new Carbon($month);
        $thisMonth = new Carbon($thisMonth->format('Y-m-01 00:00:00'));
        $begin = $thisMonth->format('Y-m-d H:i:s');

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
            $arr['schedule_id'] = 0;
            $date = str_pad($i + 1, 2, 0, STR_PAD_LEFT);
            $arr['day'] = __('calendar.day'.$thisMonth->format('w'));
            $arr['date'] = $date;
            $monthly[$date] = $arr;
            $thisMonth->addDay();
            ++$i;
        }

        $thisMonth->addDay();
        $obj = DB::connection('shift')->table('t_timestamp')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->where('time_in','>', $begin)
                ->where('time_in','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->orderBy('time_in','ASC')
                ->get();

        $pre_time_out = null; // null will be 19700101 
        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_in));
            $monthly[$date]['time_out'] = substr($d->time_out,11,5);
            $monthly[$date]['longitude'] = $d->longitude;
            $monthly[$date]['latitude'] = $d->latitude;
            $monthly[$date]['private_ip'] = $d->private_ip;
            $monthly[$date]['public_ip'] = $d->public_ip;
            $pre = new Carbon($pre_time_out);
            $thisIn = new Carbon($d->time_in);
            if ($pre->format('d') == $thisIn->format('d')) {
                $breakMin = $breakMin + $pre->diffInMinutes($thisIn);
            } else {
                $breakMin = 0;
                $monthly[$date]['time_in'] = substr($d->time_in, 11,5);
            }
            $monthly[$date]['break'] = $breakMin;
            $pre_time_out = $d->time_out;
        }
        $obj = DB::table('t_schedule')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->where('time_start','>', $begin)
                ->where('time_end','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->where('tag',2)
                ->orderBy('time_start','ASC')
                ->get();
        $arr_schedule_id = [];
        $todos = [];
        $leaves = [];
        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_start));
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            $breakMin = $start->diffInMinutes($end);
            $monthly[$date]['break'] = $monthly[$date]['break'] + $breakMin;
            $monthly[$date]['todo_'.$d->schedule_id] = $d->title;
            $monthly[$date]['leave_'.$d->schedule_id] = 0;
            $monthly[$date]['schedule_id'] = $d->schedule_id;
            $todos[$d->schedule_id] = $date;
            $leaves[$d->schedule_id] = $date;
            $arr_schedule_id[] = $d->schedule_id;
        }

//        $obj = DB::table('t_todo')
//                ->whereIn('schedule_id', $arr_schedule_id)
//                ->orderBy('updated_at','ASC')
//                ->get();
//        foreach ($obj as $d) {
//            $paths = json_decode($d->file_paths,true) ?? [];
//            $attachs = "";
//            foreach ($paths as $d) {
//                $attachs .= "\r\n".$d;
//            }
//            $date = $todos[$d->schedule_id];
//            $monthly[$date]['todo_'.$d->schedule_id] = "\r\n".$d->todo.$attachs."\r\n";
//        }
        
//        $obj = DB::table('t_variation')
//                ->whereIn('schedule_id', $arr_schedule_id)
//                ->where('variation_name', 'leave_id')
//                ->orderBy('updated_at','ASC')
//                ->get();
//        foreach ($obj as $d) {
//            $date = $leaves[$d->schedule_id];
//            $monthly[$date]['leave_'.$d->schedule_id] = $d->variation_value;
//        }

        foreach ($monthly as $k => $d) {
            $arr = $d;
            
            $arr['break'] = str_pad(floor($d['break'] / 60), 2, 0, STR_PAD_LEFT).':'.
                    str_pad(($d['break'] % 60), 2, 0, STR_PAD_LEFT);
            $days[] = $arr;
        }

        $month = new Carbon($month);
        return view('shift.sheet_detail', compact('days','month','target_usr'));
    }
}

