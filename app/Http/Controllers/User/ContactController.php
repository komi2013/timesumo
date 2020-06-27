<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller {

    public function index(Request $request,$directory,$controller,$action,
            $fake_usr_id=0,$email='') {
//        if (!session('usr_id')) {
//            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
//            return redirect('/Auth/EmailLogin/index/');
//        }

        $usr_id = session('usr_id');
        if ($usr_id AND $fake_usr_id) {
            $usr_id = $fake_usr_id;
            $request->session()->flash('admin', 1);
            $request->session()->flash('email', $email);
        } else if(!session('usr_id')) {
            $usr_id = 1;
        }
        \App::setLocale($request->cookie('lang'));

        $obj = DB::table('t_contact')->orderBy('updated_at')->get();
        $my = [];
        $others = [];
        foreach ($obj as $k => $d) {
            if ($email AND $email == $d->email) {
                $my[$k] = $d;
            } else if ($d->usr_id == $usr_id AND $d->usr_id > 1) {
                $my[$k] = $d;
            } else if ($d->public > 0) {
                $others[$k] = $d;
            }
        }
        $msg = '';
        if ($request->msg == 1) {
            $msg = 'デポジットが足りないのでこのページから銀行振り込みの名前を入力してください'
                    .' ¥'. $request->deposit.' 必要です';
        }
        return view('user.contact', compact('my','others','usr_id','msg'));
        
    }
}

