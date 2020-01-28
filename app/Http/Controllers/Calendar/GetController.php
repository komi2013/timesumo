<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GetController extends Controller {

    public function groupUsr(Request $request, $directory=null, $controller=null, 
            $action=null, $group_id=null, $oauth_type=null) {
        $bind = [
            'group_id' => $group_id
        ];        
        $obj = DB::select("SELECT * FROM r_group_relate WHERE group_id = :group_id ", $bind);
        $arr_usr = [];
        $usr_ids = [];
        foreach ($obj as $d) {
            $usr_ids[] = $d->usr_id;
            $arr[3] = $d->group_id;
            $arr[2] = $d->owner_flg;
            $arr_usr[$d->usr_id] = $arr;
        }
        $obj = DB::table('t_usr')
                ->select('usr_id','usr_name_mb','oauth_type')
                ->whereIn("usr_id", $usr_ids)->get();
//        if ($oauth_type == 'facility') {
//            $obj = DB::table('t_usr')->select('usr_id','usr_name_mb','usr_name_mb')
//                    ->whereIn("usr_id", $usr_ids)->get();
//        } else {
//            $obj = DB::table('t_usr')->select('usr_id', 'usr_name_mb')
//                    ->whereIn("usr_id", $usr_ids)->where("oauth_type","<>",5)->get();
//        }
        foreach ($obj as $d) {
            if ($oauth_type == 'facility' AND $d->oauth_type == 5) {
                $arr_usr[$d->usr_id][0] = $d->usr_id;
                $arr_usr[$d->usr_id][1] = $d->usr_name_mb;                
            } else if ($oauth_type == 'people' AND $d->oauth_type != 5) {
                $arr_usr[$d->usr_id][0] = $d->usr_id;
                $arr_usr[$d->usr_id][1] = $d->usr_name_mb;                
//            } else if ($oauth_type == 'facility_owner') {
//                $arr_usr[$d->usr_id][0] = $d->usr_id;
//                $arr_usr[$d->usr_id][1] = $d->usr_name_mb;
//                $arr_usr[$d->usr_id][4] = $d->oauth_type;
            }
        }
        $arr = [];
        foreach ($arr_usr as $k => $d) {
            if (isset($d[0])) {
                $arr[$k] = $d;
            }
        }
        die(json_encode($arr));
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

