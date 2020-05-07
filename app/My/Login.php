<?php
namespace App\My;
use Illuminate\Support\Facades\DB;
use Session;
class Login
{
    public $usr_id;
    public function after(){
        $obj = DB::table('r_group_relate')
            ->where("usr_id", $this->usr_id)
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
            ->where("approver1", $this->usr_id)
            ->orWhere("approver2", $this->usr_id)
            ->get();
        $approver = 0;
        foreach ($obj as $d) {
            $approver = 1;
        }
        Session::put('usr_id', $this->usr_id);
        Session::put('group_id', $group_id);
        if ($group_owner > 0) {
            Session::put('group_owner', $group_owner);
        }
        if ($approver > 0) {
            Session::put('approver', $approver);
        }
    }
}
