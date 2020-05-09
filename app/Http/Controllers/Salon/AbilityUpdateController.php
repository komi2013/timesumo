<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class AbilityUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        $request->ability;
        foreach ($request->ability as $k => $d) {
            $arr['usr_id'] = $usr_id;
            $arr['service_id'] = $d; 
            $ability[$k] = $arr;
        }

        DB::beginTransaction();
        DB::table('r_ability')->where('usr_id', $usr_id)->delete();
        DB::table('r_ability')->insert($ability);
        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

