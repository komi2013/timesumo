<?php
namespace App\My;

class UserTag
{
    public $en = [
        1 => ['meeting',''],
        3 => ['out','rgba(0,128,0,0.2)'],
        4 => ['task','rgba(255,255,0,0.2)'],
        6 => ['service','rgba(128,0,128,0.2)'],
    ];
    public $ja = [
        1 => ['会議',''],
        3 => ['外出','rgba(0,128,0,0.2)'],
        4 => ['タスク','rgba(255,255,0,0.2)'],
        6 => ['サービス','rgba(128,0,128,0.2)'],
    ];
    //1=meeting, 2=off, 3=out, 4=task, 5=shift, 6=service
}
