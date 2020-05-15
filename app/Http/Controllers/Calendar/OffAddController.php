<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OffAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');

        $now = date('Y-m-d H:i:s');
        $routine = DB::table('r_routine')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        $rule = DB::table('r_rule')
            ->where('usr_id', $usr_id)
            ->where('group_id', $group_id)
            ->first();
        DB::beginTransaction();
        if (strpos($request->input('leave_id'),'schedule') > -1) { // compensatory leave
            $leave_schedule_id = str_replace("schedule_", "", $request->input('leave_id'));
            $compensatory = DB::table('t_compensatory')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('schedule_id', $leave_schedule_id)
                ->first();
            if ( ( $compensatory->compensatory_days == 0 AND $compensatory->compensatory_hours == 0 )
                    OR $routine->fix_flg == 0) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('compensatory leave is not available:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'compensatory leave is not available']);

            }
            DB::table('h_compensatory')->insert([
                    "compensatory_id" => $compensatory->compensatory_id
                    ,"compensatory_start" => $compensatory->compensatory_start
                    ,"compensatory_end" => $compensatory->compensatory_end
                    ,"usr_id" => $compensatory->usr_id
                    ,"group_id" => $compensatory->group_id
                    ,"schedule_id" => $compensatory->schedule_id
                    ,"updated_at" => $compensatory->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 1
                    ,"original_by" => 'OffAdd'
                    ,"compensatory_hours" => $compensatory->compensatory_hours
                    ,"compensatory_days" => $compensatory->compensatory_days
                ]);
            DB::table('t_compensatory')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('schedule_id', $leave_schedule_id)
                ->update([
                    "compensatory_days" => $compensatory->compensatory_days -1
                    ,"updated_at" => $now
                ]);
        } else { //paid leave
            $leave_id = $request->input('leave_id');
            $m_leave = DB::table('m_leave')->where("leave_id", $leave_id)->first();
            if ($m_leave->leave_amount_flg == 1) {

                $leave_amount = DB::table('t_leave_amount')
                        ->where("usr_id", $usr_id)
                        ->where("leave_id", $leave_id)
                        ->first();
                if (!isset($leave_amount->usr_id)) {
                    \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                    \Log::warning('leave not exist:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                    return json_encode([2,'leave not exist']);
                }

                $holidays = [];
                if ($rule->holiday_flg == 1) {
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
                if ($leave_amount->grant_days < $leave_amount->used_days + $use_days) {
                    \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                    \Log::warning('leave days not enough:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                    return json_encode([2,'leave days not enough']);
                }
                DB::table('t_leave_amount')
                    ->where('usr_id', $usr_id)
                    ->where('leave_id', $leave_id)
                    ->update([
                        "used_days" => $leave_amount->used_days + $use_days
                        ,"updated_at" => $now
                    ]);
            }
        }
        $schedule_id = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
        if ( !isset($leave_schedule_id) AND isset($leave_amount->usr_id) ) {
            DB::table('h_leave_amount')->insert([
                    "usr_id" => $leave_amount->usr_id
                    ,"enable_start" => $leave_amount->enable_start
                    ,"enable_end" => $leave_amount->enable_end
                    ,"grant_days" => $leave_amount->grant_days
                    ,"used_days" => $leave_amount->used_days
                    ,"note" => $leave_amount->note
                    ,"updated_at" => $leave_amount->updated_at
                    ,"leave_id" => $leave_amount->leave_id
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 1
                    ,"original_by" => 'OffAdd'
                    ,"schedule_id" => $schedule_id
                ]);            
        }

        $schedule[0]['time_start'] = $request->input('time_start');
        $schedule[0]['time_end'] = $request->input('time_end');
        $schedule[0]['title'] = $request->input('title');
        $schedule[0]['tag'] = $request->input('tag');
        $schedule[0]['usr_id'] = $usr_id;
        $schedule[0]['schedule_id'] = $schedule_id;
        $schedule[0]['group_id'] = $group_id;
        $schedule[0]['public_title'] = $request->input('public_title') ?? '';
        $schedule[0]['updated_at'] = $now;
        $schedule[0]['access_right'] = 770;
        DB::table('t_schedule')->insert($schedule);
        $variation[0]['schedule_id'] = $schedule_id;
        $variation[0]['variation_name'] = 'leave_id';
        $variation[0]['variation_value'] = $request->input('leave_id');
        $variation[0]['variation_category'] = 'leave';
        $variation[0]['updated_at'] = $now;
        DB::table('t_variation')->insert($variation);
        if ($request->input('todo')) {
            DB::table('t_todo')->insert([
                'todo' => $request->input('todo'),
                'schedule_id' => $schedule_id,
                'updated_at' => $now
            ]);
        }

        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

