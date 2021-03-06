<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ScheduleAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $usrs = json_decode($request->input('usrs'),true) ?? [];
//        $obj = DB::table('r_group_relate')->where("group_id", $group_id)->get();
//        foreach ($obj as $d) {
//            $db_usr_ids[] = $d->usr_id;
//        }
//        $group = false;
//        foreach ($usrs as $d) {
//            if (in_array($d,$db_usr_ids)) {
//                $group = true;
//            }
//        }
//        if (!$group) {
//            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
//            \Log::warning('group is different:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
//            return json_encode([2,'group is different']);
//        }
        $schedule_id = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
        $file_paths = [];
        if ( isset($_FILES['files']['tmp_name']) ) {
            foreach ($_FILES['files']['tmp_name'] as $k => $d) {
                $name = $_FILES['files']['name'][$k];
                $path = '/todo/'.date('Ymd').'/'.$schedule_id.'/'.
                    substr(base_convert(md5(uniqid()), 16, 36), 0, 3);
                $file_paths[] = '/File'.$path.'/'.$name;
                Storage::putFileAs('/public'.$path, $d, $name);
            }
        }
        $public_title = $request->input('public_title') ?? '';
        $now = date('Y-m-d H:i:s');
        $access_right = $request->input('open') ? 777 : 700 ;
        foreach ($usrs as $d) {
            $schedule[$d]['time_start'] = $request->input('time_start');
            $schedule[$d]['time_end'] = $request->input('time_end');
            $schedule[$d]['title'] = $request->input('title');
            $schedule[$d]['tag'] = $request->input('tag');
            $schedule[$d]['usr_id'] = $d;
            $schedule[$d]['schedule_id'] = $schedule_id;
            $schedule[$d]['group_id'] = $group_id;
            $schedule[$d]['public_title'] = $public_title;
            $schedule[$d]['updated_at'] = $now;
            $schedule[$d]['access_right'] = $access_right;
        }
        DB::beginTransaction();
        DB::table('t_schedule')->insert($schedule);
        if ($request->input('todo') OR isset($file_paths[0])) {
            DB::table('t_todo')->insert([
                'todo' => $request->input('todo') ?: '',
                'schedule_id' => $schedule_id,
                'file_paths' => json_encode($file_paths),
                'updated_at' => now()
            ]);
        }
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

