<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class ScheduleController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {

        $test = Hash::make('komatsu');
        echo $test.'<br>';
        $fb_url = '';
        if (Hash::check('komatsu', $test)) {
            echo 'ok';
        } else {
            echo 'false';
        }
        die;
        return view('hair_salon.schedule', compact('fb_url'));
    }
}

