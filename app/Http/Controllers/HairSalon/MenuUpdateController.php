<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class MenuUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 1;

//            ,menu_name : this.menu_name
//            ,necessary : this.necessary
//            ,menu_id : menu_id
        
        if ($request->menu_id > 0) {
            // nessary security menu_id and menu_necessary_id should be session
            DB::connection('salon')->beginTransaction();
            DB::connection('salon')->table('t_menu')
                ->where("menu_id",$request->menu_id)
                ->update([
                    "menu_name" => $request->menu_name
                ]);
            DB::connection('salon')->table('t_menu_necessary')
                ->where("menu_id",$request->menu_id)
                ->delete();
            $menu_id = $request->menu_id;
        } else {
            $group_id = $request->session()->get('group_id');
            $obj = DB::table('r_group_relate')->select('usr_id')
                ->where("group_id", $group_id)->get();
            foreach ($obj as $d) {
                $arr_usr_id[] = $d->usr_id;
            }
            $menu_id = DB::connection('salon')->select("select nextval('t_menu_menu_id_seq')")[0]->nextval;
            DB::connection('salon')->beginTransaction();
            DB::connection('salon')->table('t_menu')->insert([
                "menu_id" => $menu_id
                ,"menu_name" => $request->menu_name
                ,"group_id" => $group_id
            ]);
        }
        foreach ($request->necessary as $d) {
            DB::connection('salon')->table('t_menu_necessary')->insert([
                "menu_id" => $menu_id
                ,"service_id" => $d['service_id']
                ,"facility_id" => $d['facility_id']
                ,"start_minute" => $d['start_minute']
                ,"end_minute" => $d['end_minute']
            ]);
        }
        DB::connection('salon')->commit();
        $res[0] = 1;
        $res[1] = $menu_id;
        die( json_encode($res) );
    }
}

