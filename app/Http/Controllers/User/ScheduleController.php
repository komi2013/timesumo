<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller {

    public function index(Request $request, $directory,$controller,$action, 
            $id_date=null,$tag=1) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));

        $mystaff = session('mystaff') ?: 0;

        $schedule_id = null;
        $Arr = new \App\My\UserTag();
        $arr_tags = \Cookie::get('lang');
        $tags = $Arr->$arr_tags;
        $bind = [
            'usr_id' => $usr_id
        ];
        $title = '';
        $todo = '';
        $public_title = '';
        $leave_id = null;
        $open = 1;
        if ( strpos($id_date,"-") OR !$id_date ) { //new
            $date = $id_date ?: date('Y-m-d');
            $date_end = date('Y-m-d',strtotime($date));
            $dt = new Carbon();
            $start = $dt->addHour()->format('H:00');
            $end = $dt->addHour()->format('H:00');
            $obj = DB::table('t_usr')->where("usr_id", $usr_id)->get();
            foreach ($obj as $d) {
                $arr = [];
                $arr['usr_name'] = $d->usr_name;
                $arr_usr[$d->usr_id] = $arr;
            }
            $access_right = 7;
            $file_paths = json_encode([]);
        } else {  //edit
            $schedule_id = $id_date;
            $obj = DB::table('t_schedule')->where("schedule_id", $schedule_id)->get();
            $access_right = 0;
            foreach ($obj as $d) {
                $start = date('H:i', strtotime($d->time_start));
                $end = date('H:i', strtotime($d->time_end));
                $tag = $d->tag;

                if(isset($arr_group[$d->group_id]['selected'])) {
                    $arr_group[$d->group_id]['selected'] = 'selected';
                }
                $title = $d->title;
                $public_title = $d->public_title;
                $db_group_id = $d->group_id;
                $db_usr_id = $d->usr_id;
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
                $open = substr($d->access_right,2,1);
            }
            if ($access_right == 0) {
                $msg = 'access right is 0:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning($msg);
                return view('errors.500', compact('msg'));
            }
            $obj = DB::table('r_group_relate')->where("usr_id", $usr_id)->get();
            $arr_group_id[] = 0;
            foreach ($obj as $d) {
                $arr_group_id[] = $d->group_id;
            }
            if (!in_array($db_group_id,$arr_group_id) OR ($db_group_id == 0 AND $usr_id != $db_usr_id)) {
                $msg = 'another group or usr:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning($msg);
                return view('errors.500', compact('msg'));
            }
            $obj = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
            $todo = $obj->todo ?? '';
            $file_paths = json_decode($obj->file_paths ?? null,true) ?: [];
            foreach ($file_paths as $k => $d) {
                $name = explode("/",$d);
                $file_paths[$k] = [$d,end($name),true];
            }
            $file_paths = json_encode($file_paths);
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
            $request->session()->flash('schedule_id', $schedule_id);
        }
        foreach ($arr_usr as $k => $d) {
            $arr = [];
            $arr[0] =  $k;
            $arr[1] =  $d['usr_name'];
            $join_usrs[] = $arr;
        }
        $request->session()->flash('view_time', date('Y-m-d H:i:s'));
        $join_usrs = json_encode($join_usrs);
        
        $dt = new Carbon($date);
        $i = 0;
        while ($i < 30) {
            $next[] = [$dt->format('Y-m-d'),$dt->format(__('calendar.date'))];
            $dt->addDay();
            ++$i;
        }
        return view('user.schedule', compact('date','date_end','schedule_id',
                'start','end','tags','tag','usr_id','group_id','join_usrs',
                'todo','file_paths','title','public_title','next','access_right','open'));
    }
}

