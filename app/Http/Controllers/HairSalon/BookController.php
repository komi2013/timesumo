<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $menu_id='', $staff=0) {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
//        \Cookie::queue('lang', $lang);
        $request->session()->put('group_id',6);
        \App::setLocale('ja');
        $menu = DB::table('t_menu')->where('menu_id', $menu_id)->first();
        if ($staff > 0) {
            if ($menu->group_id != $request->session()->get('group_id')) {
                \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
                \Log::warning('you are not staff:'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST));
                return view('404');
            }
            $customer = '';
        } else {
            $customer = \Cookie::get('usr_name');
        }
        $group_id = $menu->group_id;

        $shop = DB::table('m_group')->where('group_id', $group_id)->first();
        
        $today = Carbon::today();
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $frameDate = Carbon::createFromDate($today->year, $today->month, $today->startOfWeek()->format('d'));
        $openHour = substr($shop->open_time,0,2);
        $frameDate->setTime($openHour, 0, 0);
        $begin = $frameDate->format('Y-m-d H:00:00');
        $obj = DB::table('t_menu_necessary')->where('menu_id', $menu_id)->get();
        foreach ($obj as $d) {
            $required['service_'.$d->service_id] = 0;
            $required['facility_'.$d->facility_id] = 1;
            $arr_service_id[] = $d->service_id;
            $arr_facility_id[] = $d->facility_id;
        }
        $necessary = json_decode($obj,true);
        $obj = DB::table('t_facility')->whereIn('facility_id', $arr_facility_id)->get();
        foreach ($obj as $d) {
            $required['facility_'.$d->facility_id] = $d->amount;
        }
        $obj = DB::table('t_ability')->whereIn('service_id', $arr_service_id)->get();
        foreach ($obj as $d) {
            $arr[] = $d->service_id;
            $ability[$d->usr_id] = $arr;
            $arr_usr_id[] = $d->usr_id;
        }
        $closeHour = substr($shop->close_time,0,2);
        if (substr($shop->close_time,3,2) != '00') {
            ++$closeHour;
        }
        $i = 0;
        while ($i < 21) {
            $hour = $openHour;
            while ($hour < $closeHour) {
                $frame = 0;
                while ($frame < 6) {
                    $required['available'] = false;
                    $days21[$frameDate->format('Y-m-d H:i:s')] = $required;
                    $frameDate->addSecond(60 * 10);
                    ++$frame;
                }
                ++$hour;
            }
            $frameDate->addDay();
            $frameDate->setTime($openHour, 0, 0);
            ++$i;
        }

        $frameDate->subDay();
        $frameDate->setTime($closeHour, 0, 0);
        $end = $frameDate->format('Y-m-d H:00:00');
        $usr_facility_id = array_unique(array_merge($arr_usr_id,$arr_facility_id));
        $obj = DB::table('t_schedule')
                ->whereIn('usr_id', $usr_facility_id )
                ->where('time_end', '>',date('Y-m-d H:i:s')) // more take for other booking
                ->where('time_start', '<', $end) // more take for other booking 
                ->get();
//dd(json_decode($obj,true));
        foreach ($obj as $d) {
            $start = new Carbon($d->time_start);
            $end = new Carbon($d->time_end);
            while ($start < $end) {
                $startI = floor($start->format('i')/10) * 10;
                $k = $start->format('Y-m-d H:').str_pad($startI, 2, 0, STR_PAD_LEFT).':00';
                if (isset($days21[$k])) {
//                    $days21[$k]['schedule_ids'][] = $d->schedule_id;
                    if (in_array($d->usr_id,$arr_facility_id)) {
                        --$days21[$k]['facility_'.$d->usr_id];
                        $facilities[$d->usr_id] = $d->schedule_id;
                    } else if ($d->tag == 5) {
                        foreach ($ability[$d->usr_id] as $service_id) {
                            ++$days21[$k]['service_'.$service_id];
                        }
                    } else if ($d->tag != 5) {
                        foreach ($ability[$d->usr_id] as $service_id) {
                            --$days21[$k]['service_'.$service_id];
                            $services[$service_id] = $d->schedule_id;
                        }
                    }  
                }
                $start->addSecond(60 * 10);
            }
        }
        $del = [];
        foreach ($days21 as $date => $d) {
            $available = true;
            $end_minute = 0;
            foreach ($necessary as $d2) {
                $start = new Carbon($date);
                $start->addSecond(60 * $d2['start_minute']);
                $i = $d2['start_minute'];
                while ($i < $d2['end_minute']) {
                    $k = $start->format('Y-m-d H:i:s');                 
                    if (isset($days21[$k]['available']) AND
                        ($days21[$k]['service_'.$d2['service_id']] < 0 OR 
                         $days21[$k]['facility_'.$d2['facility_id']] < 0)) {
                        $available = false;
                        if ($days21[$k]['service_'.$d2['service_id']] < 0) {
                            $del['service_'.$d2['service_id']] = $services[$d2['service_id']];
                        }
                        if ($days21[$k]['facility_'.$d2['facility_id']] < 0) {
                            $del['facility_'.$d2['facility_id']] = $facilities[$d2['facility_id']];
                        }
                    } else if (isset($days21[$k]['available']) AND
                        ($days21[$k]['service_'.$d2['service_id']] < 1 OR 
                         $days21[$k]['facility_'.$d2['facility_id']] < 1)) {
                        $available = false;
                    }
                    $start->addSecond(60 * 10);
                    $i = $i + 10;
                }
                if ($end_minute < $d2['end_minute']) {
                    $end_minute = $d2['end_minute'];
                }
            }
            if (new Carbon($date) > new Carbon()) {
                $days21[$date]['available'] = $available;
            } else {
                $days21[$date]['available'] = false;
            }
            
        }
        $del = array_unique($del);
        arsort($del);
        if (count($del) > 0) {
            // delete statement
        }
//        dd($days21);
        $today = date('Y-m-d');
        $openTime = $openHour.':00';
        $closeTime = $closeHour - 1 .':50';
        return view('hair_salon.book', 
            compact('days21','today','menu_id','openTime','closeTime','menu','end_minute','staff','customer'));
    }
}