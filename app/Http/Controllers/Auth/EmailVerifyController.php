<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailVerifyController extends Controller {

    public function s(Request $request, $directory=null, $controller=null,
            $action=null, $auth) {
        if ($auth != session('email_auth')) {
            return view('errors.404');
        }

        $obj = DB::connection('exam_manage')->table('t_manager')
            ->where('email',session('email'))
            ->first();
        if (isset($obj->manager_id) AND $obj->oauth_type == 3) {
            DB::connection('exam_manage')->table('t_manager')
                    ->where("manager_id",$obj->manager_id)
                    ->update([
                        "manager_pass" => session('manager_pass')
                        ,"updated_at" => now()
                    ]);
            $manager_id = $obj->manager_id;
        } else {
            $manager_id = DB::connection('exam_manage')
                    ->select("select nextval('t_manager_manager_id_seq')")[0]->nextval;
            DB::connection('exam_manage')->table('t_manager')->insert([
                "manager_id" => $manager_id
                ,"oauth_type" => 3
                ,"updated_at" => now()
                ,"email" => session('email')
                ,"manager_pass" => session('manager_pass')
            ]);
        }

        $request->session()->put('manager_id', $manager_id);

        return redirect('/Manage/Applicant/index/');
    }
}

