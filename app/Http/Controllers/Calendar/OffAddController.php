<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OffAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 2;
        $group_id = 2;
        \App::setLocale('ja');
        if ($request->input('usrs')[0] != $usr_id OR $group_id != $request->input('group_id')) {
            $res[0] = 2;
            $res[1] = 'you are not this user or group is different';
            die(json_encode($res));
        }
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::connection('shift')->beginTransaction();
        if (strpos($request->input('leave_id'),'schedule') > -1) { // compensatory leave
            $leave_schedule_id = str_replace("schedule_", "", $request->input('leave_id'));
            $compensatory = DB::connection('shift')->table('t_compensatory')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('compensatory_status', 1)
                ->where('schedule_id', $leave_schedule_id)
                ->first();
            if (!isset($compensatory->usr_id)) {
                $res[0] = 2;
                $res[1] = 'this is not available leave';
                die(json_encode($res));
            }
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
                    "compensatory_status" => "2"
                    ,"updated_at" => $now
                ]);
        } else { //paid leave
            $leave_id = $request->input('leave_id');
            $leave_amount = DB::connection('shift')->table('t_leave_amount')
                    ->where("usr_id", $usr_id)
                    ->where("group_id", $group_id)
                    ->where("leave_id", $leave_id)
                    ->first();
            if (!isset($leave_amount->usr_id)) {
                $res[0] = 2;
                $res[1] = 'this is not exist leave';
                die(json_encode($res));
//            } else if ( ($leave_amount->grant_days - $leave_amount->used_days ) < 1) {
//                $res[0] = 2;
//                $res[1] = 'no more leave days';
//                die(json_encode($res));
            }
            $routine = DB::connection('shift')->table('r_routine')
                    ->where('usr_id', $usr_id)
                    ->where('group_id', $group_id)
                    ->first();
            $holidays = [];
            if ($routine->holiday_flg == 1) {
                $obj = DB::table('c_holiday')->select('holiday_date')
                        ->where('country', 'jp')
                        ->where('holiday_date','>', $request->time_start)
                        ->where('holiday_date','<=', $request->time_end)
                        ->get();
                foreach ($obj as $d) {
                    $holidays[] = $d->holiday_date;
                }
            }

            $start = new Carbon($request->time_start);
            $end = new Carbon($request->time_end);
            $use_days = 0;
            while ($start < $end) {
                $day = 'start_'.$start->format('w');
//                var_dump($day);
                if ($routine->$day AND !in_array($start->format('Y-m-d'),$holidays)) {
                    ++$use_days;
                }
                $start->addDay();
            }

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
                    "used_days" => $leave_amount->used_days + $use_days
                    ,"updated_at" => $now
                ]);
        }
        $schedule_id = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
        $schedule[0]['time_start'] = $request->input('time_start');
        $schedule[0]['time_end'] = $request->input('time_end');
        $schedule[0]['title'] = $request->input('leave_id');
        $schedule[0]['tag'] = $request->input('tag');
        $schedule[0]['usr_id'] = $usr_id;
        $schedule[0]['schedule_id'] = $schedule_id;
        $schedule[0]['group_id'] = $group_id;
        $schedule[0]['public_title'] = $request->input('public_title') ?? '';
        $schedule[0]['updated_at'] = now();
        DB::table('t_schedule')->insert($schedule);
        if ($request->input('todo')) {
            DB::table('t_todo')->insert([
                'todo' => $request->input('todo'),
                'schedule_id' => $schedule_id,
                'updated_at' => now()
            ]);
        }

        DB::connection('shift')->commit();
        DB::commit();
        $res[0] = 1;
        echo json_encode($res);
    }
}

