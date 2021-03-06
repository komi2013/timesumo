<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeSheetController extends Controller {

    public function index(Request $request,$directory,$controller,$action,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));

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
            $arr['break'] = '';
            $date = str_pad($i + 1, 2, 0, STR_PAD_LEFT);
            $arr['day'] = __('calendar.day'.$thisMonth->format('w'));
            $monthly[$date] = $arr;
            $thisMonth->addDay();
            ++$i;
        }
        $thisMonth->addDay();
        $obj = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('time_in','>', $begin)
                ->where('time_in','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->orderBy('time_in','ASC')
                ->get();

        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_in));
            $monthly[$date]['time_in'] = substr($d->time_in, 11,5) ?: '';
            $monthly[$date]['time_out'] = substr($d->time_out, 11,5) ?: '';
            $monthly[$date]['break'] = $d->break_amount;
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
        $month = new Carbon($month);
        return view('shift.timesheet', compact('days','month'));
    }
}

