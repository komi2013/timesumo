<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller {

    public function index(Request $request, $directory, $controller,$action) {


        return view('shift.test');
    }
    public function mypage(Request $request, $directory, $controller,$action) {
        // die($_COOKIE['user_id']);
        $user_id = Storage::get( $_COOKIE['user_id'] );
        $obj = DB::table('t_usr')->where('usr_name', $user_id)->get();
        $other_name = '';
        foreach ($obj as $d) {
            $other_name = $d->usr_name_mb;
        }
        // return view('shift.mypage');
        return view('shift.mypage', compact('other_name'));
    }
    public function sessionSave(Request $request) {
        // Session::put('user_id', $request->test);
        var_dump($request->test);
        // die('hei');
        $rand = Str::random(40);
        Storage::put($rand, $request->test);
        setcookie("user_id", $rand, time()+3600, "/");


        return;
        
    }
}
