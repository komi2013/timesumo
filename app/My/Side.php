<?php
namespace App\My;
use Illuminate\Support\Facades\DB;

class Side
{
    public function gets(){
        if (session('usr_id')) {
            $obj = DB::table('c_link')
                    ->where("group_owner",'<=', session('group_owner') ?: 0)
                    ->where("approver",'<=', session('approver') ?: 0)
                    ->orderBy('priority','ASC')
                    ->get();
        } else {
            $obj = DB::table('c_link')
                    ->where("public", 1)
                    ->orderBy('priority','ASC')
                    ->get();
        }

        $arr_uri = explode("/", $_SERVER["REQUEST_URI"]);
        $link = [];
        $lang = \Cookie::get('lang') ?: 'ja';
        foreach ($obj as $d) {
            $arr['url'] = $d->url;
            $arr['name'] = $d->$lang;
            $db_uri = explode("/", $d->url);
            if ($arr_uri[1] == $db_uri[1] AND $arr_uri[2] == $db_uri[2]) {
                $arr['thisPage'] = 'style="background-color: #FCFCFC;"';
            } else {
                $arr['thisPage'] = '';
            }
            if ($d->url != '/Auth/EmailLogin/index/' OR !session('usr_id')) {
                $link[] = $arr;
            }
        }
        $arr['url'] = '';
        $arr['name'] = '';
        $arr['thisPage'] = '';
        $link[] = $arr;
        return $link;
    }
}
