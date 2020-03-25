<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        \App::setLocale('ja');
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        if ($month) {
            $dt = new Carbon($month.'-01');
        } else {
            $dt = new Carbon(date('Y-m-01'));
        }
        $today = new Carbon();
        $varDate = new Carbon($dt->startOfWeek()->format('Y-m-d'));

        $begin = $varDate->format('Y-m-d 00:00:00');
        $i = 0;
        while ($i < 35) {
            $day35[$varDate->format('Y-m-d')] = [];
            $varDate->addDay();
            ++$i;
        }
        $end = $varDate->format('Y-m-d 00:00:00');
        $obj = DB::table('t_schedule')
                ->where('time_end','>',$begin)
                ->where('time_start','<',$varDate->format('Y-m-d H:i:s'))
                ->where('usr_id',$usr_id)
                ->orderBy('time_start','ASC')->get();
        foreach ($obj as $d) {
            $day35[substr($d->time_start,0,10)][$d->schedule_id] = $d->title;
        }

        return view('calendar.top', compact('day35','today'));
        
    }
}

