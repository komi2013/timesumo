<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShiftController extends Controller {

    public function regular(Request $request, $directory=null, $controller=null,
            $action=null, $arg1='', $arg2='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
//        $usr_id = $request->session()->get('usr_id');
//        \Cookie::queue('lang', $lang);
        \App::setLocale('ja');
        $usr_id = 2;
        $group_id = 1;
//        $usr = DB::table('t_usr')->where('usr_id',$usr_id)->first();
        $i = 0;
        while ($i < 7) {
            $arr['routine_id'] = 0;
            $arr['Hstart_'.$i] = '10';
            $arr['Hend_'.$i] = '20';
            $arr['Hbreak_start_'.$i] = '10';
            $arr['Hbreak_end_'.$i] = '20';
            $arr['Mstart_'.$i] = '00';
            $arr['Mend_'.$i] = '00';
            $arr['Mbreak_start_'.$i] = '00';
            $arr['Mbreak_end_'.$i] = '00';
            if ($i === 1 OR $i === 2) {
                $arr['shift_'.$i] = 'X';
            } else {
                $arr['shift_'.$i] = 'O';
            }
            ++$i;
        }
        $obj = DB::table('r_routine')
                ->where('usr_id',$usr_id)
                ->where('group_id',$group_id)
                ->get();
        $advance = false;
        foreach ($obj as $d) {
            $arr['routine_id'] = $d->routine_id;
            $i = 0;
            $advance = false;
            $start_time = $d->start_0;
            $end_time = $d->end_0;
            $break_start_time = $d->break_start_0;
            $break_end_time = $d->break_end_0;
            while ($i < 7) {
                $start = 'start_'.$i;
                $end = 'end_'.$i;
                $break_start = 'break_start_'.$i;
                $break_end = 'break_end_'.$i;
                if ($start_time != $d->$start OR $end_time != $d->$end OR
                        $break_start_time != $d->$break_start OR $break_end_time != $d->$break_end) {
                    $advance = true;
                }
                $arr['H'.$start] = substr($d->$start, 0, 2);
                $arr['H'.$end] = substr($d->$end, 0, 2);
                $arr['H'.$break_start] = substr($d->$break_start, 0, 2);
                $arr['H'.$break_end] = substr($d->$break_end, 0, 2);
                $arr['M'.$start] = substr($d->$start, 3, 2);
                $arr['M'.$end] = substr($d->$end, 3, 2);
                $arr['M'.$break_start] = substr($d->$break_start, 3, 2);
                $arr['M'.$break_end] = substr($d->$break_end, 3, 2);
                if ($d->$start) {
                    $arr['shift_'.$i] = 'O';
                } else {
                    $arr['shift_'.$i] = 'X';
                }
                ++$i;
            }
        }
        $routine[0] = $arr;
        $request->session()->put('routine_id',$arr['routine_id']);
//echo '<pre>';
//var_dump($routine);
//echo '</pre>';
//die;
        $i = 6;
        while ($i < 18) {
            $startOption[] = str_pad($i, 2, "0", STR_PAD_LEFT); 
            ++$i;
        }
        $i = 11;
        while ($i < 23) {
            $endOption[] = $i;
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
        $routine = json_encode($routine);
        $startOption = json_encode($startOption);
        $endOption = json_encode($endOption);
        $minutes = json_encode($minutes);
        $week = json_encode($week);
        return view('hair_salon.shift_regular', 
                compact('routine','startOption','endOption','minutes','week','advance'));
    }
}

