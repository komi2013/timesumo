<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ShiftAddController extends Controller {

    public function lessuri(Request $request, $directory,$controller) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $routine = DB::table('r_routine')
            ->where('usr_id',$usr_id)
            ->where('group_id',$group_id)
            ->first();
        $schedule = DB::table('t_schedule')
            ->where('usr_id',$usr_id)
            ->where('group_id',$group_id)
            ->where('tag',5)
            ->orderBy('time_start','DESC')->first();
        $day = new Carbon();
        $addDay = 7;
        $endDay = new Carbon();
        $endDay->addDay($addDay);
        if ( isset($schedule->schedule_id) ) {
            $day = new Carbon($schedule->time_start);
            $day->addDay();
        }
        $arr_sql = [];
        $now = date('Y-m-d H:i:s');
        $Calendar = new \App\My\Calendar();
        $shift = 'tags_'.$request->cookie('lang');
        $title =  $Calendar->$shift;
        while ($day->format('Y-m-d') < $endDay->format('Y-m-d')) {
            $start = 'start_'.$day->format('w');
            $end = 'end_'.$day->format('w');
            if ($routine->$start) {
                $arr['schedule_id'] = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
                $arr['usr_id'] = $usr_id;
                $arr['time_start'] = $day->format('Y-m-d ').$routine->$start;
                $arr['time_end'] = $day->format('Y-m-d ').$routine->$end;
                $arr['tag'] = 5;
                $arr['group_id'] = $group_id;
                $arr['updated_at'] = $now;
                $arr['access_right'] = 444;
                $arr['title'] = $title[5][0];
                $arr['public_title'] = $title[5][0];
                $arr_sql[] = $arr;
            }
            $day->addDay();
        }
        if (count($arr_sql)) {
            DB::beginTransaction();
            DB::table('t_schedule')->insert($arr_sql);
            DB::commit();            
        }
        $res[0] = 1;
        return json_encode($res);
    }
}

