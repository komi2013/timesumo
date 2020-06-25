<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelUpdateController extends Controller {

    public function lessuri(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        
        $book = DB::connection('timebook')->table('t_book')
                ->where('book_id',$request->book_id)->first();
        if (!isset($book->group_id) OR $book->group_id != $group_id) {
            $msg = 'no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));
        }
        $now = date('Y-m-d H:i:s');
        DB::connection('timebook')->beginTransaction();
        DB::connection('timebook')->table('t_book')
            ->where("book_id",$request->book_id)
            ->update([
                "book_action" => $request->action
                ,"review_to_usr" => $request->review
                ,"salon_comment" => $request->comment
                ,"canceled_at" => $now
                ,"updated_at" => $now
            ]);
        DB::connection('timebook')->commit();

        $res[0] = 1;
        die( json_encode($res) );

    }
}

