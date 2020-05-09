<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null, 
            $action=null, $group_id=null, $oauth_type=null) {
        $usr_id = $request->session()->get('usr_id');
        $bind = [
            'group_id' => $group_id
            ,'usr_id' => $usr_id
        ];
        $res = DB::select("SELECT * FROM r_group_relate WHERE group_id = :group_id "
                . " OR usr_id = :usr_id", $bind);
        $usr_ids = [];
        $usrs = [];
        $owner = false;
        $arr_group = [];
        $group_ids = [];
        foreach ($res as $d) {
            if ($d->group_id == $group_id) {
                if ($d->usr_id == $usr_id AND $d->owner_flg == 1) {
                    $owner = true;
                }
                $usr_ids[] = $d->usr_id;
                $arr2[0] = $d->usr_id;
                $arr2[2] = $d->owner_flg;
                $arr2[3] = $d->group_id;
                $usrs[$d->usr_id] = $arr2;
            }
            if ($d->usr_id == $usr_id) {
                $group_ids[] = $d->group_id;
                $arr['group_id'] = $d->group_id;
                $arr['owner_flg'] = $d->owner_flg;
                $arr_group[$d->group_id] = $arr;
            }
        }
        if (!$owner) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no owner:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no owner']);
        }
        $obj = DB::table('m_group')->whereIn("group_id", $group_ids)->get();
        $m_group = null;
        foreach ($obj as $d) {
            $arr_group[$d->group_id]['group_id'] = $d->group_id;
            $arr_group[$d->group_id]['group_name'] = $d->group_name;
            $arr_group[$d->group_id]['category_id'] = $d->category_id;
            $arr_group[$d->group_id]['selected'] = '';
            if ($d->group_id == $group_id) {
                $m_group = $d;
                if ($d->category_id == 1) {
                    $people = '';
                    $facility = 'selected';
                } else {
                    $people = 'selected';
                    $facility = '';
                }
            }
        }
        $obj = DB::table('t_usr')->whereIn("usr_id", $usr_ids)->get();

        foreach ($obj as $d) {
            $usrs[$d->usr_id][1] = $d->usr_name_mb;
            $usrs[$d->usr_id][4] = $d->oauth_type;
        }
        $join_usrs = [];
        $join_facility = [];
        $arr_is = [];
        $i = $i2 = 0;
        foreach ($usrs as $k => $d) {
            if ($d[4] != 5) {
                $join_usrs[$i] = $d;
                $join_usrs[$i][5] = null;
                $arr_is[] = $d[0];
                $i++;
            } else {
                $join_facility[$i2] = $d;
                $join_facility[$i2][5] = null;
                $i2++;
            }

        }
        if (count($arr_is) == 1) {
            $join_usrs[$arr_is[0]][5] = 'disabled';
            $join_facility[$arr_is[0]][5] = 'disabled';
        }
//            $arr[3] = $d->group_id;
//            $arr[2] = $d->owner_flg;
//                $arr_usr[$d->usr_id][0] = $d->usr_id;
//                $arr_usr[$d->usr_id][1] = $d->usr_name_mb;
//                $arr_usr[$d->usr_id][4] = $d->oauth_type;
//        res[arr_is[0]][5] = 'disabled';
//        dd($join_facility);
        $usr_ids = json_encode($usr_ids);
        $join_usrs = json_encode($join_usrs);
        $join_facility = json_encode($join_facility);
        return view('calendar.group_edit', compact('m_group','people','facility',
                'arr_group','usr_id','usr_ids','join_usrs','join_facility'));
    }
}

