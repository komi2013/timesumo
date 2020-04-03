<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampingController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null,
            $param=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $stamp = DB::connection('shift')->table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        if ($request->action == 'add' AND ($time_out OR !isset($stamp->timestamp_id))) {
            DB::connection('shift')->table('t_timestamp')->insert([
                    'time_in' => now()
                    ,'usr_id' => $usr_id
                    ,"group_id" => $group_id
                ]);
        } else if ($request->action == 'edit' AND !$time_out) {
            DB::connection('shift')->table('t_timestamp')
                ->where("timestamp_id",$stamp->timestamp_id)
                ->update([
                    'time_out' => now()
                ]);
        }
        $res[0] = 1;
        die( json_encode($res) );
    }
}

