<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContactController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $month=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        \App::setLocale($request->cookie('lang'));
        
        $obj = DB::table('t_contact')->orderBy('updated_at')->get();
        $my = [];
        $others = [];
        foreach ($obj as $k => $d) {
            if ($d->usr_id == $usr_id) {
                $my[$k] = $d;
            } else {
                $others[$k] = $d;
            }
        }
        
        return view('user.contact', compact('my','others'));
        
    }
}

