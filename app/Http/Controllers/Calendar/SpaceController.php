<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpaceController extends Controller {

    public function index(Request $request,$directory=null,$controller=null,$action=null,
            $date=null,$usrs=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $arr_usr = json_decode($usrs);
        $usr_ids = [];
        foreach ($arr_usr as $d) {
            $usr_ids[] = $d;
        }
        $routine = DB::table('r_routine')
                ->where("usr_id", $usr_id)
                ->where("group_id", $group_id)
                ->first();
        $thisDay = new Carbon($date);
        $start = 'start_'.$thisDay->format('w');
        $end = 'end_'.$thisDay->format('w');
        if ($routine->$start > $routine->$end) {
            $thisDay->addDay();
        }
        $begin = $date.' '.$routine->$start;
        $final = $thisDay->format('Y-m-d').' '.$routine->$end;
        $endRange = new Carbon($final);
        $range = new Carbon($begin);
        $axis = [];
        $left = 0;
        while ($range < $endRange) {
            $axis[] = [$range->format('H'),$left];
            $left += 30; // 1 hour 30px
            $range->addHour();
        }
        $obj = DB::table('t_usr')
            ->whereIn("usr_id", $usr_ids)
            ->get();
        $space = [];
        $top = 20;
        foreach ($obj as $d) {
            $arr['name'] = $d->usr_name;
            $arr['top'] = $top;
            $arr['schedules'] = [];
            $space[$d->usr_id] = $arr;
            $top += 30; 
        }
        $obj = DB::table('t_schedule')
                ->whereIn("usr_id", $usr_ids)
                ->where('time_start', '>=',$begin)
                ->where('time_end', '<=', $final)
                ->get();
        $begin = new Carbon($begin);
        foreach ($obj as $d) {
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            $arr['left'] = round($begin->diffInMinutes($start) /2);
            $arr['width'] = round($start->diffInMinutes($end) /2);
            $space[$d->usr_id]['schedules'][] = $arr;
        }
        return view('calendar.space', compact('space','axis'));
    }
}

