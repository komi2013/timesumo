<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UsrNameController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        $usr = DB::table('t_usr')
                ->where('usr_id',session('usr_id'))
                ->update([
                    'usr_name' => $request->usr_name
                ]);
        $res[0] = 1;
        return json_encode($res);

    }
}

