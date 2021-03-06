<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\Simple;

class EmailSendController extends Controller {

    public function lessuri(Request $request) {
        $auth = Str::random(8);
        $request->session()->put('email_auth', $auth);
        $request->session()->put('password', $request->password);
        $request->session()->put('email', $request->email);

        \App::setLocale($request->cookie('lang'));

        $simple = new Simple();
        $simple->from_email = 'noreply@'.$_SERVER['SERVER_NAME'];
        $simple->from_name = 'TimeBook';
        $simple->simple_subject = __('auth.verify');
        $simple->arr_variable = [
            "url" => "https://".$_SERVER['SERVER_NAME']."/Auth/EmailVerify/code/" . $auth,
            "password" => $request->password
        ];
        $simple->template = 'mail.auth_emailsend';
        $simple->arr_bcc = [];
        $simple->arr_to = [$request->email];
        $simple->simple_send();
        $res[0] = 1;

        return json_encode($res);
    }
}

