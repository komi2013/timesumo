<?php
namespace App\My;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Hash;

class BookerRegister
{
    public $usr_id;
    public $group_id;
    public function __construct($usr_name){
//        echo '<pre>';
        $now = date('Y-m-d H:i:s');
        $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
        if (Session('your_owner') OR (Session('group_id') AND Session('sample_usr')) ) {
            $group_id = DB::select("select nextval('m_group_group_id_seq')")[0]->nextval;
        } else {
            $group_id = 0;
        }
        
        DB::beginTransaction();
        if (Session('your_owner')) {  //invitation without group
            DB::table('m_group')->insert([
                "group_id" => $group_id
                ,"group_name" => $usr_name.'G'
                ,"updated_at" => $now
                ,"password" => \Str::random(8)
            ]);
            DB::table('r_group_relate')->insert([
                "group_id" => $group_id
                ,"usr_id" => Session('your_owner')
                ,"updated_at" => $now
                ,"owner_flg" => 1
            ]);
            DB::table('r_group_relate')->insert([
                "group_id" => $group_id
                ,"usr_id" => $usr_id
                ,"updated_at" => $now
                ,"owner_flg" => 0
            ]);
        } else if (Session('group_id') AND Session('sample_usr')) {  // invitation with group
            DB::table('m_group')->insert([
                "group_id" => $group_id
                ,"usr_id" => $usr_id
                ,"updated_at" => $now
                ,"owner_flg" => 0
            ]);
//        } else {  // without invitation
        }
        DB::table('t_usr')->insert([
            "usr_id" => $usr_id
            ,"oauth_type" => 3
            ,"updated_at" => $now
            ,"email" => session('email')
            ,"password" => Hash::make(session('password'))
            ,"usr_name" => $usr_name
        ]);
        DB::commit();

        $this->usr_id = $usr_id;
        $this->group_id = $group_id;
//        echo '</pre>'; 
//        die;
    }
}
