<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeSheetController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
//        \App::setLocale('ja');
        $month = $month ?? date('Y-m');
        $thisMonth = new Carbon($month);
        $thisMonth = new Carbon($thisMonth->format('Y-m-01 00:00:00'));
        $begin = $thisMonth->format('Y-m-d H:i:s');
//        die($begin);
        $i = 0;
        $monthly = [];
        $endOfMonth = $thisMonth->daysInMonth;
        while ($i < $endOfMonth) {
            $arr = [];
            $arr['time_in'] = '';
            $arr['time_out'] = '';
            $arr['break'] = '';
            $date = str_pad($i + 1, 2, 0, STR_PAD_LEFT);
            $arr['day'] = __('calendar.day'.$thisMonth->format('w'));
            $monthly[$date] = $arr;
            $thisMonth->addDay();
            ++$i;
        }
        $thisMonth->addDay();
        $obj = DB::connection('shift')->table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('time_in','>', $begin)
                ->where('time_in','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->orderBy('time_in','ASC')
                ->get();

        $pre_time_out = null; // null will be 19700101 
        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_in));
            $monthly[$date]['time_out'] = substr($d->time_out,11,5);
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
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('time_start','>', $begin)
                ->where('time_end','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->where('tag',2)
                ->orderBy('time_start','ASC')
                ->get();
        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_start));
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            $breakMin = $start->diffInMinutes($end);
            $monthly[$date]['break'] = $monthly[$date]['break'] + $breakMin;
        }
        foreach ($monthly as $k => $d) {
            $arr = [];
            $arr['time_in'] = $d['time_in'];
            $arr['time_out'] = $d['time_out'];
            $arr['day'] = $d['day'];
            $arr['date'] = $k;
            if ($d['break']) {
                $arr['break'] = str_pad(floor($d['break']/60), 2, 0, STR_PAD_LEFT) .
                        ':' . str_pad($d['break']%60, 2, 0, STR_PAD_LEFT);
            }
            $days[] = $arr;
        }
        // r_routine, t_leave_amount, m_leave, r_extra
        $month = new Carbon($month);
        return view('shift.timesheet', compact('days','month'));
    }
}

