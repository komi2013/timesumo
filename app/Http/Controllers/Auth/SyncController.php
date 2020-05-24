<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncController extends Controller {

    public function begin(Request $request, $directory,$controller,$action) {
        if(!session('usr_id')){
            $request->session()->put('redirect', $_SERVER["REQUEST_URI"]);
            return redirect('/Auth/EmailLogin/index/');
        }
        \App::setLocale(\Cookie::get('lang') ?: 'ja');
        $usr_id = session('usr_id');
        $random = \Str::random(8);
        DB::table('t_sync')->where("usr_id",$usr_id)->delete();
        DB::table('t_sync')
            ->where("usr_id",$usr_id)
            ->insert([
                "usr_id" => $usr_id
                ,"sync_token" => $random
                ,"updated_at" => now()
            ]);

        return redirect('https://timebook.quigen.info/Auth/Sync/second/'
                .$_SERVER['SERVER_NAME'].'/'.$usr_id.'/'.$random.'/');
    }
    public function second(Request $request, $directory,$controller,$action,
            $domain,$sumo_usr_id,$random) {
        if(!session('usr_id')){
            $request->session()->put('redirect', $_SERVER["REQUEST_URI"]);
            return redirect('/Auth/EmailLogin/index/');
        }
        \App::setLocale(\Cookie::get('lang') ?: 'ja');
        $usr_id = session('usr_id');
        $db = DB::table('c_db')->where('domain',$domain)->first();
        \Config::set('database.connections.dynamic.host',$db->host);
        \Config::set('database.connections.dynamic.database',$db->database);
        \Config::set('database.connections.dynamic.username',$db->username);
        \Config::set('database.connections.dynamic.password',$db->password);

        $sync = DB::connection('dynamic')->table('t_sync')->where('usr_id', $sumo_usr_id)->first();

        if(isset($sync->sync_token) AND $sync->sync_token == $random){
            $db_at = new Carbon( $sync->updated_at );
            $dt = new Carbon();
            if ( $db_at->diffInMinutes($dt) < 30 ) {
                $syncs = json_decode(session('syncs'),true);
                $syncs[] = [$db->db_id,$sumo_usr_id];
                
                $request->session()->put('syncs', json_encode($syncs));
                return redirect('/');
            }
        }
        return view('errors.404');
        
    }
}

