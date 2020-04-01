<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupGetController extends Controller {

    public function get(Request $request, $directory=null, $controller=null, 
            $action=null, $group_id=null, $oauth_type=null) {
        $bind = [
            'group_id' => $group_id
        ];        
        $obj = DB::select("SELECT * FROM r_group_relate WHERE group_id = :group_id ", $bind);
        $arr_usr = [];
        $usr_ids = [];
        foreach ($obj as $d) {
            $usr_ids[] = $d->usr_id;
//            $arr[3] = $d->group_id;
//            $arr[2] = $d->owner_flg;
//            $arr_usr[$d->usr_id] = $arr;
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
//            $arr_usr[$k][2] = $d[2];
//            $arr_usr[$k][3] = $d[3];
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
        $res[0] = 1;
        $res[1] = $group_usrs;
        $res[2] = $group_facility;
        die( json_encode($res) );
    }
    public function searchUsr(Request $request, $directory=null, $controller=null, 
            $action=null, $word=null, $oauth_type=null) {
        $obj = DB::table('r_group_relate')->select('usr_id')
            ->whereIn("group_id", $_GET['group_ids'])->get();
        $i = 1;
        $com_usr_id = '';
        $usr_ids = [];
        foreach ($obj as $d) {
            if (!in_array($d->usr_id, $usr_ids)) {
                if ($i == 1) {
                    $com_usr_id = $d->usr_id;
                }else{
                    $com_usr_id .= ','.$d->usr_id;
                }
                $usr_ids[] = $d->usr_id;
                $i++;
            }
        }
        $bind = [
            'word' => '%'.$word.'%'
        ];
        if(strlen($word) == mb_strlen($word,'utf8')) {
            $sql = "SELECT * FROM t_usr WHERE usr_name like :word AND usr_id in (".$com_usr_id.")";
        }else{
            $sql = "SELECT * FROM t_usr WHERE usr_name_mb like :word AND usr_id in (".$com_usr_id.")";
        }
        if ($oauth_type == 'facility') {
            $oauth_type_and = ' AND oauth_type = 5';
        } else {
            $oauth_type_and = ' AND oauth_type <> 5';
        }
        $obj = DB::select($sql.$oauth_type_and, $bind);
        $arr_usr = [];
        foreach ($obj as $d) {
            $arr = [$d->usr_id,$d->usr_name_mb];
            $arr_usr[$d->usr_id] = $arr;
        }
        die(json_encode($arr_usr));
    }
}

