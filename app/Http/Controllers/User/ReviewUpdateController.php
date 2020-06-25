<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewUpdateController extends Controller {

    public function lessuri(Request $request,$directory,$controller,$action=null) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        
        $b = $request->book;
        $dt = new Carbon();
        $dt->subMonths();
        $obj = DB::table('t_book')
                ->where('time_start','>',$dt->format('Y-m-d H:i:s'))
                ->orderBy('time_start','ASC')->get();
        $arr = json_decode($obj,true);
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        foreach ($arr as $k => $d) {
            if ($d['usr_id'] == $usr_id AND $d['review_to_salon'] == 0) {
                DB::table('t_book')
                    ->where("book_id", $d['book_id'])
                    ->update([
                        "usr_comment" => $b[$d['book_id']]['usr_comment']  ?: ''
                        ,"review_to_salon" => $b[$d['book_id']]['review_to_salon']
                        ,"updated_at" => $now
                    ]);
            }
        }
        DB::commit();
        $res[0] = 1;
        return json_encode($res);

    }
}
