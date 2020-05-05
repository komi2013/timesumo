<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ShiftAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 1;

        $routine = DB::table('r_routine')
            ->where('usr_id',$usr_id)
            ->where('group_id',$group_id)
            ->first();
        if ( !isset($routine->routine_id) ) {
            $res[0] = 2;
            $res[1] = 'please make routine';
            die( json_encode($res) );
        }
        $schedule = DB::table('t_schedule')
            ->where('usr_id',$usr_id)
            ->where('group_id',$group_id)
            ->where('tag',5)
            ->orderBy('time_start','DESC')->first();
        $new = true;
        $day = new Carbon();
        $addDay = 22;
        $add_day = $addDay;
        if ( isset($schedule->schedule_id) ) {
            $new = false;
            $schedule_id = $schedule->schedule_id;
            $future = new Carbon($schedule->time_end);
            if ($future > $day) {
                $day = $future;
                $today = new Carbon();
                $add_day = $addDay -1 - $today->diffInDays($future);
                $day->addDay();
            }
        }
        $day_w = $day->format('w');
        $i = 0;
        $arr_sql = [];
        DB::beginTransaction();
        if ($new) {
            $schedule_id = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
        }
        while ($i < $add_day) {
            $w = ($i + $day_w) % 7;
            $start = 'start_'.$w;
            $end = 'end_'.$w;
            if ($routine->$start) {
                $arr_sql[] =
                    ["schedule_id" => $schedule_id
                    ,"usr_id" => $usr_id
                    ,"time_start" => $day->format('Y-m-d ').$routine->$start
                    ,"time_end" => $day->format('Y-m-d ').$routine->$end
                    ,"tag" => 5
                    ,"group_id" => $group_id
                    ,"updated_at" => now()
                    ,"editable_flg" => 0];
            }
            $break_start = 'break_start_'.$w;
            $break_end = 'break_end_'.$w;
            if ($routine->$break_start) {
                $arr_sql[] =
                    ["schedule_id" => $schedule_id
                    ,"usr_id" => $usr_id
                    ,"time_start" => $day->format('Y-m-d ').$routine->$break_start
                    ,"time_end" => $day->format('Y-m-d ').$routine->$break_end
                    ,"tag" => 2
                    ,"group_id" => $group_id
                    ,"updated_at" => now()
                    ,"editable_flg" => 0];
            }
            $day->addDay();
            ++$i;
        }
//        dd($arr_sql);
        DB::table('t_schedule')
            ->where('schedule_id',$schedule_id)
            ->where('time_end','<',now())
            ->delete();
        DB::table('t_schedule')->insert($arr_sql);
        DB::commit();
        $res[0] = 1;
        echo json_encode($res);
    }
}

