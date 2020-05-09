<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class RoutineUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $r = $request->routine;
        $i = 0;
        while ($i < 7) {
            if ($r[0]['shift_'.$i] === 'O') {
                $arr['start_'.$i] = $r[0]['Hstart_'.$i].':'.$r[0]['Mstart_'.$i].':00';
                $arr['end_'.$i] = $r[0]['Hend_'.$i].':'.$r[0]['Mend_'.$i].':00';
                $arr['break_start_'.$i] = $r[0]['Hbreak_start_'.$i].':'.$r[0]['Mbreak_start_'.$i].':00';
                $arr['break_end_'.$i] = $r[0]['Hbreak_end_'.$i].':'.$r[0]['Mbreak_end_'.$i].':00';   
            } else {
                $arr['start_'.$i] = null;
                $arr['end_'.$i] = null;
                $arr['break_start_'.$i] = null;
                $arr['break_end_'.$i] = null;
            }
            $arr['break_start_'.$i] = strlen($arr['break_start_'.$i]) === 7 ? $arr['break_start_'.$i] : null;
            $arr['break_end_'.$i] = strlen($arr['break_end_'.$i]) === 7 ? $arr['break_end_'.$i] : null;
            ++$i;
        }
        if ($request->session()->get('routine_id') > 0) {
            DB::beginTransaction();
            DB::table('r_routine')
                ->where('usr_id',$usr_id)
                ->where('group_id',$group_id)
                ->update($arr);
        } else {
            DB::beginTransaction();
            $arr['usr_id'] = $usr_id;
            $arr['group_id'] = $group_id;
            DB::table('r_routine')->insert($arr);
        }
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

