<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $schedule = $request->schedule;
        $schedule_id = $schedule['schedule_id'];
        
        $todo = DB::table('t_todo')->where('schedule_id',$schedule_id)->get();
        $todo = json_decode($todo,true);

        foreach ($todo as $k => $d) {
            $todo[$k]['action_by'] = $usr_id;
            $todo[$k]['action_at'] = date('Y-m-d H:i:s');
            $todo[$k]['action_flg'] = 0;
        }
        DB::beginTransaction();
        DB::table('h_schedule')->insert([
                "schedule_id" => $schedule_id
                ,"title" => $schedule['title']
                ,"usr_id" => $schedule['usr_id']
                ,"time_start" => $schedule['time_start']
                ,"time_end" => $schedule['time_end']
                ,"tag" => $schedule['tag']
                ,"group_id" => $schedule['group_id']
                ,"updated_at" => $schedule['updated_at']
                ,"access_right" => $schedule['access_right']
                ,"action_by" => $usr_id
                ,"action_at" => date('Y-m-d H:i:s')
                ,"action_flg" => 0
                ,"original_by" => $schedule['title']
            ]);
        DB::table('h_todo')->insert($todo);
        DB::table('t_schedule')
                ->where('schedule_id',$schedule_id)
                ->delete();
        DB::table('t_todo')
                ->where("schedule_id",$schedule_id)
                ->delete();
        DB::commit();

        $res[0] = 1;
        die( json_encode($res) );

    }
}

