<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TmpController extends Controller {

    public function index(Request $request, $directory=null, $controller=null, 
            $action=null, $group_type_id=null, $oauth_type=null) {
        
        $request->session()->put('usr_id', $request->input('usr_id'));
        echo $request->session()->get('usr_id');
        $request->session()->put('usr_name_mb', $request->input('usr_name_mb'));
        echo $request->session()->get('usr_name_mb');
//        return view('calendar.tmp');
//        die;
    }
}

