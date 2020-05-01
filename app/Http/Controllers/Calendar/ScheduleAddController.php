<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScheduleAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 2;
        $usrs = json_decode($request->input('usrs'),true) ?? [];
        $group_ids = json_decode($request->session()->get('group_ids'),true);
        $obj = DB::table('r_group_relate')->whereIn("group_id", $group_ids)->get();
        foreach ($obj as $d) {
            $db_usr_ids[] = $d->usr_id;
        }
        $no_group = false;
        foreach ($usrs as $d) {
            if (!in_array($d,$db_usr_ids)) {
                $no_group = true;
            }
        }
        if ($no_group) {
            $res[0] = 2;
            $res[1] = 'you can not insert because you are not part of this group';
            die(json_encode($res));
        }
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

        $group_id = $request->input('group_id');
        $public_title = $request->input('public_title') ?? '';
        foreach ($usrs as $d) {
            $schedule[$d]['time_start'] = $request->input('time_start');
            $schedule[$d]['time_end'] = $request->input('time_end');
            $schedule[$d]['title'] = $request->input('title');
            $schedule[$d]['tag'] = $request->input('tag');
            $schedule[$d]['usr_id'] = $d;
            $schedule[$d]['schedule_id'] = $schedule_id;
            $schedule[$d]['group_id'] = $group_id;
            $schedule[$d]['public_title'] = $public_title;
            $schedule[$d]['updated_at'] = now();
            $schedule[$d]['access_right'] = 777;
        }
        DB::table('t_schedule')->insert($schedule);
        if ($request->input('todo') OR isset($file_paths[0])) {
            DB::table('t_todo')->insert([
                'todo' => $request->input('todo') ?: '',
                'schedule_id' => $schedule_id,
                'file_paths' => json_encode($file_paths),
                'updated_at' => now()
            ]);
        }
        $res[0] = 1;
        echo json_encode($res);
    }
}

