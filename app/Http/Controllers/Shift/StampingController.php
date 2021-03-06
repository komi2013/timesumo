<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampingController extends Controller {

    public function lessuri(Request $request,$directory,$controller,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $group = DB::table('m_group')
                ->where('group_id', $group_id)
                ->first();
        if ($group->password != $request->password) {
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning('password is wrong:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
            return json_encode([2,'password is wrong']);
        }
        $stamp = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        $now = date('Y-m-d H:i:s');
        if ($request->action == 'add' AND ($time_out OR !isset($stamp->timestamp_id))) {
            DB::table('t_timestamp')->insert([
                    'time_in' => $now
                    ,'usr_id' => $usr_id
                    ,"group_id" => $group_id
                    ,"latitude" => $request->latitude
                    ,"longitude" => $request->longitude
                    ,"public_ip" => $_SERVER["REMOTE_ADDR"]
                    ,"break_at" => '2000-01-01 00:00:00'
                ]);
        } else if ($request->action == 'edit' AND !$time_out) {
            DB::table('t_timestamp')
                ->where("timestamp_id",$stamp->timestamp_id)
                ->update([
                    'time_out' => $now
                    ,"latitude2" => $request->latitude
                    ,"longitude2" => $request->longitude
                    ,"public_ip2" => $_SERVER["REMOTE_ADDR"]
                ]);
        } else if ($request->action == 'breakStart') {
            DB::table('t_timestamp')
                ->where("timestamp_id",$stamp->timestamp_id)
                ->update([
                    'break_at' => $now
                ]);
        } else if ($request->action == 'breakEnd') {
            $dt = new Carbon();
            $break_at = new Carbon($stamp->break_at);
            $breakMin = $break_at->diffInMinutes($dt);
            DB::table('t_timestamp')
                ->where("timestamp_id",$stamp->timestamp_id)
                ->update([
                    'break_amount' => $breakMin + $stamp->break_amount
                    ,'break_at' => '2000-01-01 00:00:00'
                ]);
        }
        $res[0] = 1;
        return json_encode($res);
    }
}

