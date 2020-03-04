<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>book management</title>
    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">

    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>



    <style>
        .offwork{
            color:orange;
        }
        table {
            width:700px;
            border-collapse: collapse;
        }
        table td {
            border-width: 0px;
        }
        .min10 {
            width: 100px;
            height: 30px;
            border-right-style: solid;
            border-width: thin;
            line-height: 12px;
            margin-left: -2px;
        }
        .hour {
            border-bottom-style: dotted;
            border-bottom-color: silver;
        }
        .closeTime {
            border-bottom-style: solid;
        }
        .day {
            border-bottom-style: solid;
            border-right-style: solid;
            border-width: thin;
        }
        .unavailable {
            background: gray;
        }
    </style>

<div id="content" style="width:800px;">
<table>
    <?php  foreach ($days21 as $date => $d) {?>
        <?php $u = strtotime($date);?>
        <?php if(date('D',$u) == 'Sun' && date('H:i',$u) == $openTime){?> <tr> <?php }?>
            <?php if(date('H:i',$u ) == $openTime){?>  
            <td border="0" date="<?=$date?>"> 
            <div class="day"><?=date('m-d',$u)?></div>                
            <?php }?>
            <?php if($d['available']) {?> <a href="/HairSalon/Book/<?=$menu_id?>/<?=$u?>/"> <?php } ?>
            <div class="min10 
                <?php if(date('H:i',$u ) == $closeTime){
                    echo 'closeTime';
                }else if(date('i',$u ) == '50'){
                    echo 'hour';
                } ?>
                <?=$d['available'] ? '' : 'unavailable'?>" 
                >
                
            </div>
            <?php if($d['available']) {?> </a> <?php } ?>
            <?php if(date('H:i',$u ) == $closeTime){?> </td> <?php }?>
        <?php if(date('D',$u) == 'Sat' && date('H:i',$u ) == $closeTime){?> </tr> <?php }?>
    <?php } ?>
</table>
    <template v-for="(d,k) in detail">
        <div style="width:100%;margin-top:20px;">{{d['agenda']}}</div>
        <div style="width:100%;">　{{d['time_start']}} ~ {{d['time_end']}}</div>
        <div style="width:100%;">{{d['todo']}}</div>
        <div>{{d['file_paths']}}</div>
    </template>
</div>

<script>

var tag_color = ['','rgba(0,0,255,0.2)','rgba(0,128,0,0.2)','rgba(255,255,0,0.2)','rgba(255,0,0,0.2)','rgba(128,0,128,0.2)'];
//1=meeting, 2=off, 3=out, 4=task, 5=shift
var obj = {
    agenda:'新規作成'
    ,time_start:'00:00'
    ,time_end:'00:00'
    ,todo:'...'
    ,file_paths:''
}
var content = new Vue({
  el: '#content',
  data: {
      detail:[obj]
  },
  computed: {

  }
});
var today = '<?=$today?>';
console.log(today);
var month = '02';


function showDetail(d,date){
    console.log(d[date]);
    if(d[date]){
        content.detail = d[date];
    }

}
//$(function(){ ga('send', 'pageview'); });
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
