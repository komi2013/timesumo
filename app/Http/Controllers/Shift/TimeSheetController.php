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
        $i = 0;
        $monthly = [];
        $endOfMonth = $thisMonth->daysInMonth;
        while ($i < $endOfMonth) {
            $arr = [];
            $arr['time_in'] = '';
            $arr['time_out'] = '';
            $arr['note'] = '';
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
                ->get();
        foreach ($obj as $d) {
            $date = date('d',strtotime($d->time_in));
            $monthly[$date]['time_in'] = substr($d->time_in, 11,5);
            $monthly[$date]['time_out'] = substr($d->time_out,11,5);
            $monthly[$date]['note'] = $d->note;
        }
        foreach ($monthly as $k => $d) {
            $arr['time_in'] = $d['time_in'];
            $arr['time_out'] = $d['time_out'];
            $arr['note'] = $d['note'];
            $arr['day'] = $d['day'];
            $arr['date'] = $k;
            $days[] = $arr;
        }
        $month = new Carbon($month);
        return view('shift.timesheet', compact('days','month'));
    }
}

