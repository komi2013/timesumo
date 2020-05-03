<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupGetController extends Controller {

    public function get(Request $request, $directory=null, $controller=null, 
            $action=null, $group_id=0, $oauth_type=null) {
        $request->session()->reflash();
        $usr_id = 2;
        $obj = DB::table('r_group_relate')->where("usr_id", $usr_id)->get();
        $arr_group = [];
        $group_ids = [];
        foreach ($obj as $d) {
           $group_ids[] = $d->group_id;
           $arr['group_id'] = $d->group_id;
           $arr['owner_flg'] = $d->owner_flg;
           $arr['priority'] = $d->priority;
           $arr_group[$d->group_id] = $arr;
        }
        $obj = DB::table('m_group')->whereIn("group_id", $group_ids)->get();
        foreach ($obj as $d) {
           $arr_group[$d->group_id]['group_name'] = $d->group_name;
           $arr_group[$d->group_id]['selected'] = '';
           if (!$group_id) {
               $group_id = $d->group_id;
           }
        }
        $group_ids = json_encode($group_ids);
        $request->session()->flash('group_ids', $group_ids);

        $obj = DB::table('r_group_relate')->where("group_id", $group_id)->get();
        $arr_usr = [];
        $usr_ids = [];
        foreach ($obj as $d) {
            $usr_ids[] = $d->usr_id;
        }
        $obj = DB::table('t_usr')
                ->select('usr_id','usr_name')
                ->whereIn("usr_id", $usr_ids)->get();
        foreach ($obj as $d) {
            $arr_usr[$d->usr_id][0] = $d->usr_id;
            $arr_usr[$d->usr_id][1] = $d->usr_name;
        }
        foreach ($arr_usr as $k => $d) {
            $arr_usr[$k][0] = $d[0];
            $arr_usr[$k][1] = $d[1];
        }
        $group_usrs = [];
        foreach ($arr_usr as $k => $d) {
            if (isset($d[0])) {
                $group_usrs[$k] = $d;
            }
        }
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
        echo json_encode($res);
    }
}

