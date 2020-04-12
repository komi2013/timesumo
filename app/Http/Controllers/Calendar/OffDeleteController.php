<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OffDeleteController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 2;
        $group_id = 2;
        \App::setLocale('ja');

        $schedule_id = $request->input('schedule_id');
        $schedule = DB::table('t_schedule')->where("schedule_id", $schedule_id)->first();
        if ($schedule->usr_id != $usr_id OR $schedule->group_id != $group_id
                OR $schedule->tag != 2) {
            $res[0] = 2;
            $res[1] = 'you are not this user or group is different or tag is wrong';
            die(json_encode($res));
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
        // t_leave_amount, t_compensatory
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::connection('shift')->beginTransaction();
        if (strpos($schedule->title,'schedule') > -1) { // compensatory leave
            $leave_schedule_id = str_replace("schedule_", "", $schedule->title);
            $compensatory = DB::connection('shift')->table('t_compensatory')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('schedule_id', $leave_schedule_id)
                    ->first();
            DB::connection('shift')->table('h_compensatory')->insert([
                    "compensatory_id" => $compensatory->compensatory_id
                    ,"compensatory_status" => $compensatory->compensatory_status
                    ,"compensatory_start" => $compensatory->compensatory_start
                    ,"compensatory_end" => $compensatory->compensatory_end
                    ,"usr_id" => $compensatory->usr_id
                    ,"group_id" => $compensatory->group_id
                    ,"schedule_id" => $schedule_id
                    ,"updated_at" => $compensatory->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 0
                    ,"original_by" => 'OffDelete'
                ]);
            DB::connection('shift')->table('t_compensatory')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('schedule_id', $leave_schedule_id)
                    ->update([
                        "compensatory_status" => "1"
                        ,"updated_at" => $now
                    ]);
        }else{
            $leave_amount = DB::connection('shift')->table('t_leave_amount')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('leave_id', $schedule->title)
                    ->first();
            DB::connection('shift')->table('h_leave_amount')->insert([
                    "leave_amount_id" => $leave_amount->leave_amount_id
                    ,"usr_id" => $leave_amount->usr_id
                    ,"enable_start" => $leave_amount->enable_start
                    ,"enable_end" => $leave_amount->enable_end
                    ,"grant_days" => $leave_amount->grant_days
                    ,"used_days" => $leave_amount->used_days
                    ,"note" => $leave_amount->note
                    ,"updated_at" => $leave_amount->updated_at
                    ,"group_id" => $leave_amount->group_id
                    ,"leave_id" => $leave_amount->leave_id
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 0
                    ,"original_by" => 'OffDelete'
                ]);
            DB::connection('shift')->raw("UPDATE t_leave_amount SET "
                    . " used_days = used_days - 1"
                    . ",updated_at = ".$now
                    . " WHERE usr_id = " . $usr_id
                    . " AND group_id = " . $group_id
                    . " AND leave_id = " . $schedule->title);
        }
        DB::connection('shift')->table('h_schedule')->insert([
                "schedule_id" => $schedule_id
                ,"title" => $schedule->title
                ,"usr_id" => $schedule->usr_id
                ,"time_start" => $schedule->time_start
                ,"time_end" => $schedule->time_end
                ,"tag" => $schedule->tag
                ,"group_id" => $schedule->group_id
                ,"updated_at" => $schedule->updated_at
                ,"access_right" => $schedule->access_right
                ,"action_by" => $usr_id
                ,"action_at" => $now
                ,"action_flg" => 0
                ,"original_by" => 'OffDelete'
                ,"usr_id_json" => json_encode([$usr_id])
            ]);
        DB::table('t_schedule')->where('schedule_id', $schedule_id)->delete();
        if(isset($todo->updated_at)){
            DB::connection('shift')->table('h_todo')->insert([
                    'todo' => $todo->todo
                    ,'schedule_id' => $schedule_id
                    ,'updated_at' => $todo->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 0
                ]);
        }
        DB::table('t_todo')->where("schedule_id", $schedule_id)->delete();
        DB::connection('shift')->commit();
        DB::commit();
        $res[0] = 1;
        echo json_encode($res);
    }
}

