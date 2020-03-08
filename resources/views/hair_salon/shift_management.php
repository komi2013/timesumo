<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>shift management</title>
    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>
    <style>
        body {
            width:1180px;
        }
        #drawer {
          position : absolute;
          float : left;
          margin-top : -1px;
          width : 300px;   
          background-color: white;
        }
        #content{
            margin: 0px 0px 0px 310px;
            width: 700px;
            float:left;
        }
        #ad_right{
            margin: 0px 0px 0px 10px;
            width: 160px;
            float:left;
        }
        table {
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
            text-align: center;
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
        .amt_1{
            background-color: #FFFF00;
        }
        .amt_2{
            background-color: #E5F200;
        }
        .amt_3{
            background-color: #CCE500;
        }
        .amt_4{
            background-color: #B2D800;
        }
        .amt_5{
            background-color: #99CC00;
        }
        .amt_6{
            background-color: #7FBF00;
        }
        .amt_7{
            background-color: #66B200;
        }
        .amt_8{
            background-color: #4CA600;
        }
        .amt_9{
            background-color: #339900;
        }
        .amt_10{
            background-color: #198C00;
        }
        .hour {
            border-bottom-style: dotted;
            border-bottom-color: silver;
        }
    </style>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content">
<table><tr>
    <?php $count=-1; foreach ($days7 as $time => $d) {?>
            <?php if( substr($time,8,5) == $openTime ){?>  
            <td border="0"> 
            <div class="day"><?=__('hair_salon.day'.substr($time,6,1))?></div>                
            <?php }?>
            <div class="min10 amt_<?=round(count($d)/$max,1)*10?> 
                 <?php if(substr($time,11,2) == '50'){
                     echo 'hour';
                 }?>
                 " date="<?=__('hair_salon.day'.substr($time,6,1))?> <?=substr($time,8,5)?>" usrs="<?=json_encode($d)?>">
                <?= count($d) != $count ? count($d) : '' ?>
            </div>
            <?php if( substr($time,8,5) == $closeTime  ){ ?> </td> <?php }?>
    <?php $count = count($d); } ?>
</tr></table>
<div class="showUsr" style="display:none;" id="usr_name"></div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

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
