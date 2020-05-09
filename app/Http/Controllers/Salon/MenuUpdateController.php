<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class MenuUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        if ($request->menu_id > 0) {
            // nessary security menu_id and menu_necessary_id should be session
            DB::beginTransaction();
            DB::table('m_menu')
                ->where("menu_id",$request->menu_id)
                ->update([
                    "menu_name" => $request->menu_name
                ]);
            DB::table('m_menu_necessary')
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
            $menu_id = DB::select("select nextval('m_menu_menu_id_seq')")[0]->nextval;
            DB::beginTransaction();
            DB::table('m_menu')->insert([
                "menu_id" => $menu_id
                ,"menu_name" => $request->menu_name
                ,"group_id" => $group_id
            ]);
        }
        foreach ($request->necessary as $d) {
            DB::table('m_menu_necessary')->insert([
                "menu_id" => $menu_id
                ,"service_id" => $d['service_id']
                ,"facility_id" => $d['facility_id']
                ,"start_minute" => $d['start_minute']
                ,"end_minute" => $d['end_minute']
            ]);
        }
        DB::commit();
        $res[0] = 1;
        $res[1] = $menu_id;
        return json_encode($res);
    }
}

