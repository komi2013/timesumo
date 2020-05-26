<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopController extends Controller {

    public function index(Request $request,$directory=null,$controller=null,$action=null,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        \App::setLocale($request->cookie('lang'));
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        if ($month) {
            $dt = new Carbon($month.'-01');
        } else {
            $dt = new Carbon(date('Y-m-01'));
        }
        $today = $dt->format(__('calendar.month_f'));
        $prev = new Carbon($dt->format('Y-m-d'));
        $prev = $prev->subMonth(1);
        $prev = $prev->format('Y-m');
        $next = new Carbon($dt->format('Y-m-d'));
        $next = $next->addMonth(1);
        $next = $next->format('Y-m');
        $varDate = new Carbon($dt->startOfWeek()->format('Y-m-d'));

        $begin = $varDate->format('Y-m-d 00:00:00');
        $i = 0;
        while ($i < 35) {
            $day35[$varDate->format('Y-m-d')] = [];
            $varDate->addDay();
            ++$i;
        }
        $cal_url = '/Calendar/Schedule/index/';
        $url = $_SERVER['SERVER_NAME'] == 'timebook.quigen.info' ? '/User/Schedule/index/' : $cal_url;
        $end = $varDate->format('Y-m-d 00:00:00');
        $obj = DB::table('t_schedule')
                ->where('time_end','>',$begin)
                ->where('time_start','<',$varDate->format('Y-m-d H:i:s'))
                ->where('usr_id',$usr_id)
                ->orderBy('time_start','ASC')->get();
        foreach ($obj as $d) {
            $day35[substr($d->time_start,0,10)][$d->schedule_id] = [$d->title,$d->tag,$url];
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            while ($start->diffInDays($end) > 0) {
                $start->addDay();
                $day35[$start->format('Y-m-d')][$d->schedule_id] = [$d->title,$d->tag,$url];
            }
        }
        if ($_SERVER['SERVER_NAME'] == 'timebook.quigen.info') {
            $obj_sync = DB::table('t_sync')->where('usr_id',$usr_id)->get();
            foreach ($obj_sync as $dd) {
                $db = DB::table('c_db')->where('db_id',$dd->db_id)->first();
                \Config::set('database.connections.dynamic.host',$db->host);
                \Config::set('database.connections.dynamic.database',$db->database);
                \Config::set('database.connections.dynamic.username',$db->username);
                \Config::set('database.connections.dynamic.password',$db->password);
                $cal_url = 'https://'.$db->domain.$cal_url;
                $obj = DB::connection('dynamic')->table('t_schedule')
                        ->where('time_end','>',$begin)
                        ->where('time_start','<',$varDate->format('Y-m-d H:i:s'))
                        ->where('usr_id',$dd->sync_usr_id)
                        ->orderBy('time_start','ASC')->get();
                foreach ($obj as $d) {
                    $day35[substr($d->time_start,0,10)][$d->schedule_id] = [$d->title,$d->tag,$cal_url];
                    $start = new Carbon($d->time_start);
                    $end = new Carbon($d->time_end);
                    while ($start->diffInDays($end) > 0) {
                        $start->addDay();
                        $day35[$start->format('Y-m-d')][$d->schedule_id] = [$d->title,$d->tag,$cal_url];
                    }
                }
            }            
        }

        return view('calendar.top', compact('day35','today','prev','next','url'));
        
    }
}

