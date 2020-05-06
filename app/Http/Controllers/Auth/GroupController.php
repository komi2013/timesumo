<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class GroupController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;


        $relate = DB::table('r_group_relate')
                ->where("usr_id", $usr_id)
                ->where("group_id", session('group_id'))
                ->first();
        if ($relate->owner_flg == 0) {
            die('hey do not change group');
        }
        $now = date('Y-m-d H:i:s');
        $obj = DB::table('r_group_relate')
                ->whereIn("usr_id", $request->removeUsr)
                ->where("group_id", session('group_id'))
                ->get();
        $relate = json_decode($obj,true);
        foreach ($relate as $k => $d) {
            $relate[$k]['action_by'] = $usr_id;
            $relate[$k]['action_at'] = $now;
            $relate[$k]['action_flg'] = 0;
        }
        $obj = DB::table('t_facility')
                ->whereIn("facility_id", $request->removeUsr)
                ->where("group_id", session('group_id'))
                ->get();
        $facility = json_decode($obj,true);
        foreach ($facility as $k => $d) {
            $facility[$k]['action_by'] = $usr_id;
            $facility[$k]['action_at'] = $now;
            $facility[$k]['action_flg'] = 0;
        }
        
        DB::beginTransaction();
        DB::table('h_group_relate')->insert($relate);
        DB::table('r_group_relate')
                ->whereIn("usr_id", $request->removeUsr)
                ->where("group_id", session('group_id'))
                ->delete();
        DB::table('h_facility')->insert($facility);
        DB::table('t_facility')
                ->whereIn("facility_id", $request->removeUsr)
                ->where("group_id", session('group_id'))
                ->delete();
        DB::commit();
        $res[0] = 1;
        return json_encode($res);

    }
}

