<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupUsrController extends Controller {

    public function get(Request $request,$directory=null,$controller=null,$action=null,
            $word=null) {
        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $request->session()->reflash();
        $group_ids = json_decode(session('group_ids'),true) ?: [];
        $obj = DB::table('r_group_relate')->select('usr_id')
            ->whereIn("group_id", $group_ids)->get();
        $i = 1;
        $com_usr_id = '0';
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
        $sql = "SELECT * FROM t_usr WHERE usr_name like :word AND usr_id in (".$com_usr_id.")";

        $obj = DB::select($sql, $bind);
        $arr_usr = [];
        foreach ($obj as $d) {
            $arr = [$d->usr_id,$d->usr_name];
            $arr_usr[$d->usr_id] = $arr;
        }
        return json_encode($arr_usr);
    }
}

