<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoutineController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $target_usr=0) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
//        \App::setLocale('ja');

        $obj = DB::connection('shift')->table('r_routine')
                ->where('group_id', $group_id)
                ->orderBy('updated_at','ASC')
                ->get();
        $i = 0;
        while ($i < 7) {
            $start = 'start_'.$i;
            $end = 'end_'.$i;
            $arr['H'.$start] = '00';
            $arr['H'.$end] = '23';
            $arr['M'.$start] = '00';
            $arr['M'.$end] = '00';
            ++$i;
        }
        $arr['routine_id'] = 0;
        $arr['holiday_flg'] = 0;
        $arr['approver1'] = 0;
        $arr['approver2'] = 0;
        $arr['compensatory_within'] = 0;
        $arr['fix_flg'] = 0;
        $arr['usr_id'] = $target_usr;
        $arr['group_id'] = $group_id;
        $target_data = false;
        foreach ($obj as $d) {
            if( !isset($d->usr_id) OR 
                    ($d->approver1 != $usr_id AND $d->approver2 != $usr_id) ) {
                die('you can not access this');
            }
            if ($target_data) {
                break;
            }
            $i = 0;
            while ($i < 7) {
                $start = 'start_'.$i;
                $end = 'end_'.$i;
                $arr['H'.$start] = substr($d->$start, 0, 2);
                $arr['H'.$end] = substr($d->$end, 0, 2);
                $arr['M'.$start] = substr($d->$start, 3, 2);
                $arr['M'.$end] = substr($d->$end, 3, 2);
                ++$i;
            }
            $arr['routine_id'] = $d->routine_id;
            $arr['holiday_flg'] = $d->holiday_flg;
            $arr['approver1'] = $d->approver1;
            $arr['approver2'] = $d->approver2;
            $arr['compensatory_within'] = $d->compensatory_within;
            $arr['fix_flg'] = $d->fix_flg;
            if ($target_usr == $d->usr_id) {
                $target_data = true;
            }
        }
        $routine = $arr;

        $i = 0;
        while ($i < 24) {
            $hours[] = str_pad($i, 2, "0", STR_PAD_LEFT); 
            ++$i;
        }
        $i = 0;
        while ($i < 6) {
            $minutes[] = str_pad($i * 10, 2, "0", STR_PAD_LEFT); 
            ++$i;
        }
        $i = 0;
        while ($i < 7) {
            $week[] = __('hair_salon.day'.$i); 
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
        
        return view('shift.routine', compact('routine','hours','minutes','week',
                        'time_unit','groups'));
    }
}

