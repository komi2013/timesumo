<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Mail\Simple;

class ContactUpdateController extends Controller {

    public function lessuri(Request $request,$directory,$controller,$action=null) {
//        if (!session('usr_id')) {
//            $request->session()->put('redirect',$_SERVER['REQUEST_URI']);
//            return redirect('/Auth/EmailLogin/index/');
//        }
        $usr_id = session('usr_id') ?: $request->usr_id;
        \App::setLocale($request->cookie('lang'));
                
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        if ($request->inquiry) {
            DB::table('t_contact')
                ->insert([
                    "contact_txt" => $request->inquiry
                    ,"email" => $request->email ?: ''
                    ,"usr_id" => $usr_id
                    ,"updated_at" => $now
                ]);
        } else {
            $obj = DB::table('t_contact')->orderBy('updated_at')->get();
            $contact = [];
            foreach ($obj as $d) {
                $contact[$d->contact_id]['contact_txt'] = $d->contact_txt;
            }
            foreach ($request->my as $d) {
                if ($contact[$d['contact_id']]['contact_txt'] != $d['contact_txt']
                   AND ( $d['usr_id'] == $usr_id OR session('admin')) ) {
                    if (session('admin')) {
                        $simple = new Simple();
                        $simple->from_email = 'noreply@'.$_SERVER['SERVER_NAME'];
                        $simple->from_name = 'TimeBook';
                        $simple->simple_subject = '返信：問い合わせ';
                        $simple->arr_variable = [
                            "url" => "https://".$_SERVER['SERVER_NAME']."/User/Contact/index/",
                            "contact_txt" => nl2br($d['contact_txt']) ?: ''
                        ];
                        $simple->template = 'mail.contact';
                        $simple->arr_bcc = [];
                        $simple->arr_to = [session('email')];
                        $simple->simple_send();
                    }
                    DB::table('t_contact')
                        ->where("contact_id", $d['contact_id'])
                        ->update([
                            "contact_txt" => $d['contact_txt'] ?: ''
                            ,"updated_at" => $now
                        ]);
                }
            }
        }

        DB::commit();
        $res[0] = 1;
        return json_encode($res);
    }
}

