<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleDeleteController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 10;
        $arr3 = [];
//        $usrs = $request->input('usrs');
        $schedule_id = $request->input('schedule_id');

        $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
        $mydata = false;
        $overwrite = false;
        foreach ($obj as $d) {
            if ($d->usr_id == $usr_id) {
                $mydata = true;
            }
            $lastUpdate = new Carbon($d->updated_at);
            $viewTime = new Carbon($request->session()->get('view_time'));
            if ( $lastUpdate->gt($viewTime) ) {
                $overwrite = true;
            }
            if ($overwrite) {
                $res[0] = 2;
                $res[1] = 'somebody overwrite this schedule, please refresh page and submit again';
                die(json_encode($res));
            }
            $time_start = $d->time_start;
            $time_end = $d->time_end;
            $title = $d->title;
            $tag = $d->tag;
            $editable_flg = $d->editable_flg;
            $group_id = $d->group_id;
            $updated_at = $d->updated_at;
            $arr = [];
            $arr['public_tag'] = $public_tag = $d->public_tag;
            $arr['public_title'] = $public_title = $d->public_title;
            $db[$d->usr_id] = $arr;

            $usr_ids[] = $d->usr_id;
        }
        if (!$mydata AND $editable_flg == 0) {
            $res[0] = 2;
            $res[1] = 'you can not update because you can not change others schedule';
            die(json_encode($res));
        }
        $is = DB::table('r_group_relate')
                ->where("group_id", $group_id)
                ->where("usr_id", $usr_id)
                ->first();
        if (!isset($is->usr_id)) {
            $res[0] = 2;
            $res[1] = 'you can not update because you can not change others schedule';
            die(json_encode($res));
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
//        if (isset($todo->schedule_id) AND $request->input('todo') == $todo->todo) {
//            $same = true;
//        }
//        foreach ($usrs as $d) {
//            $schedule[$d]['time_start'] = $request->input('time_start');
//            $schedule[$d]['time_end'] = $request->input('time_end');
//            $schedule[$d]['title'] = $request->input('title');
//            $schedule[$d]['tag'] = $request->input('tag');
//            $schedule[$d]['usr_id'] = $d;
//            $schedule[$d]['schedule_id'] = $schedule_id;
//            $schedule[$d]['group_id'] = $request->input('group_id');
//            if ($d != $usr_id AND isset($db[$d]['public_tag'])) {
//                $schedule[$d]['public_tag'] = $db[$d]['public_tag'];
//            } else {
//                $schedule[$d]['public_tag'] = $request->input('public_tag') ?? '';
//            }
//            if ($d != $usr_id AND isset($db[$d]['public_title'])) {
//                $schedule[$d]['public_title'] = $db[$d]['public_title'];
//            } else {
//                $schedule[$d]['public_title'] = $request->input('public_title') ?? '';
//            }
//            $schedule[$d]['updated_at'] = now();
//        }
        DB::beginTransaction();
        DB::connection('shift')->beginTransaction();
        DB::connection('shift')->table('h_schedule')->insert([
                "schedule_id" => $schedule_id
                ,"title" => $title
                ,"usr_id" => $usr_id
                ,"time_start" => $time_start
                ,"time_end" => $time_end
                ,"tag" => $tag
                ,"group_id" => $group_id
                ,"updated_at" => $updated_at
                ,"editable_flg" => $editable_flg
                ,"action_by" => $usr_id
                ,"action_at" => date('Y-m-d H:i:s')
                ,"action_flg" => 0
                ,"original_by" => 'ScheduleDelete'
                ,"usr_id_json" => json_encode($usr_ids)
            ]);
        DB::table('t_schedule')->where('schedule_id', $request->input('schedule_id'))->delete();
//        DB::table('t_schedule')->insert($schedule);
//        if(!isset($same)){
        if(isset($todo->updated_at)){
            DB::connection('shift')->table('h_todo')->insert([
                    'todo' => $todo->todo
                    ,'schedule_id' => $schedule_id
                    ,'updated_at' => $todo->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => date('Y-m-d H:i:s')
                    ,"action_flg" => 1
                ]);
        }
        DB::table('t_todo')->where("schedule_id", $schedule_id)->delete();
//        if ($request->input('todo')) {
//            DB::table('t_todo')->insert([
//                'todo' => $request->input('todo'),
//                'schedule_id' => $schedule_id,
//                'updated_at' => now()
//            ]);
//        }   
//        }
        DB::connection('shift')->commit();
        DB::commit();
        $res[0] = 1;
        echo json_encode($res);
    }
}

