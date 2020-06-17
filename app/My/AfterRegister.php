<?php
namespace App\My;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Hash;

class AfterRegister
{
    public $usr_id;
    public function __construct($usr_name){
//        echo '<pre>';
        $now = date('Y-m-d H:i:s');

        if (!session('group_id')) {
            $area = DB::table('c_area')->where("area_id", session('area_id'))->first();
            $init_group_id = $area->group_id;
            $init_usr_id = $area->usr_id;
            $group_id = DB::select("select nextval('m_group_group_id_seq')")[0]->nextval;
            $obj = DB::table('m_group')->where("group_id", $init_group_id)->get();
            $group = json_decode($obj,true);
            foreach ($group as $k => $d) {
                $group[$k]['group_id'] = $group_id;
                $group[$k]['updated_at'] = $now;
                $group[$k]['password'] = \Str::random(8);
            }
            $obj = DB::table('t_facility')->where("group_id", $init_group_id)->get();
            $facility = json_decode($obj,true);
            foreach ($facility as $k => $d) {
                $facility[$k]['group_id'] = $group_id;
                $facility[$k]['updated_at'] = $now;
                unset($facility[$k]['facility_id']);
            }
            $obj = DB::table('m_menu')->where("group_id", $init_group_id)->get();
            $menu = json_decode($obj,true);
            $arr_menu = [];
            foreach ($menu as $k => $d) {
                $menu[$k]['group_id'] = $group_id;
                $menu[$k]['updated_at'] = $now;
                $menu_id = DB::select("select nextval('m_menu_menu_id_seq')")[0]->nextval;
                $menu[$k]['menu_id'] = $menu_id;
                $arr_menu[$d['menu_id']] = $menu_id;
                $arr_menu_id[] = $d['menu_id'];
            }
            if ( count($arr_menu) ) {
                $obj = DB::table('m_menu_necessary')->whereIn("menu_id", $arr_menu_id)->get();
                $necessary = json_decode($obj,true);
                foreach ($necessary as $k => $d) {
                    $necessary[$k]['updated_at'] = $now;
                    $necessary[$k]['menu_id'] = $arr_menu[$d['menu_id']];
                    unset($necessary[$k]['menu_necessary_id']);
                }
            }
            $obj = DB::table('m_leave')->where("group_id", $init_group_id)->get();
            $leave = json_decode($obj,true);
            $arr_leave = [];
            foreach ($leave as $k => $d) {
                $leave[$k]['group_id'] = $group_id;
                $leave[$k]['updated_at'] = $now;
                $leave_id = DB::select("select nextval('m_leave_leave_id_seq')")[0]->nextval;
                $leave[$k]['leave_id'] = $leave_id;
                $arr_leave[$d['leave_id']] = $leave_id;
                $arr_leave_id[] = $d['leave_id'];
            }
            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            $obj = DB::table('t_leave_amount')->whereIn("leave_id", $arr_leave_id)->get();
            $leave_amount = json_decode($obj,true);
            foreach ($leave_amount as $k => $d) {
                $leave_amount[$k]['leave_id'] = $arr_leave[$d['leave_id']];
                $leave_amount[$k]['usr_id'] = $usr_id;
                $leave_amount[$k]['updated_at'] = $now;
            }
            DB::beginTransaction();
            DB::table('m_group')->insert($group);
            DB::table('t_facility')->insert($facility);
            if ( count($arr_menu) ) {
                DB::table('m_menu')->insert($menu);
                DB::table('m_menu_necessary')->insert($necessary);
            }
            DB::table('m_leave')->insert($leave);
            DB::table('t_leave_amount')->insert($leave_amount);
            DB::commit();
//            var_dump($facility,$leave,$leave_amount);
            $owner_flg = 1;
        } else {
            $init_group_id = session('group_id');
            $init_usr_id = session('sample_usr');
            $group_id = session('group_id');
            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;
            $obj = DB::table('t_leave_amount')->where("usr_id", $init_usr_id)->get();
            $leave_amount = json_decode($obj,true);
            foreach ($leave_amount as $k => $d) {
                $leave_amount[$k]['usr_id'] = $usr_id;
                $leave_amount[$k]['updated_at'] = $now;
            }
            DB::table('t_leave_amount')->insert($leave_amount);
            $owner_flg = 0;
        }
        $obj = DB::table('r_extra')
                ->where("group_id", $init_group_id)
                ->where("usr_id", $init_usr_id)
                ->get();
        $extra = json_decode($obj,true);
        foreach ($extra as $k => $d) {
            $extra[$k]['group_id'] = $group_id;
            $extra[$k]['usr_id'] = $usr_id;
            $extra[$k]['updated_at'] = $now;
        }
        $obj = DB::table('r_routine')
                ->where("group_id", $init_group_id)
                ->where("usr_id", $init_usr_id)
                ->limit(1)->get();
        $routine = json_decode($obj,true);
        foreach ($routine as $k => $d) {
            $routine[$k]['group_id'] = $group_id;
            $routine[$k]['usr_id'] = $usr_id;
            $routine[$k]['updated_at'] = $now;
            unset($routine[$k]['routine_id']);
        }
        $obj = DB::table('r_rule')
                ->where("group_id", $init_group_id)
                ->where("usr_id", $init_usr_id)
                ->limit(1)->get();
        $rule = json_decode($obj,true);
        foreach ($rule as $k => $d) {
            $rule[$k]['group_id'] = $group_id;
            $rule[$k]['usr_id'] = $usr_id;
            $rule[$k]['updated_at'] = $now;
            if (!session('group_id')) {
                $rule[$k]['approver1'] = $usr_id;
            }
        }
        $obj = DB::table('r_ability')->where("usr_id", $init_usr_id)->get();
        $ability = json_decode($obj,true);
        foreach ($ability as $k => $d) {
            $ability[$k]['usr_id'] = $usr_id;
            $ability[$k]['updated_at'] = $now;
        }
        $relate[0]['group_id'] = $group_id;
        $relate[0]['usr_id'] = $usr_id;
        $relate[0]['priority'] = 0;
        $relate[0]['updated_at'] = $now;
        $relate[0]['owner_flg'] = $owner_flg;

        $usr[0]['usr_id'] = $usr_id;
        $usr[0]['oauth_type'] = 3;
        $usr[0]['updated_at'] = $now;
        $usr[0]['email'] = session('email');
        $usr[0]['password'] = Hash::make(session('password'));
        $usr[0]['usr_name'] = $usr_name;
        
        DB::beginTransaction();
        DB::table('r_extra')->insert($extra);
        DB::table('r_routine')->insert($routine);
        DB::table('r_rule')->insert($rule);
        if ( count($ability) ) {
            DB::table('r_ability')->insert($ability);
        }
        DB::table('r_group_relate')->insert($relate);
        DB::table('t_usr')->insert($usr);
        DB::commit();
//        session()->forget('sample_usr');
//        session()->forget('area_id');
//        var_dump($extra,$rule,$ability);
        $this->usr_id = $usr_id;
//        echo '</pre>'; 
//        die;
    }
}
