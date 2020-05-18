<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $target_usr=null) {
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
        $rule = DB::table('r_rule')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if ($r_group->owner_flg == 0 AND $rule->approver1 == 0 AND $rule->approver2 == 0) {
            $msg = 'no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));
        }
        $usrs = [];
        $arr_usr_id = [];
        if ($r_group->owner_flg) {
            $obj = DB::table('r_group_relate')->where('group_id', $group_id)->get();
            foreach ($obj as $d) {
                $usrs[$d->usr_id] = '';
                $arr_usr_id[] = $d->usr_id;
            }
        }
        if ($rule->approver1 OR $rule->approver2) {
            $obj = DB::table('r_rule')->where('group_id', $group_id)->get();
            foreach ($obj as $d) {
                $usrs[$d->usr_id] = '';
                $arr_usr_id[] = $d->usr_id;
            }
        }
        $obj = DB::table('t_usr')->whereIn('usr_id', $arr_usr_id)->get();
        foreach ($obj as $d) {
            $usrs[$d->usr_id] = $d->usr_name;
        }
        $target_usr = $target_usr ?: $usr_id;
        $obj = DB::table('r_extra')
                ->where('group_id', $group_id)
                ->where('usr_id', $target_usr)
                ->get();
        foreach ($obj as $d) {
            $arr['extra_start'] = substr($d->extra_start, 0, 5);
            $arr['extra_end'] = substr($d->extra_end, 0, 5);
            $arr['dayoff_flg'] = $d->dayoff_flg;
            $arr['usr_id'] = $d->usr_id;
            $arr['group_id'] = $d->group_id;
            $arr['extra_ratio'] = $d->extra_ratio;
            $arr['over_flg'] = $d->over_flg;
            $arr['hour_start'] = $d->hour_start;
            $extra[] = $arr;
        }
        $add = $arr;
        $over_flg[0] = '';
        $over_flg[1] = __('calendar.month');
        $over_flg[2] = __('calendar.week');
        $over_flg[3] = __('calendar.day');
        $usr = DB::table('t_usr')->where('usr_id', $target_usr)->first();
        return view('shift.extra', compact('extra','usr','over_flg','add','usrs','target_usr'));
    }
}
