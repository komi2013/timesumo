<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Weidner\Goutte\GoutteFacade;

class FboauthController extends Controller {

    public function lessuri(Request $request, $page) {
        $client_id = '593374818166961';
        $client_secret = 'f38fea9ea17bf1e769748e90f1fc1231';
//    'fb_id' => '430706947760829',
//    'fb_secret' => 'd9f112f5c9af5535aea1f68dad3bd300',
        $fb_url = 'https://graph.facebook.com/oauth/access_token?';
        $redirect_uri = 'redirect_uri=https://'.$_SERVER['HTTP_HOST'].'/Auth/Fboauth/&';

        $contents = file_get_contents($fb_url.'client_id='.$client_id.'&'.$redirect_uri.'client_secret='.$client_secret.'&code='.$_GET['code']);
        $contents = json_decode($contents,true);

        $contents = file_get_contents('https://graph.facebook.com/me?access_token='.$contents['access_token']);
        $contents = json_decode($contents);
//        dd($contents);
//  +"name": "Seigi Komatsu"
//  +"id": "10216221617110572"

        $obj = DB::table('t_usr')
                ->where('oauth_type',2)
                ->where('oauth_id',$contents->id)
                ->first();
        if (isset($obj->usr_id)) {
            $usr_id = $obj->usr_id;
        } else {
            $usr_id = DB::select("select nextval('t_usr_usr_id_seq')")[0]->nextval;

            DB::table('t_usr')->insert([
                'usr_id' => $usr_id
                ,"oauth_type" => 2
                ,"oauth_id" => $contents->id
                ,"updated_at" => now()
                ,"usr_name" => $contents->name
            ]);
        }
        $request->session()->put('usr_id', $usr_id);

        $redirect = '/Calendar/Top/index/';
        
        if ($request->session()->put('after_signin') == 1) {
            $obj = DB::connection('salon')->table('m_skill')
                    ->where('usr_id',$usr_id)
                    ->first();
            if (isset($obj->usr_id)) {
//                $obj = DB::connection('salon')->table('m_skill')
//                    ->where('usr_id',$usr_id)
//                    ->first();
            } else {
                $redirect = '/Salon/Dresser/index/';
            }
            //check hairdresser usr id, 
            //check group id
        }
        
        
        return redirect($redirect);
    }

}

