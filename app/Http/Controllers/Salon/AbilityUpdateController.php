<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class AbilityUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 4;

        $request->ability;
        foreach ($request->ability as $k => $d) {
            $arr['usr_id'] = $usr_id;
            $arr['service_id'] = $d; 
            $ability[$k] = $arr;
        }

        DB::beginTransaction();
        DB::table('t_ability')->where('usr_id', $usr_id)->delete();
        DB::table('t_ability')->insert($ability);
        DB::commit();
        $res[0] = 1;
        die( json_encode($res) );
    }
}

