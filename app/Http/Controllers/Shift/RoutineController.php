<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoutineController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $target_usr=0) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $obj = DB::table('r_rule')
                ->where('group_id', $group_id)
                ->where('usr_id', $target_usr)
                ->get();
        $rule = json_decode($obj,true);
        if ($rule[0]['approver1'] != $usr_id AND $rule[0]['approver2'] != $usr_id) {
            $msg = 'no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));            
        }
        $obj = DB::table('r_routine')
                ->where('group_id', $group_id)
                ->where('usr_id', $target_usr)
                ->get();
        $routine = json_decode($obj,true);
        $i = 0;
        while ($i < 7) {
            if ($routine[0]['start_'.$i]) {
                $routine[0]['start_'.$i] = substr($routine[0]['start_'.$i],0,5);
                $routine[0]['end_'.$i] = substr($routine[0]['end_'.$i],0,5);
                $routine[0]['disable_'.$i] = 0;
            } else {
                $routine[0]['disable_'.$i] = 1;
            }
            $week[] = __('salon.day'.$i); 
            ++$i;
        }
        $time_unit[0] = __('calendar.hour');
        $time_unit[1] = __('calendar.day');
        $time_unit[2] = __('calendar.month');

        $obj = DB::table('r_group_relate')->where('group_id', $group_id)->get();
        foreach ($obj as $d) {
            $arr_usr_id[] = $d->usr_id;
        }
        $obj = DB::table('t_usr')->whereIn('usr_id', $arr_usr_id)->get();
        foreach ($obj as $d) {
            $groups[$d->usr_id] = $d->usr_name; 
        }
//        dd($rule);
        $rule = json_encode($rule);
        $request->session()->flash('rule', $rule);
        $routine = json_encode($routine);
        $request->session()->flash('routine', $routine);
        $request->session()->flash('target_usr', $target_usr);
        return view('shift.routine', compact('routine','rule','week','time_unit','groups'));
    }
}

