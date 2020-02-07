<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\Simple;

class EmailSendController extends Controller {

    public function lessuri(Request $request) {
        $auth = Str::random(8);
        $request->session()->put('email_auth', $auth);
        $request->session()->put('password', $request->password);
        $request->session()->put('email', $request->email);
//        RCPT TO: seijiro.komatsu@shopairlines.com To: seijiro.komatsu@shopairlines.com
        \App::setLocale($request->cookie('lang'));
        $to = $request->email;
        Mail::to($to)->send('mail.auth_emailsend', [
            "url" => "https://".config('domain.my')."/Auth/EmailVerify/s/" . $auth,
            "password" => $request->password
        ], function($message) {
            $message->subject(__('email_verify.verify'));
        });



//        $to = $request->email;
//        $subject = __('email_verify.verify');
//        $message = "Please Verify Your Email\r\n"
//                ."https://".config('domain.my')."/Auth/EmailVerify/s/"
//                . $auth ."/ \r\n"
//                . "new password : ".$request->password." \r\n"
//                ;

//        $url = "https://".config('domain.my')."/Auth/EmailVerify/s/" . $auth;
//        $password = $request->password;

//        $message = view('mail.auth_emailsend', compact('url','password'));
//die($message);
        $headers = "From: noreply@".config('domain.my');
        $res[0] = 1;
//        $res[1] = mail($to, $subject, $message, $headers);

        echo json_encode($res);
    }
}

