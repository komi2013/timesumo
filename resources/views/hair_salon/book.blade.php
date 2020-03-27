<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>book</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>
    <style>
        table {
            border-collapse: collapse;
        }
        .day_td {
            width : 14%;
        }
        .sunday_td {
            width : 14%;
        }
        .min10 {
            width: 100%;
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
<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    <?=date(__('hair_salon.today'))?>
  </td>
  <td style="text-align:center;width:25%;">
    <a href="/"><img src="/img/icon/home.png" class="icon"></a>
  </td>
  </tr>
</table>

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>
<div id="content">
<p id="menu_name"><?=$menu->menu_name?></p>
<?php if($staff){ ?><input type="text" value="<?=$customer?>" placeholder="<?=__('hair_salon.customer')?>" id="customer"><?php } ?>
<table>
    <?php  foreach ($days21 as $date => $d) {?>
        <?php $u = strtotime($date);?>
        <?php if(date('D',$u) == 'Sun' && date('H:i',$u) == $openTime){?> <tr> <?php }?>
            <?php if(date('H:i',$u ) == $openTime){?>  
            <td border="0" class="day_td"> 
            <div class="day"><?=date(__('hair_salon.date'),$u)?> <?=__('hair_salon.day'.date('w',$u))?></div>                
            <?php }?>
            <div date="<?=date('m/d',$u)?> <?=__('hair_salon.day'.date('w',$u))?>"
                 unix="<?=$u?>"
                 start="<?=date('H:i',$u)?>" end="<?=date('H:i',($u + 60 * $end_minute))?>"
                class="min10
                <?php if(date('H:i',$u ) == $closeTime){
                    echo 'closeTime';
                }else if(date('i',$u ) == '50'){
                    echo 'hour';
                } ?>
                <?=$d['available'] ? 'available' : 'unavailable'?>
                ">
                <?php if ( date('i',$u ) == '00' && $d['available'] ) {?>
                    <?=date('H:i',$u)?>
                <?php } ?>
            </div>
            <?php if(date('H:i',$u ) == $closeTime){?> </td> <?php }?>
        <?php if(date('D',$u) == 'Sat' && date('H:i',$u ) == $closeTime){?> </tr> <?php }?>
    <?php } ?>
</table>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

//let target = document.getElementById('content');
$('.available').click(function(){
    var check = $('#menu_name').html()+"\r\n";
    check = check + $(this).attr('date')+"\r\n";
    check = check + $(this).attr('start');
    check = check + " ~ " +$(this).attr('end')+"\r\n";
    check = check + $('#customer').val();
    var r = confirm(check);
    if (r == true) {
        console.log(check);
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,unix : $(this).attr('unix')
            ,menu_id : <?=$menu_id?>
            ,customer : $('#customer').val()
            ,staff : <?=$staff?>
            
        }
        $.post('/HairSalon/BookUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    }

});
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
