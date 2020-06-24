<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelController extends Controller {

    public function index(Request $request, $directory, $controller,$action,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
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
        $frameDate = Carbon::createFromDate($dt->year, $dt->month, $dt->startOfWeek()->format('d'));
        $shop = DB::table('m_group')->where('group_id', $group_id)->first();
        $openHour = substr($shop->open_time,0,2);
        $openMinute = substr($shop->open_time,3,2);
        $frameDate->setTime($openHour, $openMinute, 0);
        $closeHour = substr($shop->close_time,0,2);
        $varDate = new Carbon($dt->startOfWeek()->format('Y-m-d'));
        $begin = $varDate->format('Y-m-d 00:00:00');
        $i = 0;
        while ($i < 35) {
            $day35[$varDate->format('Y-m-d')] = [];
            $varDate->addDay();
            ++$i;
        }
        $frameDate->subDay();
        $obj = DB::connection('timebook')->table('t_book')
                ->where('time_start','>=',$begin)
                ->where('time_end','<=',$frameDate->format('Y-m-d ').$shop->close_time)
                ->orderBy('time_start','ASC')->get();

        foreach ($obj as $d) {
            if ($d->group_id == $group_id AND $d->book_action == 0) {
                $day35[substr($d->time_start,0,10)][$d->book_id] = 
                        substr($d->time_start,11,5).' '.$d->usr_name;
            }
        }
        return view('salon.cancel',compact('day35','openHour','closeHour','today','prev','next'));
    }
}

