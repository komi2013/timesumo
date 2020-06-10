<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShiftController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $arg1='', $arg2='') {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
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
                $arr[$start] = substr($d->$start, 0, 5);
                $arr[$end] = substr($d->$end, 0, 5);
                if ($d->$start) {
                    $arr['shift_'.$i] = 'O';
                    $startTime = substr($d->$start, 0, 5);
                    $endTime = substr($d->$end, 0, 5);
                } else {
                    $arr['shift_'.$i] = 'X';
                }
                ++$i;
            }
            $arr['fix_flg'] = $d->fix_flg;
        }
        $routine[0] = $arr;
        $request->session()->put('routine_id',$arr['routine_id']);
        $i = 0;
        while ($i < 7) {
            $week[] = __('salon.day'.$i); 
            ++$i;
        }
        $routine = json_encode($routine);
        $week = json_encode($week);
        return view('salon.shift', compact('routine','week','advance','startTime','endTime'));
    }
}

