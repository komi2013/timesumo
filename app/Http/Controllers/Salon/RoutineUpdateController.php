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
                $routine['start_'.$i] = $r[0]['start_'.$i].':00';
                $routine['end_'.$i] = $r[0]['end_'.$i].':00';
            } else {
                $routine['start_'.$i] = null;
                $routine['end_'.$i] = null;
            }
            ++$i;
        }
        if ($request->session()->get('routine_id') > 0) {
            DB::beginTransaction();
            DB::table('r_routine')
                ->where('usr_id',$usr_id)
                ->where('group_id',$group_id)
                ->update($routine);
        } else {
            DB::beginTransaction();
            $routine['usr_id'] = $usr_id;
            $routine['group_id'] = $group_id;
            DB::table('r_routine')->insert($routine);
        }
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

