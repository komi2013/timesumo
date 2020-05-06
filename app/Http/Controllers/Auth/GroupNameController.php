<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class GroupNameController extends Controller {

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
        if (!isset($relate->owner_flg) OR $relate->owner_flg == 0) {
            
            die('hey do not change group');
        }
        $arr_group = $request->arr_group;
        $now = date('Y-m-d H:i:s');
        $obj = DB::table('m_group')
                ->where("group_id", session('group_id'))
                ->get();
        $group = json_decode($obj,true);
        foreach ($group as $k => $d) {
            $group[$k]['action_by'] = $usr_id;
            $group[$k]['action_at'] = $now;
            $group[$k]['action_flg'] = 0;
        }
        DB::beginTransaction();
        DB::table('h_group')->insert($group);
        DB::table('m_group')
                ->where("group_id", session('group_id'))
                ->update([
                    "group_name" => $arr_group[session('group_id')]['group_name']
                ]);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);

    }
}

