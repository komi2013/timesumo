<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $param=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $stamp = DB::connection('shift')->table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
//                ->where('time_in', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        $is = isset($stamp->timestamp_id) ? true : false;
//        var_dump($time_out);
//        var_dump($is);
//        die;
        $date = '';
        return view('shift.stamp', compact('date','time_out','is'));
    }
}

