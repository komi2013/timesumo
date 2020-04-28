<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,$action=null, 
            $id_date=null,$tag=1) {
        $usr_id = $request->session()->get('usr_id');
        $lang = 'ja';
        \App::setLocale('ja');
        $usr_id = 2;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $mystaff = session('mystaff');
        $mystaff = 2;
        $schedule_id = null;
        $minutes = ['00','15','30','45'];
        for ($i=0; $i<24; $i++) {
            $arr[] = str_pad($i,2,0,STR_PAD_LEFT);
        }
        $hours = $arr;
        $Arr = new \App\Models\Calendar\Arr();
        $arr_tags = 'tags_'.$lang;
        $tags = $Arr->$arr_tags;
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
        $leave_id = null;
        if ( strpos($id_date,"-") OR !$id_date ) { //new
            $date = $id_date ?: date('Y-m-d');
            $date_end = date('Y-m-d',strtotime($date));
            $dt = new Carbon();
            $hourStart = $dt->addHour()->format('H');
            $hourEnd = $dt->addHour()->format('H');
            $obj = DB::table('t_usr')->where("usr_id", $usr_id)->get();
            foreach ($obj as $d) {
                $arr = [];
                $arr['usr_name'] = $d->usr_name;
                $arr_usr[$d->usr_id] = $arr;
            }
            $access_right = 7;
        } else {  //edit
            $schedule_id = $id_date;
            $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
            $access_right = 0;
            foreach ($obj as $d) {
                $hourStart = date('H', strtotime($d->time_start));
                $minuteStart = date('i', strtotime($d->time_start));
                $hourEnd = date('H', strtotime($d->time_end));
                $minuteEnd = date('i', strtotime($d->time_end));
                $tag = $d->tag;

                if(isset($arr_group[$d->group_id]['selected'])) {
                    $arr_group[$d->group_id]['selected'] = 'selected';
                }
                $title = $d->title;
                $public_title = $d->public_title;
                $group_id = $d->group_id;
                $date = date('Y-m-d', strtotime($d->time_start));
                $date_end = date('Y-m-d',strtotime($d->time_end));
                $usr_ids[] = $d->usr_id;
                if ($d->usr_id == $usr_id AND $access_right < substr($d->access_right,0,1)) {
                    $access_right = substr($d->access_right,0,1);
                } else if($mystaff == $d->usr_id AND $access_right < substr($d->access_right,1,1)) {
                    $access_right = substr($d->access_right,1,1);
                } else {
                    $access_right = substr($d->access_right,2,1);
                }
            }
            if ($access_right == 0) {
                die('no access right');
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
            $arr = $tags;
            $tags = [];
            foreach ($arr as $k => $d) {
                if ($k == 2 AND $tag == 2) {
                    $tags[$k] = $d;
                } else if ($k != 2 AND $tag != 2) {
                    $tags[$k] = $d;
                }   
            }
        }
        foreach ($arr_usr as $k => $d) {
            $arr = [];
            $arr[0] =  $k;
            $arr[1] =  $d['usr_name'];
            $join_usrs[] = $arr;
        }
        $request->session()->put('view_time', date('Y-m-d H:i:s'));
        $join_usrs = json_encode($join_usrs);
        
        $arr_group = json_encode($arr_group);
        $dt = new Carbon($date);
        $i = 0;
        while ($i < 30) {
            $next[] = [$dt->format('Y-m-d'),$dt->format(__('calendar.date'))];
            $dt->addDay();
            ++$i;
        }
//        dd($next);
        return view('calendar.schedule', compact('date','date_end','arr_group','group_ids','schedule_id',
                'hours','hourStart','hourEnd','minutes','tags','tag','usr_id',
                'group_id','join_usrs','todo','title','public_title','next','access_right'));
    }
}

