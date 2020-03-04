<?php
namespace App\Http\Controllers\HairSalon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class RoutineUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,
            $action=null, $one='', $two='') {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;
        $group_id = 1;

        $r = $request->routine;
        if ($request->session()->get('routine_id') > 0) {
            $i = 0;
            while ($i < 7) {
//                $arr['routine_id'] = 0;
                if ($r[0]['shift_'.$i] === 'O') {
                    $arr['start_'.$i] = $r[0]['Hstart_'.$i].':'.$r[0]['Mstart_'.$i].':00';
                    $arr['end_'.$i] = $r[0]['Hend_'.$i].':'.$r[0]['Mend_'.$i].':00';
                    $arr['break_start_'.$i] = $r[0]['Hbreak_start_'.$i].':'.$r[0]['Mbreak_start_'.$i].':00';
                    $arr['break_end_'.$i] = $r[0]['Hbreak_end_'.$i].':'.$r[0]['Mbreak_end_'.$i].':00';   
                } else {
                    $arr['start_'.$i] = null;
                    $arr['end_'.$i] = null;
                    $arr['break_start_'.$i] = null;
                    $arr['break_end_'.$i] = null;
                }
//                $arr['Mstart_'.$i] = '00';
//                $arr['Mend_'.$i] = '00';
//                $arr['Mbreak_start_'.$i] = '00';
//                $arr['Mbreak_end_'.$i] = '00';
                ++$i;
            }
//echo '<pre>';
//var_dump($arr);
//echo '</pre>';
//die;
            DB::beginTransaction();
            DB::table('r_routine')
                ->where('usr_id',$usr_id)
                ->where('group_id',$group_id)
                ->update($arr);
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
        DB::commit();
        $res[0] = 1;
        die( json_encode($res) );
    }
}

