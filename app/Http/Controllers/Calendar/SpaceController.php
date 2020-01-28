<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpaceController extends Controller {

    public function hours12(Request $request, $directory=null, $controller=null, 
            $action=null, $date=null, $json=null) {
        $arr_usr = json_decode($json);
        $usr_ids = [];
        foreach ($arr_usr as $d) {
            $usr_ids[] = $d[0];
        }

        $obj = DB::table('t_schedule')->whereIn("usr_id", $usr_ids)
                ->whereDate('time_start', '=', $date)
                ->orWhereDate('time_end', '=', $date)
                ->orderBy('schedule_id','desc')
                ->get();
//        $schedule = [];
//        // 08:00 ~ 20:00 12 hours 
//        foreach ($obj as $d) {
//           $arr['time_start'] = $d->time_start;
//           $arr['time_end'] = $d->time_end;
//           $arr['tag'] = $d->tag;
//           $arr['usr_id'] = $d->usr_id;
//           $schedule[$d->usr_id][] = $arr;
//
//                    
//                    
//                    dd($start->diffInMinutes($end));
//        }
//        dd($schedule);
        $arr = [];
//        for ($i1 = 0; $i1 < 24; $i1++) {
//            for ($i2 = 0; $i2 < 60; $i2 += 15) {
//                $arr[] = str_pad($i1, 2, "0", STR_PAD_LEFT).':'.str_pad($i2, 2, "0", STR_PAD_LEFT);
//            }
//        }
        $s = [];
        foreach ($arr_usr as $d1) {
            foreach ($obj as $d2) {
//                echo $d1 .' == '. $d2->usr_id.'<br>';
                if ($d1[0] == $d2->usr_id){
                    $daystart = Carbon::parse($d2->time_start)->startOfDay();
                    $daystart->addHour(8);
                    $start = Carbon::parse($d2->time_start);
                    $end = Carbon::parse($d2->time_end);
                    $arr = [];
                    $arr['left'] = $daystart->diffInMinutes($start) / 2;
                    $arr['width'] = $start->diffInMinutes($end) / 2; // 1440px
                    $arr['tag'] = $d2->tag;
//                    $arr['usr_name'] = $d1[1];
                    $s[$d1[0]][] = $arr;
//                    $s[$d1[0]]['usr_name'] = $d1[1];
                }
            }
            
        }
        
        
//        $arr_usr = array_merge($arr_usr, $arr_usr);
//        $arr_usr = array_merge($arr_usr, $arr_usr);
//        $arr_usr = array_merge($arr_usr, $arr_usr);
//        $arr_usr = array_merge($arr_usr, $arr_usr);
//        $arr_usr = array_merge($arr_usr, $arr_usr);

        $u_num = (int) floor(count($arr_usr)/20) + 1;

        return view('calendar.hours12', compact('s','arr_usr','u_num'));
    }
}

