<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailCheckController extends Controller {

    public function lessuri(Request $request) {
        $usr = DB::table('t_usr')
            ->where('email',session('email'))
            ->first();
        
        if (isset($usr->usr_id) AND Hash::check($request->password, $usr->password)) {
            $login = true;
            
            $obj = DB::table('r_group_relate')
                ->where("usr_id", $usr->usr_id)
                ->get();
            $group_owner = 0;
            $group_id = 0;
            $owner_group_id = 0;
            foreach ($obj as $d) {
                if ($d->owner_flg == 1) {
                    $group_owner = 1;
                    $owner_group_id = $d->group_id;
                }
                $group_id = $d->group_id;
            }
            if ($owner_group_id > 0) {
                $group_id = $owner_group_id;
            }
            $obj = DB::table('r_rule')
                ->where("approver1", $usr->usr_id)
                ->orWhere("approver2", $usr->usr_id)
                ->get();
            $approver = 0;
            foreach ($obj as $d) {
                $approver = 1;
            }
            $request->session()->put('usr_id', $usr->usr_id);
            $request->session()->put('group_id', $group_id);
            if ($group_owner > 0) {
                $request->session()->put('group_owner', $group_owner);
            }
            if ($approver > 0) {
                $request->session()->put('approver', $approver);
            }
            $redirect = $request->cookie('redirect') ?: '/';
        } else {
            $login = false;
            $redirect = '/';
        }

        $res[0] = 1;
        $res[1] = $login;
        $res[2] = $redirect;
        return json_encode($res);
    }
}

