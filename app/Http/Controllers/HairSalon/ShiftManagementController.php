<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftManagementController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $menu_id='', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
        $group_id = 1;
        \App::setLocale('ja');
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
        
//        echo $openHour.'<br>';
//        echo $closeHour.'<br>';
//        echo date('w');
//        die;
        

        $openHour = strtotime(date('H:00:00',$openHour));
        if (date('H:00:00',$closeHour) != date('H:i:s',$closeHour)) {
            $closeHour = strtotime(date('H:00:00',$closeHour)) + (60 * 60); // 1 hour
        }

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
        foreach ($obj as $d) {
            $i = 0;
            while ($i < 7) {
                $t_start = 'start_'.$i;
                $t_end = 'end_'.$i;
                if ($d->$t_start) {
                    $start = new Carbon($d->$t_start);
                    $end = new Carbon($d->$t_end);
                    while ($start < $end) {
//                        echo '<pre>'; var_dump('start_'.$i.' '.$start->format('H:i:s')); echo '</pre>';
                        $days7['start_'.$i.' '.$start->format('H:i:s')][] = $d->usr_id;
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
//        dd($days7);
        return view('hair_salon.shift_management', compact('days7','usr_ids'));
    }
}

