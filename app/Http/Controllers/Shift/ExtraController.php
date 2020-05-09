<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $target_usr=0) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $r_group = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        $routine = DB::table('r_routine')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if ($r_group->owner_flg == 0 AND $routine->approver1 == 0 AND $routine->approver2 == 0) {
            $msg = 'no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));
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
        $usr_name = $usr->usr_name;
        return view('shift.extra', compact('extra','hours','minutes','usr','over_flg',
                'new','is_data','usr_name'));
    }
}
