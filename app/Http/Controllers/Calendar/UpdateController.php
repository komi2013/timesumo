<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {

        $usr_id = 10;
        $arr3 = [];
        if ($request->input('common_id')) {
            $common_id = $request->input('common_id');
            $obj = DB::table('t_schedule')->where("common_id", $common_id)->get();
            $mydata = false;
            $overwrite = false;
            foreach ($obj as $d) {
                if ($d->usr_id == $usr_id) {
                    $mydata = true;
                }
                $lastUpdate = new Carbon($d->updated_at);
                $viewTime = new Carbon($request->session()->get('view_time'));
                if ( $lastUpdate->gt($viewTime) ) {
                    $overwrite = true;
                }
                if ($overwrite) {
                    $res[0] = 2;
                    $res[1] = 'somebody overwrite this schedule, please refresh page and submit again';
                    die(json_encode($res));
                }
                $arr2 = [];
                $arr2['time_start'] = $d->time_start;
                $arr2['time_end'] = $d->time_end;
                $arr2['todo'] = $d->todo;
                $arr2['title'] = $d->title;
                $arr2['tag'] = $d->tag;
                $arr2['common_id'] = $d->common_id;
                $arr2['usr_id'] = $d->usr_id;
                $arr2['group_id'] = $d->group_id;
                $arr2['public_tag'] = $d->public_tag;
                $arr2['public_title'] = $d->public_title;
                $arr3[$d->usr_id] = $arr2;
            }
            if (!$mydata) {
//                $res[0] = 2;
//                $res[1] = 'you can not update because you can not change others schedule';
//                die(json_encode($res));
            }
            DB::table('t_schedule')->where('common_id', $request->input('common_id'))->delete();

        } else {
            $obj = DB::select("select nextval('t_schedule_common_id_seq')");
            foreach ($obj as $d) {
                $common_id = $d->nextval;
            }
        }
        if ($request->input('public') == 2) {
            $group_id = $request->input('group_id');
        } else {
            $group_id = $request->input('public');
        }
        $public_tag = $request->input('public_tag') ?? '';
        $public_title = $request->input('public_title') ?? '';
        foreach ($request->input('usrs') as $d) {
            if (isset($arr3[$d[0]])) {
                $arr3[$d[0]]['time_start'] = $request->input('time_start');
                $arr3[$d[0]]['time_end'] = $request->input('time_end');
                $arr3[$d[0]]['todo'] = $request->input('todo');
                $arr3[$d[0]]['title'] = $request->input('title');
                $arr3[$d[0]]['tag'] = $request->input('tag');
                $arr3[$d[0]]['usr_id'] = $d;
                $arr3[$d[0]]['common_id'] = $common_id;
                $arr3[$d[0]]['regi_usr'] = true;
                if ($d[0] == $usr_id) {
                    $arr3[$d[0]]['group_id'] = $group_id;
                    $arr3[$d[0]]['public_tag'] = $public_tag;
                    $arr3[$d[0]]['public_title'] = $public_title;
                }
            } else {
                $arr2 = [];
                $arr2['time_start'] = $request->input('time_start');
                $arr2['time_end'] = $request->input('time_end');
                $arr2['todo'] = $request->input('todo');
                $arr2['title'] = $request->input('title');
                $arr2['tag'] = $request->input('tag');
                $arr2['usr_id'] = $d[0];
                $arr2['common_id'] = $common_id;
                $arr2['regi_usr'] = true;
                if ($d[0] == $usr_id) {
                    $arr2['group_id'] = $group_id;
                    $arr2['public_tag'] = $public_tag;
                    $arr2['public_title'] = $public_title;                    
                } else {
                    $arr2['group_id'] = 0; //0 = todo only, 1 = all, 2 ~ group_id
                    $arr2['public_tag'] = 0;
                    $arr2['public_title'] = '';
                }
                $arr3[$d[0]] = $arr2;
            }
        }
        $schedule = [];
        foreach ($arr3 as $d) {
            if (isset($d['regi_usr'])) {
                unset($d['regi_usr']);
                $schedule[] = $d;
            }
        }
//        echo '<pre>'; var_dump($schedule); echo '</pre>'; die;
        DB::table('t_schedule')->insert($schedule);
        $res[0] = 1;
        echo json_encode($res);
//        $request->session()->regenerateToken();
    }
}

