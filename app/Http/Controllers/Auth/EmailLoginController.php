<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class EmailLoginController extends Controller {

    public function index(Request $request) {
        $lang = $request->cookie('lang') ?? 'en';
        \App::setLocale($lang);
        return view('auth.email_login');
    }
}

