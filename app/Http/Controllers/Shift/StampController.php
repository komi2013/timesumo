<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $password='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $group = DB::table('m_group')
                ->where('group_id', $group_id)
                ->first();
        if ($group->password != $password) {
            die('no accesss right');
        }
        $stamp = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        $is = isset($stamp->timestamp_id) ? true : false;
        $pause = false;
        $start = new Carbon($stamp->time_in);
        $break_at = new Carbon($stamp->break_at);
        if ($start < $break_at) {
            $pause = true;
        }
        $date = new Carbon();
        return view('shift.stamp', compact('date','time_out','is','pause','password'));
    }
}

