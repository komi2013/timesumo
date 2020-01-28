<?php
namespace App\Http\Controllers;
require_once('/var/www/zstg_salon/vendor/simple_html_dom.php');

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Weidner\Goutte\GoutteFacade;

class HtmController extends Controller {

    public function index(Request $request, $page) {
        return view('htm.'.$page);
    }

}

