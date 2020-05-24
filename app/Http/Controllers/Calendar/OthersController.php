<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OthersController extends Controller {

    public function index(Request $request,$directory,$controller,$action,
            $target,$token,$month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        \App::setLocale($request->cookie('lang'));
        if (!$target) {
            return view('errors.404');
        }
        $usr = DB::table('t_usr')->where('usr_id',$target)->first();
        if ($usr->token != $token) {
            return view('errors.404');
        }
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        if ($month) {
            $dt = new Carbon($month.'-01');
        } else {
            $dt = new Carbon(date('Y-m-01'));
        }
        $today = $dt->format(__('calendar.month_f'));
        $prev = new Carbon($dt->format('Y-m-d'));
        $prev = $prev->subMonth(1);
        $prev = $prev->format('Y-m');
        $next = new Carbon($dt->format('Y-m-d'));
        $next = $next->addMonth(1);
        $next = $next->format('Y-m');
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
                ->where('usr_id',$target)
                ->orderBy('time_start','ASC')->get();
        foreach ($obj as $d) {
            $day35[substr($d->time_start,0,10)][$d->schedule_id] = [$d->public_title,$d->tag];
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            while ($start->diffInDays($end) > 0) {
                $start->addDay();
                $day35[$start->format('Y-m-d')][$d->schedule_id] = [$d->public_title,$d->tag];
            }
        }
        return view('calendar.others', compact('day35','today','prev','next'));
        
    }
}

