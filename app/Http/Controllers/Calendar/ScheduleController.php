<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,$action=null, 
            $id_date=null) {
        $usr_id = $request->session()->get('usr_id');
        $lang = 'ja';
        $usr_id = 10;
        $group_id = $request->session()->get('group_id');
        $schedule_id = null;
        $minute_start = $minute_end = $minutes = [['00',''],['15',''],['30',''],['45','']];
        for ($i=0; $i<24; $i++) {
            $k = str_pad($i,2,0,STR_PAD_LEFT);
            $k = (string) $k; 
            $hours[$k] = [$k,''];
        }
        $hour_start = $hour_end = $hours;
        $Arr = new \App\Models\Calendar\Arr();
        $arr_tags = 'tags_'.$lang;
        $tags = $Arr->$arr_tags;
        $public_tags = $tags;
        $bind = [
            'usr_id' => $usr_id
        ];
        $obj = DB::select("SELECT * FROM r_group_relate WHERE usr_id = :usr_id ", $bind);
        $arr_group = [];
        $group_ids = [];
        foreach ($obj as $d) {
           $group_ids[] = $d->group_id;
           $arr['group_id'] = $d->group_id;
           $arr['owner_flg'] = $d->owner_flg;
           $arr['priority'] = $d->priority;
           $arr_group[$d->group_id] = $arr;
        }
        $obj = DB::table('m_group')->whereIn("group_id", $group_ids)->get();
        foreach ($obj as $d) {
           $arr_group[$d->group_id]['group_name'] = $d->group_name;
           $arr_group[$d->group_id]['selected'] = '';
           if (!$group_id) {
               $group_id = $d->group_id;
           }
        }
        $group_ids = json_encode($group_ids);
        $request->session()->put('group_ids', $group_ids);
        
        $title = '';
        $todo = '';
        $public_title = '';
        $mydata = 0;
        if ( strpos($id_date,"-") ) { //new
            $date = $id_date;
            $hours[date('H')][1] = 'selected';
            $hour_start = $hour_end = $hours;
            if (date('i') < 15) {
                $minutes[0][1] = 'selected';
            } else if (date('i') < 30) {
                $minutes[1][1] = 'selected';
            } else if (date('i') < 45) {
                $minutes[2][1] = 'selected';
            } else {
                $minutes[3][1] = 'selected';
            }
            $minute_start = $minute_end = $minutes;
            $hour_start = $hour_end = $hours;
            $obj = DB::table('t_usr')->where("usr_id", $usr_id)->get();
            foreach ($obj as $d) {
                $arr['usr_name'] = $d->usr_name;
                $arr_usr[$d->usr_id] = $arr;
            }
        } else {  //edit
            $schedule_id = $id_date;
            $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
            $date = date('Y-m-d');
            $mydata = 1;
            foreach ($obj as $d) {
                if (date('i', strtotime($d->time_start)) < 15) {
                    $minute_start[0][1] = 'selected';
                } else if (date('i', strtotime($d->time_start)) < 30) {
                    $minute_start[1][1] = 'selected';
                } else if (date('i', strtotime($d->time_start)) < 45) {
                    $minute_start[2][1] = 'selected';
                } else if (date('i', strtotime($d->time_start)) <= 59) {
                    $minute_start[3][1] = 'selected';
                }
                if (date('i', strtotime($d->time_end)) < 15) {
                    $minute_end[0][1] = 'selected';
                } else if (date('i', strtotime($d->time_end)) < 30) {
                    $minute_end[1][1] = 'selected';
                } else if (date('i', strtotime($d->time_end)) < 45) {
                    $minute_end[2][1] = 'selected';
                } else if (date('i', strtotime($d->time_end)) <= 59) {
                    $minute_end[3][1] = 'selected';
                }
                $hour_start[date('H', strtotime($d->time_start))][1] = 'selected';
                $hour_end[date('H', strtotime($d->time_end))][1] = 'selected';
                $tags[$d->tag][1] = 'selected';
                if (isset($public_tags[$d->public_tag][1])) {
                    $public_tags[$d->public_tag][1] = 'selected';
                }
                if(isset($arr_group[$d->group_id]['selected'])) {
                    $arr_group[$d->group_id]['selected'] = 'selected';
                }
                $title = $d->title;
                $public_title = $d->public_title;
                $group_id = $d->group_id;
                $date = date('Y-m-d', strtotime($d->time_start));
                $mydata = 2;
                $usr_ids[] = $d->usr_id;
            }
            $is = DB::table('r_group_relate')
                    ->where("group_id", $group_id)
                    ->where("usr_id", $usr_id)
                    ->first();
            if (!isset($is->usr_id)) {
                die('you are not part of this group');
            }
            $obj = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
            $todo = $obj->todo ?? '';
            $obj = DB::table('t_usr')->whereIn("usr_id", $usr_ids)->get();
            foreach ($obj as $d) {
                $arr['usr_name'] = $d->usr_name;
                $arr_usr[$d->usr_id] = $arr;
            }
        }
        // join_usrs:[{0:2,1:"test2usesr",2:0,3:4}]
        foreach ($arr_usr as $k => $d) {
            $arr = [];
//            $arr["2"] =  $d['owner_flg'];
//            $arr["3"] =  $d['group_id'];
            $arr[0] =  $k;
            $arr[1] =  $d['usr_name'];

            $join_usrs[] = $arr;
        }
        $request->session()->put('view_time', date('Y-m-d H:i:s'));
        $join_usrs = json_encode($join_usrs);

        $arr_group = json_encode($arr_group);

        return view('calendar.edit', compact('date','arr_group','group_ids','schedule_id',
                'mydata','hour_start','hour_end','minute_start','minute_end','tags',
                'usr_id','public_tags','group_id','join_usrs','todo','title','public_title'));
    }
}

