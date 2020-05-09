<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $r_group = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        $now = date('Y-m-d H:i:s');

        foreach ($request->extra as $k => $d) {
            if( $d['group_id'] != $group_id) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('group is different:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'group is different']);
            }
            $start = $d['Hstart'].':'.$d['Mstart'].'00';
            $end = $d['Hend'].':'.$d['Mend'].'00';
            unset( $d['Hstart'],$d['Mstart'],$d['Hend'],$d['Mend'] );
            $add[$k] = $d;
            $add[$k]['dayoff_flg'] = $d['dayoff_flg'] ? 1 : 0;
            $add[$k]['extra_start'] = $start;
            $add[$k]['extra_end'] = $end;
            $add[$k]['updated_at'] = $now;
            $target_usr = $d['usr_id'];
        }

        $routine = DB::table('r_routine')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->first();
        if ($r_group->owner_flg == 0 AND $routine->approver1 == 0 AND $routine->approver2 == 0) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no access right']);
        }
        $edit = false;
        $del = DB::table('r_extra')
                ->where('group_id', $group_id)
                ->where('usr_id', $add[0]['usr_id'])
                ->get();
        $del = json_decode($del,true);
        $arr_extra_id = [];
        if ( isset($del[0]['usr_id']) ) { //update
            foreach ($del as $k => $d) {
                $del[$k]['action_by'] = $usr_id;
                $del[$k]['action_at'] = $now;
                $del[$k]['action_flg'] = 1;
                $del[$k]['original_by'] = 'ExtraEditController';
                $arr_extra_id[] = $d['extra_id'];
            }
            $edit = true;
        }

        DB::beginTransaction();
        if($edit){
            DB::table('h_extra')->insert($del);
            DB::table('r_extra')
                    ->whereIn("extra_id", $arr_extra_id)->delete();
        }
        DB::table('r_extra')->insert($add);
        DB::commit();
        
        $res[0] = 1;
        return json_encode($res);
    }
}

