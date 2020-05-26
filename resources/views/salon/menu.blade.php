<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Menu</title>
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
<div style="width:100%;text-align: center;">
    <select style="width:90%;height:30px;">
        <?php foreach ($shops as $k => $d) {?>
        <option value="<?=$k?>" <?= $k==$group_id ? 'selected' : ''?> ><?=$d?></option>
        <?php }?>
    </select>
</div>
<br>
<?php foreach($menu as $menu_id => $d) { ?>
    <br>
    <a href="/Salon/MenuEdit/edit/<?=$menu_id?>">
      <div style="width:93%;">
      <img src="/img/icon/pencil.png" style="max-height:20px;">{{$d['menu_name']}}
      </div>
    </a>
    <div style="word-break:break-all;padding:10px;">
    予約URL<br>
    <a href="https://timebook.quigen.info/User/Book/index/<?=$menu_id?>/<?=$db->db_id?>/">
        https://timebook.quigen.info/User/Book/index/<?=$menu_id?>/<?=$db->db_id?>/</a>
    </div>
    <div style="line-height: 10px;width:100%;border-bottom:1px solid silver; ">&nbsp;</div>
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
    $.post('/Salon/ShopUpdate/',param,function(){},"json")
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
