<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShiftController extends Controller {

    public function regular(Request $request, $directory=null, $controller=null,
            $action=null, $arg1='', $arg2='') {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $i = 0;
        while ($i < 7) {
            $arr['routine_id'] = 0;
            $arr['Hstart_'.$i] = '10';
            $arr['Hend_'.$i] = '20';
            $arr['Mstart_'.$i] = '00';
            $arr['Mend_'.$i] = '00';
            if ($i === 1 OR $i === 2) {
                $arr['shift_'.$i] = 'X';
            } else {
                $arr['shift_'.$i] = 'O';
            }
            $Hstart = $arr['Hstart_'.$i];
            $Hend = $arr['Hend_'.$i];
            $Mstart = $arr['Mstart_'.$i];
            $Mend = $arr['Mend_'.$i];
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
            while ($i < 7) {
                $start = 'start_'.$i;
                $end = 'end_'.$i;
                if ($d->$start) {
                    if (  ($start_time AND $start_time != $d->$start)
                       OR ($end_time AND $end_time != $d->$end ) ) {
                        $advance = true;
                    }
                    $start_time = $d->$start;
                    $end_time = $d->$end;
                }
                $arr['H'.$start] = substr($d->$start, 0, 2);
                $arr['H'.$end] = substr($d->$end, 0, 2);
                $arr['M'.$start] = substr($d->$start, 3, 2);
                $arr['M'.$end] = substr($d->$end, 3, 2);
                if ($d->$start) {
                    $arr['shift_'.$i] = 'O';
                    $Hstart = $arr['H'.$start];
                    $Hend = $arr['H'.$end];
                    $Mstart = $arr['M'.$start];
                    $Mend = $arr['M'.$end];
                } else {
                    $arr['shift_'.$i] = 'X';
                }
                ++$i;
            }
            $arr['fix_flg'] = $d->fix_flg;
        }
        $routine[0] = $arr;
        $request->session()->put('routine_id',$arr['routine_id']);

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
            $week[] = __('salon.day'.$i); 
            ++$i;
        }
        $routine = json_encode($routine);
        $startOption = json_encode($startOption);
        $endOption = json_encode($endOption);
        $minutes = json_encode($minutes);
        $week = json_encode($week);
        return view('salon.shift_regular', 
                compact('routine','startOption','endOption','minutes','week','advance',
                        'Hstart','Hend','Mstart','Mend'));
    }
}

