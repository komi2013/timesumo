<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookedController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        \App::setLocale($request->cookie('lang'));
//        予約の日時、予約した日時、店の名前、メニュー名、予約番号
        $month = $month ?: date('Y-m');
        $begin = $month.'-01 00:00:00';
        $dt = new Carbon($begin);
        $dt->addMonth();
        $end = $dt->format('Y-m-d 00:00:00');
        $booked = DB::table('h_booked')
                ->where('updated_at','>',$begin)
                ->where('updated_at','<',$end)
                ->orderBy('updated_at','ASC')->get();
//        var_dump($begin,$end);
//        die('dhi');
        return view('user.booked', compact('booked'));
        
    }
}

