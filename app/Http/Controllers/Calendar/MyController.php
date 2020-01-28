<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MyController extends Controller {

    public function index(Request $request, $directory=null, $controller=null, 
            $action=null, $month=null) {

        $today = Carbon::parse($month.date('-d'));

        
        $tempDate = Carbon::createFromDate($today->year, $today->month, 1);
        $skip = $tempDate->dayOfWeek;

        for($i = 0; $i < $skip; $i++) {
            $tempDate->subDay();
        }
        $begin = $tempDate->format('Y-m-d 00:00:00');
        $arr_35days = [];
        while($tempDate->month <= $today->month) {
            for($i=0; $i < 7; $i++) {
                $arr = [];
                $arr_35days[$tempDate->format('Y-m-d')] = $arr;
                $tempDate->addDay();
            }
        }
        $end = $tempDate->format('Y-m-d 00:00:00');
        $bind = [
            'usr_id' => 2
            ,'begin' => $begin
            ,'end' => $end
        ];

        $obj = DB::select("SELECT * FROM t_schedule WHERE usr_id = :usr_id "
                . "AND time_end > :begin AND time_start < :end ORDER BY time_start ASC", $bind);
        $arr_schedule = [];
        foreach ($obj as $d) {
            $arr['tag'] = $d->tag;
            $arr['time_start'] = date('H:i:s', strtotime($d->time_start));
            $arr['time_end'] = date('H:i:s', strtotime($d->time_end));
            $arr['private_id'] = $d->private_id;
            $arr['agenda'] = $d->agenda;
            $arr['todo'] = $d->todo;
            $arr['file_paths'] = '';
            $arr['created_at'] = '';
            $arr['created_byname'] = '';
            $arr['updated_at'] = '';
            $arr['updated_byname'] = '';
            $date = date('Y-m-d', strtotime($d->time_start));
            $arr_schedule[$date][] = $arr;
        }
//        dd($arr_schedule);
        die(json_encode($arr_schedule));
//        return view('calendar.top', compact('arr_35days','month'));
        
    }
}

