<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftManagementController extends Controller {

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
        $obj = DB::table('r_routine')->where('group_id',$group_id)->get();
        $openHour = strtotime('23:59:59');
        $closeHour = strtotime('00:00:00');
        foreach ($obj as $d) {
            $i = 0;
            while ($i < 7) {
                $start = 'start_'.$i;
                $end = 'end_'.$i;
                $break_start = 'break_start_'.$i;
                $break_end = 'break_end_'.$i;
                if ($d->$start) {
                    $openHour = $openHour > strtotime($d->$start) ? strtotime($d->$start) : $openHour;
                    $closeHour = $closeHour < strtotime($d->$end) ? strtotime($d->$end) : $closeHour;
                }
                ++$i;
            }
        }
        $openHour = strtotime(date('H:00:00',$openHour));
        if (date('H:00:00',$closeHour) != date('H:i:s',$closeHour)) {
            $closeHour = strtotime(date('H:00:00',$closeHour)) + (60 * 60); // 1 hour
        }
        $openTime = date('H:00',$openHour);
        $closeTime = $closeHour - (60 * 10); 
        $closeTime = date('H:i',$closeTime);
        $frameDate->setTime(date('H',$openHour), 0, 0);
        $days7 = [];
        $i = 0;
        while ($i < 7) {
            $hour = $openHour;
            while ($hour < $closeHour) {
                $frame = 0;
                while ($frame < 6) {
                    $days7['start_'.$frameDate->format('w H:i:s')] = [];
                    $frameDate->addSecond(60 * 10);
                    ++$frame;
                }
                $hour = $hour + (60 * 60); // 1 hour
            }
            $frameDate->addDay();
            $frameDate->setTime(date('H',$openHour), 0, 0);
            ++$i;
        }
        $obj = DB::table('r_routine')->where('group_id',$group_id)->get();
        $i = 0;
        $arr_usr_id = [];
        $max = 0;
        foreach ($obj as $d) {
            $i = 0;
            while ($i < 7) {
                $t_start = 'start_'.$i;
                $t_end = 'end_'.$i;
                if ($d->$t_start) {
                    $start = new Carbon($d->$t_start);
                    $end = new Carbon($d->$t_end);
                    while ($start < $end) {
                        $days7['start_'.$i.' '.$start->format('H:i:s')][] = $d->usr_id;
                        if ( $max < count($days7['start_'.$i.' '.$start->format('H:i:s')]) ) {
                            $max = count($days7['start_'.$i.' '.$start->format('H:i:s')]);
                        }
                        $start->addSecond(60 * 10);
                    }   
                }
                ++$i;
            }
            $arr_usr_id[] = $d->usr_id;
        }
        $obj = DB::table('t_usr')->whereIn('usr_id',$arr_usr_id)->get();
        $usr_ids = [];
        foreach ($obj as $d) {
            $usr_ids[$d->usr_id] = $d->usr_name;
        }
        $usr_ids = json_encode($usr_ids);
        return view('salon.shift_management', 
                compact('days7','usr_ids','openTime','closeTime','max'));
    }
}

