<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoutineEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $group_owner = $request->session()->get('group_owner');
        $group_owner = true;
//        \App::setLocale('ja');
        $r = $request->routine;
        if( $r['group_id'] != $group_id) {
            die('you can not access this');
        }
        $i = 0;
        while ($i < 7) {
            $Hstart = 'Hstart_'.$i;
            $Hend = 'Hend_'.$i;
            $Mstart = 'Mstart_'.$i;
            $Mend = 'Mend_'.$i;
            $add['start_'.$i] = $r[$Hstart].':'.$r[$Mstart];
            $add['end_'.$i] = $r[$Hend].':'.$r[$Mend];
            ++$i;
        }

        $add['holiday_flg'] = $r['holiday_flg'] ? 1 : 0;
        $add['approver1'] = $r['approver1'];
        $add['approver2'] = $r['approver2'];
        $add['compensatory_within'] = $r['compensatory_within'];
        $add['fix_flg'] = $r['fix_flg'];
        $add['usr_id'] = $r['usr_id'];
        $add['group_id'] = $r['group_id'];
        $edit = false;
        $routine = DB::connection('shift')->table('r_routine')
                ->where('group_id', $group_id)
                ->where('usr_id', $r['usr_id'])
                ->first();
        if ( isset($routine->usr_id) ) { //update
            $i = 0;
            while ($i < 7) {
                $start = 'start_'.$i;
                $end = 'end_'.$i;
                $del['start_'.$i] = $routine->$start;
                $del['end_'.$i] = $routine->$end;
                ++$i;
            }
            $del['routine_id'] = $routine->routine_id;
            $del['holiday_flg'] = $routine->holiday_flg;
            $del['approver1'] = $routine->approver1;
            $del['approver2'] = $routine->approver2;
            $del['compensatory_within'] = $routine->compensatory_within;
            $del['fix_flg'] = $routine->fix_flg;
            $del['usr_id'] = $routine->usr_id;
            $del['group_id'] = $routine->group_id;
            $add['routine_id'] = $routine->routine_id;
            if ($del['approver1'] != $usr_id OR $del['approver2'] != $usr_id) {
                $edit = true;
            }
        } else {
            $add['routine_id'] = DB::select("select nextval('r_routine_routine_id_seq')")[0]->nextval;
        }
        if (!$group_owner AND !$edit) {
            die('not owner approver either');
        }
        DB::connection('shift')->beginTransaction();
        if($edit){
            DB::connection('shift')->table('h_routine')->insert($del);
            DB::connection('shift')->table('r_routine')
                    ->where("routine_id", $del['routine_id'])->delete();
        }
        DB::connection('shift')->commit();
        DB::connection('shift')->table('r_routine')->insert($add);
        $res[0] = 1;
        echo json_encode($res);
//        return view('shift.timesheet', compact('days','month'));
    }
}

