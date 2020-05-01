<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupUsrController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, 
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

