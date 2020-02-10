<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailLoginController extends Controller {

    public function lessuri(Request $request) {

        $obj = DB::connection('exam_manage')->table('t_manager')
            ->where('email',$request->email)
            ->first();
        $res[0] = 2;
        if (!isset($obj->manager_id)) {
            die(json_encode($res));
        }
        if ($obj->oauth_type != 3 OR $obj->manager_pass != $request->manager_pass) {
            die(json_encode($res));
        }
        $request->session()->put('manager_id', $obj->manager_id);
        $res[0] = 1;
        echo json_encode($res);
//        return redirect('/Manage/Applicant/index/' );
    }
}

