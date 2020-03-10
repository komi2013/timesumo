<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,
            $action=null, $menu_id='', $language='') {
//        if (!$request->session()->get('usr_id')) {
//            return redirect('/Auth/Sign/in/0/');
//        }
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;
        $group_id = 1;
        \App::setLocale('ja');
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $today = Carbon::today();
        $frameDate = Carbon::createFromDate($today->year, $today->month, $today->startOfWeek()->format('d'));
        $shop = DB::connection('salon')->table('t_shop')->where('group_id', $group_id)->first();
        $openHour = substr($shop->open_time,0,2);
        $openMinute = substr($shop->open_time,3,2);
        $frameDate->setTime($openHour, $openMinute, 0);
        $closeHour = substr($shop->close_time,0,2);
        $days21 = [];
        $i = 0;
        while ($i < 21) {
//            $hour = $openHour;
//            while ($hour < $closeHour) {
                $days21[$frameDate->format('Y-m-d')] = [];
//                $frameDate->addHour();
//                ++$hour;
//            }
            $frameDate->addDay();
//            $frameDate->setTime($openHour, $openMinute, 0);
            ++$i;
        }
        $frameDate->subDay();
//echo $frameDate->format('Y-m-d ').$shop->close_time; die;
        $obj = DB::table('t_schedule')
                ->where('time_start','>=',date('Y-m-d ').$shop->open_time)
                ->where('time_end','<=',$frameDate->format('Y-m-d ').$shop->close_time)
                ->where('group_id',$group_id)
                ->where('tag',6)
                ->orderBy('time_start','ASC')->get();
        foreach ($obj as $d) {
            
            $days21[substr($d->time_start,0,10)][$d->schedule_id] = substr($d->time_start,11,5).' '.$d->title;

        }
//        dd($days21);
        return view('hair_salon.cancel', 
                compact('days21','openHour','closeHour'));
    }
}

