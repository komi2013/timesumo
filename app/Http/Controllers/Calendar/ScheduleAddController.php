<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleAddController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 10;
        $usrs = $request->input('usrs') ?? [];
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

        $group_id = $request->input('group_id');
        $public_tag = $request->input('public_tag') ?? '';
        $public_title = $request->input('public_title') ?? '';
        foreach ($usrs as $d) {
            $schedule[$d]['time_start'] = $request->input('time_start');
            $schedule[$d]['time_end'] = $request->input('time_end');
            $schedule[$d]['title'] = $request->input('title');
            $schedule[$d]['tag'] = $request->input('tag');
            $schedule[$d]['usr_id'] = $d;
            $schedule[$d]['schedule_id'] = $schedule_id;
            $schedule[$d]['group_id'] = $group_id;
            $schedule[$d]['public_tag'] = $public_tag;
            $schedule[$d]['public_title'] = $public_title;
            $schedule[$d]['updated_at'] = now();
        }
        DB::table('t_schedule')->insert($schedule);
        if ($request->input('todo')) {
            DB::table('t_todo')->insert([
                'todo' => $request->input('todo'),
                'schedule_id' => $schedule_id,
                'updated_at' => now()
            ]);
        }
        $res[0] = 1;
        echo json_encode($res);
    }
}

