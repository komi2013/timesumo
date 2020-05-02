<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $target_usr=0) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
//        \App::setLocale('ja');
        $r_group = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($r_group->usr_id)) {
            die('you should belong group at first');
        }
        $routine = DB::table('r_routine')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($routine->usr_id)) {
            die('you should go to routine page');
        }
        if ($r_group->owner_flg == 0 AND $routine->approver1 == 0 AND $routine->approver2 == 0) {
            die('you have no access right');
        }
        $obj = DB::table('r_extra')
                ->where('group_id', $group_id)
                ->get();
        $arr['Hstart'] = '08';
        $arr['Hend'] = '22';
        $arr['Mstart'] = '00';
        $arr['Mend'] = '00';
        $arr['dayoff_flg'] = 0;
        $arr['usr_id'] = $target_usr;
        $arr['group_id'] = $group_id;
        $arr['extra_percent'] = 0;
        $arr['over_flg'] = 0;
        $arr['hour_start'] = 0;
        $arr['hour_end'] = 0;
        $new = $arr;
        $extra = [];
        $previous = [];
        $is_data = 0;
        foreach ($obj as $d) {
            $arr['Hstart'] = substr($d->extra_start, 0, 2);
            $arr['Hend'] = substr($d->extra_end, 0, 2);
            $arr['Mstart'] = substr($d->extra_start, 3, 2);
            $arr['Mend'] = substr($d->extra_end, 3, 2);
            $arr['dayoff_flg'] = $d->dayoff_flg;
            $arr['usr_id'] = $d->usr_id;
            $arr['group_id'] = $d->group_id;
            $arr['extra_percent'] = $d->extra_percent;
            $arr['over_flg'] = $d->over_flg;
            $arr['hour_start'] = $d->hour_start;
            $arr['hour_end'] = $d->hour_end;
            if ($target_usr == $d->usr_id) {
                $extra[] = $arr;
            } else {
                $previous[0] = $arr;
            }
            $is_data = 1;
        }

        if (count($extra) == 0 AND $is_data > 0) {
            $extra = $previous;
        }
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
        $over_flg[0] = '';
        $over_flg[1] = __('calendar.month');
        $over_flg[2] = __('calendar.week');
        $over_flg[3] = __('calendar.day');
        $usr = DB::table('t_usr')->where('usr_id', $target_usr)->first();
//        $usr = json_decode($usr,true);
        $usr_name = $usr->usr_name;
        return view('shift.extra', compact('extra','hours','minutes','usr','over_flg',
                'new','is_data','usr_name'));
    }
}
