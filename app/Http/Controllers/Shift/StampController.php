<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampController extends Controller {

    public function index(Request $request,$directory,$controller,$action,
            $password='') {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $group = DB::table('m_group')
                ->where('group_id', $group_id)
                ->first();
        if ($group->password != $password) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'no access right']);
        }
        $stamp = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        $is = isset($stamp->timestamp_id) ? true : false;
        $pause = false;
        if (isset($stamp->time_in)) {
            $start = new Carbon($stamp->time_in);
            $break_at = new Carbon($stamp->break_at);
            if ($start < $break_at) {
                $pause = true;
            }            
        }
        $date = new Carbon();
        return view('shift.stamp', compact('date','time_out','is','pause','password'));
    }
}

