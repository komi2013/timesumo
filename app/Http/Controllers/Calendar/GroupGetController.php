<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupGetController extends Controller {

    public function get(Request $request, $directory=null, $controller=null, 
            $action=null, $group_id=0, $oauth_type=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $request->session()->reflash();
        $obj = DB::table('r_group_relate')->where("usr_id", $usr_id)->get();
        $arr_group = [];
        $group_ids = [];
        foreach ($obj as $d) {
           $group_ids[] = $d->group_id;
           $arr['group_id'] = $d->group_id;
           $arr['owner_flg'] = $d->owner_flg;
           $arr['priority'] = $d->priority;
           $arr_group[$d->group_id] = $arr;
           $is_group = true;
        }
        $obj = DB::table('m_group')->whereIn("group_id", $group_ids)->get();
        foreach ($obj as $d) {
           $arr_group[$d->group_id]['group_name'] = $d->group_name;
           $arr_group[$d->group_id]['selected'] = '';
           if (!$group_id) {
               $group_id = $d->group_id;
           }
        }
        if (!isset($is_group)) {
            $arr_group[0]['group_id'] = 0;
            $arr_group[0]['group_name'] = '';
            $arr_group[0]['owner_flg'] = 0;
            $arr_group[0]['priority'] = 0;
            $arr_group[0]['selected'] = '';
        }
        $group_ids = json_encode($group_ids);
        $request->session()->flash('group_ids', $group_ids);

        $obj = DB::table('r_group_relate')->where("group_id", $group_id)->get();
        $group_usrs = [];
        $usr_ids = [];
        foreach ($obj as $d) {
            $usr_ids[] = $d->usr_id;
        }
        $obj = DB::table('t_usr')
                ->select('usr_id','usr_name','token')
                ->whereIn("usr_id", $usr_ids)->get();
        foreach ($obj as $d) {
            $group_usrs[$d->usr_id][0] = $d->usr_id;
            $group_usrs[$d->usr_id][1] = $d->usr_name;
            $group_usrs[$d->usr_id][2] = $d->token;
        }
        
//        foreach ($arr_usr as $k => $d) {
//            if (isset($d[0])) {
//                $group_usrs[$k] = $d;
//            }
//        }
        $obj = DB::table('t_facility')
                ->select('facility_id','facility_name','amount')
                ->where("group_id", $group_id)->get();
        $group_facility = [];
        foreach ($obj as $d) {
            $arr[0] = $d->facility_id;
            $arr[1] = $d->facility_name;
            $arr[2] = $d->amount;
            $group_facility[] = $arr;
        }

        $request->session()->put('group_id', $group_id);
        $res[0] = 1;
        $res[1] = $group_usrs;
        $res[2] = $group_facility;
        $res[3] = $group_ids;
        $res[4] = $arr_group;
        $res[5] = $group_id;
        return json_encode($res);
    }
}

