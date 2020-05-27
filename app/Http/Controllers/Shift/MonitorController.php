<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitorController extends Controller {

    public function index(Request $request, $directory, $controller,$action) {
        if (!session('usr_id')) {
            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
            return redirect('/Auth/EmailLogin/index/');
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $group = DB::table('m_group')
                ->where('group_id', $group_id)
                ->first();
        $stamp = DB::table('t_timestamp')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->orderBy('time_in','DESC')->first();
        $time_out =  $stamp->time_out ?? '';
        $is = isset($stamp->timestamp_id) ? true : false;
        $pause = false;
        if (isset($stamp->time_in)) {
            $start = new Carbon($stamp->time_in);
            $break_at = new Carbon($stamp->break_at);
            if ($start < $break_at) {
                $pause = true;
            }            
        }
        $date = new Carbon();
        $password = $group->password;
        return view('shift.monitor', compact('date','time_out','is','pause','password'));
    }
    public function lessuri(Request $request) {
        if (!session('usr_id')) {
            return json_encode([2,'no session usr_id']);
        }
        $usr_id = session('usr_id');
        $group_id = session('group_id');
        \App::setLocale($request->cookie('lang'));
        $canvas = preg_replace("/data:[^,]+,/i","",$request->base64);
        $canvas = base64_decode($canvas);
        $image = imagecreatefromstring($canvas);
        imagesavealpha($image, TRUE);
        imagepng($image ,storage_path('app/public/monitor/'.$usr_id.'.png'));
        $text = 'https://'.$_SERVER['SERVER_NAME'].
                '/storage/monitor/'.$usr_id.'.png'.
                '?ver='.date('YmdHis')." \r\n ";
        $u = DB::table('t_usr')->where('usr_id', $usr_id)->first();
        $text .= $u->usr_name.' '.$request->actionTxt;
        
        $info = array(
            'body' => array(
                'payload' => json_encode(array(
//                    'channel'    => $this->channel,
//                    'username'   => $this->username,
//                    'icon_emoji' => $this->icon_emoji,
                    'text'       => $text,
                )),
            ),
        );
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $request->channel);
        curl_setopt($curl,CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $info['body']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HEADER, true);
        $output = curl_exec($curl);
//        var_dump($output);
        
    }
}
