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
        .showUsr {
            position:fixed;
            background-color:silver;
            padding:30px;
            width:220px;
            height:220px;
        }
    </style>

<div id="content" style="width:800px;">
<table><tr>
    <?php foreach ($days7 as $time => $d) {?>
            <?php if( substr($time,8,5) == '09:00' ){?>  
            <td border="0"> 
            <div class="day"><?=__('hair_salon.day'.substr($time,6,1))?></div>                
            <?php }?>
            <div class="min10" date="<?=__('hair_salon.day'.substr($time,6,1))?> <?=substr($time,8,5)?>" usrs="<?=json_encode($d)?>">
                <?=count($d)?>
            </div>
            <?php if( substr($time,8,5) == '18:50'  ){ ?> </td> <?php }?>
    <?php } ?>
</tr></table>
<div class="showUsr" style="display:none;" id="usr_name"></div>
</div>

<script>

var usr_ids = eval(<?=$usr_ids?>);
let target = document.getElementById('content');
$('.min10').click(function(){
    var usrs = eval($(this).attr('usrs'));
    var info = $(this).attr('date')+'<br>';
    for (var i = 0; i < usrs.length; i++) {
        info = info + usr_ids[usrs[i]] + "<br>";   
    }
    $('#usr_name').html(info);
    target.addEventListener('click', function(e){
        $('#usr_name').css({
            'top':(e.clientY-180)+'px'
           ,'left':(e.clientX-160)+'px'
           ,'display':''
        });
    });
    function getPosition(e) {
      let offsetX = e.offsetX; // =>要素左上からのx座標
      let offsetY = e.offsetY; // =>要素左上からのy座標
      let pageX = e.pageX; // =>ウィンドウ左上からのx座標
      let pageY = e.pageY; // =>ウィンドウ左上からのy座標
      let clientX = e.clientX; // =>ページ左上からのx座標
      let clientY = e.clientY; // =>ページ左上からのy座標
    }
});
$('#usr_name').click(function(){
    $( "#usr_name" ).fadeOut( "slow", function() {
        $('#usr_name').css({'display':'none'});
    });
});
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
