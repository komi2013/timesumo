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
        $obj = DB::table('t_variation')->where("schedule_id", $schedule_id)->get();
        foreach ($obj as $d) {
            if ($d->variation_name == 'leave_id') {
                $leave_id = $d->variation_value;
                $variation_category = $d->variation_category;
                $variation_updated_at = $d->updated_at;
                $variation_id = $d->variation_id;
                
            }
        }
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::connection('shift')->beginTransaction();
        
        if (strpos($leave_id,'schedule') > -1) { // compensatory leave
            $leave_schedule_id = str_replace("schedule_", "", $leave_id);
            $compensatory = DB::connection('shift')->table('t_compensatory')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('schedule_id', $leave_schedule_id)
                    ->first();
            DB::connection('shift')->table('h_compensatory')->insert([
                    "compensatory_id" => $compensatory->compensatory_id
                    ,"compensatory_start" => $compensatory->compensatory_start
                    ,"compensatory_end" => $compensatory->compensatory_end
                    ,"usr_id" => $compensatory->usr_id
                    ,"group_id" => $compensatory->group_id
                    ,"schedule_id" => $compensatory->schedule_id
                    ,"updated_at" => $compensatory->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 0
                    ,"original_by" => 'OffDelete'
                    ,"compensatory_hours" => $compensatory->compensatory_hours
                    ,"compensatory_days" => $compensatory->compensatory_days
                    ]);
            DB::connection('shift')->table('t_compensatory')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('schedule_id', $leave_schedule_id)
                    ->update([
                        "compensatory_days" => $compensatory->compensatory_days +1
                        ,"updated_at" => $now
                    ]);
        }else{ // paid leave
            $m_leave = DB::connection('shift')->table('m_leave')->where("leave_id", $leave_id)->first();
            if ($m_leave->leave_amount_flg == 1) {
                $leave_amount = DB::connection('shift')->table('t_leave_amount')
                        ->where('usr_id', $usr_id)
                        ->where('group_id', $group_id)
                        ->where('leave_id', $leave_id)
                        ->first();
                $pre_leave_amount = DB::connection('shift')->table('h_leave_amount')
                        ->where('schedule_id', $schedule_id)
                        ->orderBy('action_at','DESC')
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
                DB::connection('shift')->table('t_leave_amount')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->where('leave_id', $leave_id)
                    ->update([
                        "used_days" => $pre_leave_amount->used_days
                        ,"updated_at" => $now
                    ]);
            }
        }
        DB::connection('h_calendar')->table('h_variation')->insert([
                "variation_id" => $variation_id
                ,"variation_name" => 'leave_id'
                ,"variation_value" => $leave_id
                ,"variation_category" => $variation_category
                ,"updated_at" => $variation_updated_at
                ,"schedule_id" => $schedule_id
                ,"action_by" => $usr_id
                ,"action_at" => $now
                ,"action_flg" => 0
                ,"original_by" => 'OffDelete'
            ]);
        DB::connection('h_calendar')->table('h_variation')->where('variation_id', $variation_id)->delete();
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

