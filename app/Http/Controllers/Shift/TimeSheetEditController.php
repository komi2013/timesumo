<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeSheetEditController extends Controller {

    public function lessuri(Request $request,$directory,$controller,$action) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        
        $month = $request->month ?? date('Y-m');
        $thisMonth = new Carbon($month);
        $thisMonth = new Carbon($thisMonth->format('Y-m-01 00:00:00'));
        $begin = $thisMonth->format('Y-m-d H:i:s');
        $i = 0;
        $monthly = [];
        $endOfMonth = $thisMonth->daysInMonth;
        while ($i < $endOfMonth) {
            $arr = [];
            $arr['time_in'] = '';
            $arr['time_out'] = '';
            $date = str_pad($i + 1, 2, 0, STR_PAD_LEFT);
            $arr['day'] = __('calendar.day'.$thisMonth->format('w'));
            $monthly[$date] = $arr;
            $thisMonth->addDay();
            ++$i;
        }
        $thisMonth->addDay();
        $obj = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->where('time_in','>', $begin)
                ->where('time_in','<', $thisMonth->format('Y-m-01 00:00:00'))
                ->get();
        $arr = json_decode($obj,true);
        foreach ($arr as $d) {
            $date = date('d',strtotime($d['time_in']));
            $monthly[$date] = $d;
            $monthly[$date]['time_in'] = substr($d['time_in'], 11,5);
            $monthly[$date]['time_out'] = substr($d['time_out'],11,5);
        }
        $del = [];
        $add = [];
        $ids = [];
        foreach ($request->days as $d) {
            
            if( $monthly[$d['date']]['time_in'] != $d['time_in'] OR
                $monthly[$d['date']]['time_out'] != $d['time_out'] ){
                $action_flg = 0;
                if( $d['time_in'] OR
                    $d['time_out'] ){
                    $arr = [];
                    $time_in = $request->month.'-'.$d['date'].' '.$d['time_in'].':00';
                    $time_out = $request->month.'-'.$d['date'].' '.$d['time_out'].':00';
                    $arr['time_in'] = $time_in;
                    $arr['time_out'] = $time_out;
                    $arr['group_id'] = $group_id;
                    $arr['usr_id'] = $usr_id;
                    $arr['manual_flg'] = 1;
                    $add[] = $arr;
                    $action_flg = 1;
                }
                if( $monthly[$d['date']]['time_in'] OR
                    $monthly[$d['date']]['time_out'] ){
                    $arr = $monthly[$d['date']];
                    $time_in = $request->month.'-'.$d['date'].' '.$monthly[$d['date']]['time_in'].':00';
                    $time_out = $request->month.'-'.$d['date'].' '.$monthly[$d['date']]['time_out'].':00';
                    $arr['time_in'] = $time_in;
                    $arr['time_out'] = $time_out;
                    $arr['action_by'] = $usr_id;
                    $arr['action_at'] = date('Y-m-d H:i:s');
                    $arr['action_flg'] = $action_flg;
                    $del[] = $arr;
                    $ids[] = $monthly[$d['date']]['timestamp_id'];
                    $action_flg = 0;
                }
            }
        }
        DB::beginTransaction();
        if(count($ids)){
            DB::table('h_timestamp')->insert($del);
            DB::table('t_timestamp')
                    ->whereIn("timestamp_id", $ids)->delete();
        }
        DB::table('t_timestamp')->insert($add);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

