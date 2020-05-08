<?php
namespace App\My;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Hash;

class AfterRegister
{
    public function __construct($usr_name){
        $group_id = session('group_id');
        $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
        $now = date('Y-m-d H:i:s');
        $obj = DB::table('r_extra')->where("group_id", $group_id)->limit(1)->get();
        $extra = json_decode($obj,true);
        foreach ($extra as $k => $d) {
            $extra[$k]['usr_id'] = $usr_id;
            $extra[$k]['updated_at'] = $now;
        }
        $obj = DB::table('r_routine')->where("group_id", $group_id)->limit(1)->get();
        $routine = json_decode($obj,true);
        foreach ($routine as $k => $d) {
            $routine[$k]['usr_id'] = $usr_id;
            $routine[$k]['updated_at'] = $now;
        }
        $obj = DB::table('r_rule')->where("group_id", $group_id)->limit(1)->get();
        $rule = json_decode($obj,true);
        foreach ($rule as $k => $d) {
            $rule[$k]['usr_id'] = $usr_id;
            $rule[$k]['updated_at'] = $now;
        }   
        $relate = DB::table('r_group_relate')->where("group_id", $group_id)->first();
        $obj = DB::table('r_ability')->where("usr_id", $relate->usr_id)->get();
        $ability = json_decode($obj,true);
        foreach ($ability as $k => $d) {
            $ability[$k]['usr_id'] = $usr_id;
            $ability[$k]['updated_at'] = $now;
        }
        $relate[0]['group_id'] = $group_id;
        $relate[0]['usr_id'] = $usr_id;
        $relate[0]['priority'] = 0;
        $relate[0]['updated_at'] = $now;
        $relate[0]['owner_flg'] = 0;
        
        DB::table('r_extra')->insert($extra);
        
        
        DB::table('t_usr')->insert([
            "usr_id" => $usr_id
            ,"oauth_type" => 3
            ,"updated_at" => now()
            ,"email" => session('email')
            ,"password" => Hash::make(session('password'))
            ,"usr_name" => $usr_name
        ]);

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
            ->where("approver1", $usr_id)
            ->orWhere("approver2", $usr_id)
            ->get();
        $approver = 0;
        foreach ($obj as $d) {
            $approver = 1;
        }
        Session::put('usr_id', $usr_id);
        Session::put('group_id', $group_id);
        if ($group_owner > 0) {
            Session::put('group_owner', $group_owner);
        }
        if ($approver > 0) {
            Session::put('approver', $approver);
        }
    }
}
