<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>login</title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content">

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
<div style="width:100%;text-align: center;">
    <select style="width:90%;height:30px;">
        <?php foreach ($shops as $k => $d) {?>
        <option value="<?=$k?>" <?= $k==$group_id ? 'selected' : ''?> ><?=$d?></option>
        <?php }?>
    </select>
</div>
<br>
<?php foreach($menu as $menu_id => $d) { ?>
<a href="/HairSalon/MenuEdit/edit/<?=$menu_id?>">
<div style="width:93%; padding: 1%;">
{{$d['menu_name']}}
</div></a>
    <?php foreach($d['necessary'] as $menu_necessary_id => $d2) { ?>
        <div style="width:90%;padding: 1%;">
            &nbsp;{{$d2['service']}}
        </div>
        <div style="width:90%;padding: 1%;">
            &nbsp;{{$d2['facility']}}
        </div>
        <div style="width:90%;padding: 1%;">
            &nbsp;<?=$d2['start_minute']?> - <?=$d2['end_minute']?>
        </div>
    <?php } ?>
<?php } ?>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

$('.submit').click(function(){
    console.log($(this).attr('group'));
    var g = $(this).attr('group');
    console.log($('#shop_name_'+g).val());
    if(!$('#shop_name_'+g).val() || $('#shop_name_'+g).val().length > 30){
         $('#shop_name_error_'+g).css({'display':''})
        return ;
    }
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,group_id : g
        ,shop_name : $('#shop_name_'+g).val()
        ,seat : $('#seat_'+g).val()
        ,shampoo_seat : $('#shampoo_seat_'+g).val()
        ,perm_dry : $('#perm_dry_'+g).val()
    }
    $.post('/HairSalon/ShopUpdate/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            alert(updateOk);
            location.href = '';
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
