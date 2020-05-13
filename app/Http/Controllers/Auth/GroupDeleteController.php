<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class GroupOutController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {

        if (!session('usr_id')) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no session usr_id:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');

        $relate = DB::table('r_group_relate')
                ->where("usr_id", $usr_id)
                ->where("group_id", $group_id)
                ->first();
        if ($relate->owner_flg == 0) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no owner_flg:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no owner_flg']);
        }
        $now = date('Y-m-d H:i:s');
        $obj = DB::table('r_group_relate')
                ->whereIn("usr_id", $request->usrs)
                ->where("group_id", $group_id)
                ->get();
        $relate = json_decode($obj,true);
        foreach ($relate as $k => $d) {
            $relate[$k]['action_by'] = $usr_id;
            $relate[$k]['action_at'] = $now;
            $relate[$k]['action_flg'] = 0;
        }
        $obj = DB::table('t_facility')
                ->whereIn("facility_id", $request->usrs)
                ->where("group_id", $group_id)
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
                ->whereIn("usr_id", $request->usrs)
                ->where("group_id", $group_id)
                ->delete();
        DB::table('h_facility')->insert($facility);
        DB::table('t_facility')
                ->whereIn("facility_id", $request->usrs)
                ->where("group_id", $group_id)
                ->delete();
        DB::commit();
        $res[0] = 1;
        return json_encode($res);

    }
}

