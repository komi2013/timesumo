<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleDeleteController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');

        $schedule_id = $request->input('schedule_id');

        $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
        $mydata = false;
        $overwrite = false;
        $access_right = 0;
        $book_id = 0;
        foreach ($obj as $d) {
            if ($d->usr_id == $usr_id AND $access_right < substr($d->access_right,0,1)) {
                $access_right = substr($d->access_right,0,1);
            } else if(session('mystaff') == $usr_id AND $access_right < substr($d->access_right,1,1)) {
                $access_right = substr($d->access_right,1,1);
            } else if ($group_id == $d->group_id AND $access_right < substr($d->access_right,2,1)) {
                $access_right = substr($d->access_right,2,1);
            } else {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('group is different:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'group is different']);
            }
            $time_start = $d->time_start;
            $time_end = $d->time_end;
            $title = $d->title;
            $tag = $d->tag;
            $group_id = $d->group_id;
            $updated_at = $d->updated_at;
            $arr = [];
            $arr['public_title'] = $public_title = $d->public_title;
            $db[$d->usr_id] = $arr;
            $usr_ids[] = $d->usr_id;
            $accessRight = $d->access_right;
            $book_id = $d->book_id;
        }
        if ($access_right < 6) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('access_right < 6:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'access_right < 6']);
        }
        $paths = json_decode($request->input('file_paths'),true) ?: [];
        foreach ($paths as $d) {
            Storage::deleteDirectory("public/".substr($d[0],5,strrpos($d[0], "/")-5));
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
        $now = date('Y-m-d H:i:s');
        if ($book_id) {
            $var = DB::table('t_variation')
                    ->where('schedule_id', $schedule_id)
                    ->where('variation_name', 'db_id')
                    ->first();
            $db = DB::table('c_db')->where('db_id', $var->variation_value)->first();
            \Config::set('database.connections.dynamic.host',$db->host);
            \Config::set('database.connections.dynamic.database',$db->database);
            \Config::set('database.connections.dynamic.username',$db->username);
            \Config::set('database.connections.dynamic.password',$db->password);

            $obj = DB::connection('dynamic')->table('t_schedule')->where('book_id', $book_id)->get();
            $s = json_decode($obj,true);
            $arr_schedule_id = [];
            $arr_usr_id = [];
            foreach ($s as $k => $d) {
                $s[$k]['action_by'] = $usr_id;
                $s[$k]['action_at'] = $now;
                $s[$k]['action_flg'] = 0;
                $s[$k]['original_by'] = 'User_ScheduleDelete';
                $arr_schedule_id[] = $d['schedule_id'];
                $arr_usr_id[] = $d['usr_id'];
                $s[$k]['usr_id_json'] = json_encode($arr_usr_id);
            }
            $obj = DB::connection('dynamic')->table('t_todo')->whereIn('schedule_id', $arr_schedule_id)->get();
            $arr_todo = json_decode($obj,true);
            foreach ($arr_todo as $k => $d) {
                $arr_todo[$k]['action_by'] = $usr_id;
                $arr_todo[$k]['action_at'] = $now;
                $arr_todo[$k]['action_flg'] = 0;
            }
            DB::connection('dynamic')->beginTransaction();
            DB::connection('dynamic')->table('h_schedule')->insert($s);
            DB::connection('dynamic')->table('h_todo')->insert($arr_todo);
            DB::connection('dynamic')->table('t_schedule')->where('book_id', $book_id)->delete();
            DB::connection('dynamic')->table('t_todo')->whereIn('schedule_id', $arr_schedule_id)->delete();
            DB::connection('dynamic')->commit();
        }

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
                ,"original_by" => 'User_ScheduleDelete'
                ,"usr_id_json" => json_encode($usr_ids)
            ]);
        DB::table('t_schedule')->where('schedule_id', $schedule_id)->delete();
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
        $res[0] = 1;
        return json_encode($res);
    }
}

