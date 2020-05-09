<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $today = Carbon::today();
        $frameDate = Carbon::createFromDate($today->year, $today->month, $today->startOfWeek()->format('d'));
        $shop = DB::table('m_group')->where('group_id', $group_id)->first();
        $openHour = substr($shop->open_time,0,2);
        $openMinute = substr($shop->open_time,3,2);
        $frameDate->setTime($openHour, $openMinute, 0);
        $closeHour = substr($shop->close_time,0,2);
        $days21 = [];
        $i = 0;
        while ($i < 21) {
            $days21[$frameDate->format('Y-m-d')] = [];
            $frameDate->addDay();
            ++$i;
        }
        $frameDate->subDay();
        $obj = DB::table('t_schedule')
                ->where('time_start','>=',date('Y-m-d ').$shop->open_time)
                ->where('time_end','<=',$frameDate->format('Y-m-d ').$shop->close_time)
                ->where('group_id',$group_id)
                ->where('tag',4)
                ->orderBy('time_start','ASC')->get();
        foreach ($obj as $d) {
            $days21[substr($d->time_start,0,10)][$d->schedule_id] = substr($d->time_start,11,5).' '.$d->title;
        }
        return view('salon.cancel',compact('days21','openHour','closeHour'));
    }
}

