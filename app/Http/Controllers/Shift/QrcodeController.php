<?php
namespace App\Http\Controllers\Shift;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrcodeController extends Controller {

    public function index(Request $request, $directory=null, $controller=null,$action=null,
            $param=null) {
        $usr_id = $request->session()->get('usr_id');
        $usr_id = 2;
        $group_id = $request->session()->get('group_id');
        $group_id = 2;
        $relate = DB::table('r_group_relate')
                ->where('usr_id', $usr_id)
                ->where('group_id', $group_id)
                ->first();
        if (!$relate->owner_flg) {
            die('no access right');
        }
        $group = DB::table('m_group')->where('group_id', $group_id)->first();
        $qrcode = new QrCode();
        
        $password = substr(base_convert(md5(uniqid()), 16, 36), 0, 6);
        DB::table('m_group')
            ->where('group_id', $group_id)
            ->update([
                'password' => $password
                ,'updated_at' => now()
                ]);
        $url = 'https://'.$_SERVER['HTTP_HOST'].'/Shift/Stamp/index/'.$password.'/';
        $src = base64_encode($qrcode::format('png')->size(300)->generate($url));
        $qr = '<img src="data:image/png;base64, ' . $src . '">';
        $date = new Carbon();
        return view('shift.qrcode', compact('date','qr','url'));
    }
}

