<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelingController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $schedule_id='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
        $group_id = 6;
        \App::setLocale('ja');
        
        $obj = DB::table('t_schedule')->where('schedule_id',$schedule_id)->get();
        $access_right = false;
        $arr_customer = [];
        foreach ($obj as $d) {
            if ($d->tag != 7) {
                if ($d->tag == 4) {
                    $arr_staff[$d->usr_id] = '';
                    $schedule['title'] = $d->title;  // supposed to be customer name
                    $schedule['usr_id'] = $d->usr_id;
                    $schedule['time_start'] = $d->time_start;
                    $schedule['time_end'] = $d->time_end;
                    $schedule['tag'] = $d->tag;
                    $schedule['group_id'] = $d->group_id;
                    $schedule['updated_at'] = $d->updated_at;
                    $schedule['editable_flg'] = $d->editable_flg;
                }
                if ($d->tag == 6) {
                    $arr_customer[$d->usr_id] = '';
                }
                $arr_usr_id[] = $d->usr_id;
            }
            if ($d->group_id === $group_id OR $d->usr_id === $usr_id) {
                $access_right = true;
            }
        }
        if (!$access_right) {
            die('404');
        }
        $schedule['schedule_id'] = $schedule_id;
        $obj = DB::table('t_usr')->whereIn('usr_id',$arr_usr_id)->get();
        foreach ($obj as $d) {
            if ( isset($arr_staff[$d->usr_id]) ) {
                $arr_staff[$d->usr_id] = $d->usr_name;
            }
            if ( isset($arr_customer[$d->usr_id]) ) {
                $arr_customer[$d->usr_id] = $d->usr_name;
            }
        }
        $todo = DB::table('t_todo')->where('schedule_id',$schedule_id)->orderBy('updated_at','DESC')->get();
        $todo = json_decode($todo,true);
        return view('hair_salon.canceling', 
                compact('schedule','arr_staff','arr_customer','todo','schedule'));
    }
}

