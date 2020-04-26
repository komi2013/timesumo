<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class HtmController extends Controller {

    public function index(Request $request, $page) {
        session()->reflash();
        return view('htm.'.$page);
    }

}

