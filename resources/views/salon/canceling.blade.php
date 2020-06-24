<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Canceling</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>
<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    
  </td>
  <td style="text-align:center;width:25%;">
    <a href="/"><img src="/img/icon/home.png" class="icon"></a>
  </td>
  </tr>
</table>
<?php $side = new \App\My\Side(); ?>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
<?php foreach ($side->gets() as $d) {?>
  <tr><td <?=$d['thisPage']?> ><a href="<?=$d['url']?>" >&nbsp;<?=$d['name']?></a></td></tr>
<?php }?>
</table>

<div id="content">

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>

<table style="width:100%;">
    <tr><th>予定日時</th><td><?=substr($book->time_start, 11, 5)?> ~ <?=substr($book->time_end, 11, 5)?></td></tr>
    <tr><th>予約した日時</th><td><?=date(__('calendar.today').' H:i',strtotime($book->booked_at))?></td></tr>
    <tr><th>メニュー</th><td><?=$book->menu_name?></td></tr>
    <tr><th>ユーザー名</th><td><?=$book->usr_name?></td></tr>
</table>

<table style="width:100%;text-align: center;"><tr>
    <td style="height:50px;"><input type="radio" name="payment" id="pay" action="1" checked><label for="pay">キャンセル料金支払い</label></td>
    <td style="height:50px;"><input type="radio" name="payment" id="nopay" action="2" ><label for="nopay">キャンセル料金支払いなし</label></td>
</tr></table>

<table style="width:100%;text-align: center;"><tr>
    <td style="height:50px;"><input type="radio" name="review" id="r_1" review="1" checked><label for="r_1">悪い</label></td>
    <td style="height:50px;"><input type="radio" name="review" id="r_5" review="5" ><label for="r_5">良い</label></td>
</tr></table>

<div style="width:100%;text-align: center;">
    <textarea class="column1" id="comment"></textarea>
</div>

<div style="width:100%;text-align: center;">
    <input type="submit" value="予約削除" class="column1 cancel"><br>
</div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

$('.cancel').click(function(){

    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,action : $('[name="payment"]:checked').attr('action')
        ,book_id : <?=json_encode($book->book_id)?>
        ,review : $('[name="review"]:checked').attr('review')
        ,comment : $('#comment').val()
    }
    $.post('/Salon/CancelUpdate/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            location.href = '/Salon/Cancel/index/';
        }else{
            alert('system error');
        }
    });
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>
