<?php
namespace App\Data;
use Illuminate\Support\Facades\DB;

class Side
{
    public function gets(){
        $obj = DB::table('c_link')
                ->where("group_owner",'<=', session('group_owner') ?: 0)
                ->where("approver",'<=', session('approver') ?: 0)
                ->get();
        $arr_uri = explode("/", $_SERVER["REQUEST_URI"]);
        $link = [];
        foreach ($obj as $d) {
            $arr['url'] = $d->url;
            $arr['name'] = $d->ja;
            $db_uri = explode("/", $d->url);
            if ($arr_uri[0] == $db_uri[0] AND $arr_uri[1] == $db_uri[1]) {
                $arr['thisPage'] = 'style="background-color: #FCFCFC;"';
            } else {
                $arr['thisPage'] = '';
            }
            $link[] = $arr;
        }
        return $link;
    }
}
