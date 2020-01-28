<?php
namespace App\Http\Controllers\Stamp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisplayController extends Controller {

    public function index(Request $request, $directory=null, $controller=null, 
            $action=null, $id_date=null) {
        
        
        $date = '';
        return view('stamp.display', compact('date'));
    }
}

