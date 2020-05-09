<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $menu = DB::table('m_menu')->where('menu_id', $request->menu_id)->first();
        if ($request->staff > 0) {            
            if ($menu->group_id != $request->session()->get('group_id')) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('you are not staff:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return json_encode([2,'you are not staff']);
            }
            $customer = $request->customer;
        } else {
            $customer = \Cookie::get('usr_name');
        }
        $group_id = $menu->group_id;
        $obj = DB::table('m_menu_necessary')->where('menu_id', $menu->menu_id)->get();
        $end = 0;
        foreach ($obj as $d) {
            $arr_service_id[] = $d->service_id;
            $arr_facility_id[] = $d->facility_id;
            if ($end < $d->end_minute) {
                $end = $d->end_minute;
            }
            $arr['facility_id'] = $d->facility_id;
            $arr['service_id'] = $d->service_id;
            $arr['start_minute'] = $d->start_minute;
            $arr['end_minute'] = $d->end_minute;
            $arr['usr_ids'] = [];
            $necessary[$d->menu_necessary_id] = $arr;
            $insertFacilities[$d->facility_id] = [0,0];
        }
        $obj = DB::table('r_ability')
                ->whereIn('service_id', $arr_service_id)
                ->get();
        foreach ($obj as $d) {
            foreach ($necessary as $k2 => $d2) {
                if ($d->service_id == $d2['service_id']) {
                    $necessary_usr[$k2.'_'.$d->usr_id] = 0;
                    $arr['start_minute'] = date('Y-m-d H:i:s',($request->unix+$d2['start_minute']*60));
                    $arr['end_minute'] = date('Y-m-d H:i:s',($request->unix+$d2['end_minute']*60));
                    $usr_time[$k2.'_'.$d->usr_id] = $arr;
                }
            }
            $arr_usr_id[] = $d->usr_id;
        }
        $usr_facility_id = array_unique(array_merge($arr_usr_id,$arr_facility_id));
        $obj = DB::table('t_schedule')
                ->whereIn('usr_id', $usr_facility_id )
                ->where('time_end', '>',date('Y-m-d H:i:s',$request->unix)) // more take for other booking
                ->where('time_start', '<', date('Y-m-d H:i:s',($request->unix + $end * 60) )) // more take for other booking 
                ->get();
        foreach ($obj as $d) {
            foreach ($usr_time as $k2 => $d2) {
                $necessary_id = explode("_", $k2)[0];
                $uid = explode("_", $k2)[1];
                if ($d->usr_id == $uid) {
                    if ( $d->tag != 5 AND (
                         $d->time_start <= $d2['end_minute'] OR 
                         $d->time_end >= $d2['start_minute'] )
                            ) {  // got another task
                        --$necessary_usr[$necessary_id.'_'.$uid];
                    }
                    if ( $d->tag == 5 AND 
                         $d->time_start <= $d2['start_minute'] AND 
                         $d->time_end >= $d2['end_minute'] AND
                         $d->group_id == $group_id
                            ) {  // shift must be covered
                        ++$necessary_usr[$necessary_id.'_'.$uid];
                    }
                }
            }
        }
        foreach ($necessary_usr as $k => $d) {
            if ($d > 0) {
                $necessary_id = explode("_", $k)[0];
                $uid = explode("_", $k)[1];
                $priorityTime[$k] = $uid;
                $necessary[$necessary_id]['usr_ids'][] = $uid;
            }
        }
        $priorityTime = array_count_values($priorityTime);
        foreach ($priorityTime as $k => $d) {
            $insertUids[$k] = [0,0];
        }
        arsort($priorityTime);
        foreach ($necessary as $k => $d) {
            $sort[$k] = $d['start_minute'];
        }
        array_multisort($sort, SORT_ASC, $necessary);
        $arr_sql = [];

        DB::beginTransaction();
        $schedule_id = DB::select("select nextval('t_schedule_schedule_id_seq')")[0]->nextval;
        foreach ($necessary as $k => $d) {
            $fid = $d['facility_id'];
            if ( $d['start_minute'] == $insertFacilities[$fid][1] ) { // postpone 
                $insertFacilities[$fid] = [$insertFacilities[$fid][0], $d['end_minute']];
            } else if ( $d['start_minute'] < $insertFacilities[$fid][1] OR 
                    $insertFacilities[$fid][1] > 0) { //duplicate or skip time
                $arr_sql[] = 
                    ["schedule_id" => $schedule_id
                    ,"title" => $customer
                    ,"usr_id" => $fid
                    ,"time_start" => date('Y-m-d H:i:s',$request->unix + $insertFacilities[$fid][0] * 60)
                    ,"time_end" => date('Y-m-d H:i:s',$request->unix + $insertFacilities[$fid][1] * 60)
                    ,"tag" => 7
                    ,"group_id" => $group_id
                    ,"updated_at" => now()
                    ,"editable_flg" => 0];
                $insertFacilities[$fid] = [$d['start_minute'], $d['end_minute']];
            } else {
                $insertFacilities[$fid] = [$d['start_minute'], $d['end_minute']];
            }
            foreach ($priorityTime as $k2 => $d2) {
                foreach ($d['usr_ids'] as $d3) {
                    if ($k2 == $d3) {
                        if ( $d['start_minute'] == $insertUids[$d3][1]) { // postpone 
                            $insertUids[$d3] = [$insertUids[$d3][0], $d['end_minute']];
                        } else if ($d['start_minute'] < $insertUids[$d3][1]) { //duplicate
                            continue 2;
                        } else if ($insertUids[$d3][1] > 0) { //skip time
                            $arr_sql[] = 
                                ["schedule_id" => $schedule_id
                                ,"title" => $customer
                                ,"usr_id" => $d3
                                ,"time_start" => date('Y-m-d H:i:s',$request->unix + $insertUids[$d3][0] * 60)
                                ,"time_end" => date('Y-m-d H:i:s',$request->unix + $insertUids[$d3][1] * 60)
                                ,"tag" => 4
                                ,"group_id" => $group_id
                                ,"updated_at" => now()
                                ,"editable_flg" => 0];
                            $insertUids[$d3] = [$d['start_minute'], $d['end_minute']];
                            
                        } else {
                            $insertUids[$d3] = [$d['start_minute'], $d['end_minute']];
                        }
                        continue 3;
                    }
                }
            }
        }

        foreach ($insertUids as $k => $d) {
            if ($d[1]) {
                $arr_sql[] = 
                        ["schedule_id" => $schedule_id
                        ,"title" => $customer
                        ,"usr_id" => $k
                        ,"time_start" => date('Y-m-d H:i:s',$request->unix + $d[0] * 60)
                        ,"time_end" => date('Y-m-d H:i:s',$request->unix + $d[1] * 60)
                        ,"tag" => 4
                        ,"group_id" => $group_id
                        ,"updated_at" => date('Y-m-d H:i:s')
                        ,"editable_flg" => 0];           
            }
        }
        foreach ($insertFacilities as $facility_id => $d) {
            if ($facility_id > 0) {
                $arr_sql[] = 
                    ["schedule_id" => $schedule_id
                    ,"title" => $customer
                    ,"usr_id" => $facility_id
                    ,"time_start" => date('Y-m-d H:i:s',$request->unix + $d[0] * 60)
                    ,"time_end" => date('Y-m-d H:i:s',$request->unix + $d[1] * 60)
                    ,"tag" => 7
                    ,"group_id" => $group_id
                    ,"updated_at" => now()
                    ,"editable_flg" => 0];
            }
        }
        DB::table('t_schedule')->insert($arr_sql);
        DB::table('t_todo')->insert([
            "schedule_id" => $schedule_id
            ,"todo" => $menu->menu_name
            ,"updated_at" => now()
        ]);

        DB::commit();
        $res[0] = 1;
        return json_encode($res);

    }
}

