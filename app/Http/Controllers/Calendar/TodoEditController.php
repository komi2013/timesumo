<?php
namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TodoEditController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null, $action=null) {


        $usr_id = 2;

        $paths = json_decode($request->input('file_paths'),true);
        $file_paths = [];
        foreach ($paths as $d) {
            if ($d[2]) {
                $file_paths[] = $d[0];
            } else {
                Storage::delete(substr($d[0], 4));
            }
        }
        $schedule_id = $request->schedule_id;
        if ( isset($_FILES['files']['tmp_name']) ) {
            foreach ($_FILES['files']['tmp_name'] as $k => $d) {
                $name = $_FILES['files']['name'][$k];
                $path = '/todo/'.date('Ymd').'/'.$schedule_id;
                $file_paths[] = 'file'.$path.'/'.$name;
                Storage::putFileAs($path, $d, $name);
            }            
        }
        $todo = DB::table('t_todo')->where("schedule_id", $schedule_id)->first();
        if (isset($todo->schedule_id)) {
            DB::connection('shift')->table('h_todo')->insert([
                    'todo' => $todo->todo
                    ,'schedule_id' => $schedule_id
                    ,'file_paths' => json_encode($file_paths)
                    ,'updated_at' => $todo->updated_at
                    ,"action_by" => $usr_id
                    ,"action_at" => date('Y-m-d H:i:s')
                    ,"action_flg" => 1
                ]);            
            DB::table('t_todo')
                ->where('schedule_id', $schedule_id)
                ->update([
                    'todo' => $request->todo ?: '',
                    'file_paths' => json_encode($file_paths),
                    'updated_at' => now()
                ]);
        } else {
            DB::table('t_todo')->insert([
                'todo' => $request->todo ?: '',
                'schedule_id' => $schedule_id,
                'file_paths' => json_encode($file_paths),
                'updated_at' => now()
            ]);
        }


//        if ($request->input('todo')) {
//            DB::table('t_todo')->insert([
//                'todo' => $request->input('todo'),
//                'schedule_id' => $schedule_id,
//                'file_paths' => $file_paths,
//                'updated_at' => now()
//            ]);
//        }
        $res[0] = 1;
        echo json_encode($res);
    }
}

