<?php
namespace App\Http\Controllers\Salon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// only owner access
class ShopController extends Controller {

    public function edit(Request $request, $directory=null, $controller=null,$action=null) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $relate = DB::table('r_group_relate')
                ->where('usr_id',$usr_id)
                ->where('group_id',$group_id)
                ->where('owner_flg',1)
                ->first();
        if (!isset($relate->usr_id)) {
            $msg = 'no access right:line'.__LINE__.':'.$_SERVER['REQUEST_URI'] ?? "".' '. json_encode($_POST);
            \Config::set('logging.channels.daily.path',storage_path('logs/warning.log'));
            \Log::warning($msg);
            return view('errors.500', compact('msg'));
        }
        $group = DB::table('m_group')->where('group_id', $group_id)->first();
        $shop_name = $group->group_name;
        $obj = DB::table('t_facility')->where('group_id', $group_id)->get();
        foreach ($obj as $d) {
            $facilities[$d->facility_id] = $d;
        }
        $facilities = json_encode($facilities);
        $request->session()->flash('facilities',$facilities);
        return view('salon.shop', compact('facilities','shop_name'));
    }
}

