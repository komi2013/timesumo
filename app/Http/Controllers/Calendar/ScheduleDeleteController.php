<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleDeleteController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 10;
        $group_id = 2;
        $arr3 = [];

        $schedule_id = $request->input('schedule_id');

        $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
        $mydata = false;
        $overwrite = false;
        $access_right = 0;
        foreach ($obj as $d) {
            if ($d->usr_id == $usr_id AND $access_right < substr($d->access_right,0,1)) {
                $access_right = substr($d->access_right,0,1);
            } else if(session('mystaff') == $usr_id AND $access_right < substr($d->access_right,1,1)) {
                $access_right = substr($d->access_right,1,1);
            } else if ($group_id == $d->group_id AND $access_right < substr($d->access_right,2,1)) {
                $access_right = substr($d->access_right,2,1);
            } else {
                die('you are not part of this group');
            }
            $time_start = $d->time_start;
            $time_end = $d->time_end;
            $title = $d->title;
            $tag = $d->tag;
            $access_right = $d->access_right;
            $group_id = $d->group_id;
            $updated_at = $d->updated_at;
            $arr = [];
            $arr['public_title'] = $public_title = $d->public_title;
            $db[$d->usr_id] = $arr;
            $usr_ids[] = $d->usr_id;
            $accessRight = $d->access_right;
        }
        if ($access_right < 6) {
            die('you can not update because you can not change others schedule');
        }
        $paths = json_decode($request->input('file_paths'),true) ?: [];
        foreach ($paths as $d) {
            Storage::deleteDirectory("public/".substr($d[0],5,strrpos($d[0], "/")-5));
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::beginTransaction();
        DB::table('h_schedule')->insert([
                "schedule_id" => $schedule_id
                ,"title" => $title
                ,"usr_id" => $usr_id
                ,"time_start" => $time_start
                ,"time_end" => $time_end
                ,"tag" => $tag
                ,"group_id" => $group_id
                ,"updated_at" => $updated_at
                ,"access_right" => $accessRight
                ,"action_by" => $usr_id
                ,"action_at" => $now
                ,"action_flg" => 0
                ,"original_by" => 'ScheduleDelete'
                ,"usr_id_json" => json_encode($usr_ids)
            ]);
        DB::table('t_schedule')->where('schedule_id', $request->input('schedule_id'))->delete();
        if(isset($todo->updated_at)){
            DB::table('h_todo')->insert([
                    'todo' => $todo->todo
                    ,'schedule_id' => $schedule_id
                    ,'updated_at' => $todo->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 1
                ]);
        }
        DB::table('t_todo')->where("schedule_id", $schedule_id)->delete();
        DB::commit();
        DB::commit();
        $res[0] = 1;
        echo json_encode($res);
    }
}

