<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $dt = new Carbon();
        $dt->subMonths();
        $obj = DB::connection('timebook')->table('t_book')
                ->where('time_start','>',$dt->format('Y-m-d H:i:s'))
                ->orderBy('time_start','ASC')->get();
        $arr = json_decode($obj,true);
        $book = [];
        foreach ($arr as $k => $d) {
            if ($d['group_id'] == $group_id AND $d['review_to_usr'] == 0) {
                $book[$d['book_id']] = $d;
                $book[$d['book_id']]['time_start'] = date('H:i',strtotime($d['time_start']));
                $book[$d['book_id']]['time_end'] = date('H:i',strtotime($d['time_end']));
                $book[$d['book_id']]['booked_at'] = date(__('calendar.today').' H:i',strtotime($d['booked_at']));
                $book[$d['book_id']]['review_to_usr'] = 5;
            }
        }
        
        return view('salon.review', compact('book'));
        
    }
}

