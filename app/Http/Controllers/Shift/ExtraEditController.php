<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtraEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
//        \App::setLocale('ja');
//        $e = $request->extra;
        $r_group = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($r_group->usr_id)) {
            die('you should belong group at first');
        }
        $now = date('Y-m-d H:i:s');
        foreach ($request->extra as $k => $d) {
            if( $d['group_id'] != $group_id) {
                die('you can not access this');
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
        $routine = DB::connection('shift')->table('r_routine')
                ->where('usr_id', $target_usr)
                ->where('group_id', $group_id)
                ->first();
        if (!isset($routine->usr_id)) {
            die('you should go to routine page');
        }
        if ($r_group->owner_flg == 0 AND $routine->approver1 == 0 AND $routine->approver2 == 0) {
            die('you have no access right');
        }
        $edit = false;
        $del = DB::connection('shift')->table('r_extra')
                ->where('group_id', $group_id)
                ->where('usr_id', $add[0]['usr_id'])
                ->get();
        $del = json_decode($del,true);
        if ( isset($extra[0]['usr_id']) ) { //update
            foreach ($del as $k => $d) {
                $del[$k]['action_by'] = $usr_id;
                $del[$k]['action_at'] = $now;
                $del[$k]['action_flg'] = 1;
                $del[$k]['original_by'] = 'ExtraEditController';
            }
            $edit = true;
        }
        DB::connection('shift')->beginTransaction();
        if($edit){
            DB::connection('shift')->table('h_extra')->insert($del);
            DB::connection('shift')->table('r_extra')
                    ->whereIn("extra_id", $del['extra_id'])->delete();
        }
        DB::connection('shift')->table('r_extra')->insert($add);
        DB::connection('shift')->commit();
        
        $res[0] = 1;
        echo json_encode($res);
//        return view('shift.timesheet', compact('days','month'));
    }
}

