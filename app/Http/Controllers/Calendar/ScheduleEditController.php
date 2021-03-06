<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ScheduleEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $usrs = json_decode($request->input('usrs'),true);
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
            }
            $lastUpdate = new Carbon($d->updated_at);
            $viewTime = new Carbon($request->session()->get('view_time'));
            if ( $lastUpdate->gt($viewTime) ) {
                $overwrite = true;
            }
            if ($overwrite) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('overwrite:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'overwrite']);
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
        }
        if ($access_right < 6) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('access_right < 6:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'access_right < 6']);
        }
        $is = DB::table('r_group_relate')
                ->where("group_id", $group_id)
                ->where("usr_id", $usr_id)
                ->first();
        if (!isset($is->usr_id)) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('group is different:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'group is different']);
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();

        $paths = json_decode($request->input('file_paths'),true) ?: [];
        $file_paths = [];
        foreach ($paths as $d) {
            if ($d[2]) {
                $file_paths[] = $d[0];
            } else {
                Storage::deleteDirectory("public/".substr($d[0],5,strrpos($d[0], "/")-5));
            }
        }
        if ( isset($_FILES['files']['tmp_name']) ) {
            foreach ($_FILES['files']['tmp_name'] as $k => $d) {
                $name = $_FILES['files']['name'][$k];
                $path = '/todo/'.date('Ymd').'/'.$schedule_id.'/'.
                    substr(base_convert(md5(uniqid()), 16, 36), 0, 3);
                $file_paths[] = '/File'.$path.'/'.$name;
                Storage::putFileAs('/public'.$path, $d, $name);
            }
        }
        $now = date('Y-m-d H:i:s');
        foreach ($usrs as $d) {
            $schedule[$d]['time_start'] = $request->input('time_start');
            $schedule[$d]['time_end'] = $request->input('time_end');
            $schedule[$d]['title'] = $request->input('title');
            $schedule[$d]['tag'] = $request->input('tag');
            $schedule[$d]['usr_id'] = $d;
            $schedule[$d]['schedule_id'] = $schedule_id;
            $schedule[$d]['group_id'] = $request->input('group_id');
            if ($d != $usr_id AND isset($db[$d]['public_title'])) {
                $schedule[$d]['public_title'] = $db[$d]['public_title'];
            } else {
                $schedule[$d]['public_title'] = $request->input('public_title') ?? '';
            }
            $schedule[$d]['updated_at'] = $now;
            $schedule[$d]['access_right'] = $accessRight;
        }
        $obj = DB::table('t_compensatory')->where("schedule_id", $schedule_id)->get();
        $compensatory = json_decode($obj,true);
        foreach ($compensatory as $k => $d) {
            $compensatory[$k]['action_by'] = $usr_id;
            $compensatory[$k]['action_at'] = $now;
            $compensatory[$k]['action_flg'] = 0;
        }
        $compensatory_del = $compensatory;
        $compensatory = [];
        $now = date('Y-m-d H:i:s');
        $obj = DB::table('r_routine')
                ->where("group_id", $group_id)
                ->whereIn("usr_id", $usrs)
                ->get();
        foreach ($obj as $d) {
            if ($d->fix_flg == 1) {
                $start = new Carbon($request->input('time_start'));
                $end = new Carbon($request->input('time_end'));
                $days = 0;
                while ($start->diffInDays($end) >= 0 AND $start < $end) {
                    $start_i = 'start_'.$start->format('w');
                    if ( !$d->$start_i ) {
                        ++$days; 
                    }
                    $start->addDay();
                }
                $arr = [];
                if ($days > 0) {
                    $arr['compensatory_start'] = $start->format('Y-m-d');
                    $arr['compensatory_end'] = $start;
                    $arr['usr_id'] = $d->usr_id;
                    $arr['group_id'] = $group_id;
                    $arr['schedule_id'] = $schedule_id;
                    $arr['updated_at'] = $now;
                    $arr['compensatory_days'] = $days;
                    $compensatory[] = $arr;
                }
            }
        }
        $obj = DB::table('r_rule')
                ->where("group_id", $group_id)
                ->whereIn("usr_id", $usrs)
                ->get();
        $rule = [];
        foreach ($obj as $d) {
            $rule[$d->usr_id] = $d->compensatory_within;
        }
        foreach ($compensatory as $k => $d) {
            $start = $compensatory[$k]['compensatory_end'];
            $start->addDay($rule[$d['usr_id']]);
            $compensatory[$k]['compensatory_end'] = $start->format('Y-m-d');
        }
        DB::beginTransaction();
        DB::table('h_compensatory')->insert($compensatory_del);
        DB::table('t_compensatory')->where("schedule_id", $schedule_id)->delete();
        DB::table('t_compensatory')->insert($compensatory);
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
                ,"action_flg" => 1
                ,"original_by" => 'ScheduleEdit'
                ,"usr_id_json" => json_encode($usr_ids)
            ]);
        DB::table('t_schedule')->where('schedule_id', $request->input('schedule_id'))->delete();
        DB::table('t_schedule')->insert($schedule);
        if(isset($todo->updated_at)){
            DB::table('h_todo')->insert([
                    'todo' => $todo->todo
                    ,'schedule_id' => $schedule_id
                    ,'updated_at' => $todo->updated_at
                    ,'file_paths' => $todo->file_paths
                    ,"action_by" => $usr_id
                    ,"action_at" => $now
                    ,"action_flg" => 1
                ]);
        }
        DB::table('t_todo')->where("schedule_id", $schedule_id)->delete();
        DB::table('t_todo')->insert([
            'todo' => $request->input('todo') ?: '',
            'schedule_id' => $schedule_id,
            'file_paths' => json_encode($file_paths),
            'updated_at' => $now
        ]);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

