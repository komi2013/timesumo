<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {

    public function index(Request $request, $arg0,$arg1,$arg2='',$arg3='',$arg4='',$arg5='',
            $arg6='',$arg7='',$arg8='',$arg9='',$arg10='') {
        if (session('schedule_id') != $arg2) {
            die('not found');
        }
        $i = 0;
        $path = '';
        while ($i < 10) {
            $arg = 'arg'.$i;
            if ($$arg) {
                $path .= '/'.$$arg;
            } else {
                break;
            }
            ++$i;
        }
        $filePath = '/public'.$path;
        $fileName = substr($path,strrpos($path, "/")+1);
        $mimeType = Storage::mimeType($filePath);
        $headers = [['Content-Type' => $mimeType]];
        session()->reflash();
        return Storage::download($filePath, $fileName, $headers);
    }

}

