<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Shop</title>
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

<?php foreach($shop as $group_id => $d) { ?>
<div style="width:100%;text-align: center;">
    <input type="text" value="{{$d['shop_name']}}" placeholder="<?=__('salon.shop_name')?>" id="shop_name_<?=$group_id?>" class="column1"><br>
    <div id="shop_name_error_<?=$group_id?>" style="color: red;display: none;"><?=__('salon.shopNameError')?></div>
</div>

<table style="width:100%;"><tbody>
    <tr style="height:50px;">
        <td style="width:50%;text-align: center;" id="seat_name_<?=$group_id?>"><?=$d['seat']?></td>
        <td style="width:49%;">
            <select id="seat_<?=$group_id?>" style="width:90%;height:30px;">
                <?php $i = 1; while ($i < 21){?>
                <option value="<?=$i?>" <?= $i==$d['seat_amount'] ? 'selected' : ''?> ><?=$i?></option>
                <?php ++$i;}?>
            </select>
        </td>
    </tr>
    <tr style="height:50px;">
        <td style="width:50%;text-align: center;" id="shampoo_seat_name_<?=$group_id?>"><?=$d['shampoo_seat']?></td>
        <td style="width:49%;">
            <select id="shampoo_seat_<?=$group_id?>" style="width:90%;height:30px;">
                <?php $i = 0; while ($i < 5){?>
                <option value="<?=$i?>" <?= $i==$d['shampoo_seat_amount'] ? 'selected' : ''?> ><?=$i?></option>
                <?php ++$i;}?>
            </select>
        </td>
    </tr>
    <tr style="height:50px;">
    <td style="width:50%;text-align: center;" id="digital_perm_name_<?=$group_id?>"><?=$d['digital_perm']?></td>
        <td style="width:49%;">
            <select id="digital_perm_<?=$group_id?>" style="width:90%;height:30px;">
                <?php $i = 0; while ($i < 5){?>
                <option value="<?=$i?>" <?= $i==$d['digital_perm_amount'] ? 'selected' : ''?> ><?=$i?></option>
                <?php ++$i;}?>
            </select>
        </td>
    </tr>
</tbody></table>
<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('salon.update')?>" class="submit column1" group="<?=$group_id?>"><br>
</div>
<br>
<div style="border-top: solid 1px gray; width:100%;">&nbsp;</div>
<?php } ?>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
var updateOk = "<?=__('salon.update_ok')?>";
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
        ,seat_name : $('#seat_name_'+g).html()
        ,seat_amount : $('#seat_'+g).val()
        ,shampoo_seat_name : $('#shampoo_seat_name_'+g).html()
        ,shampoo_seat_amount : $('#shampoo_seat_'+g).val()
        ,digital_perm_name : $('#digital_perm_name_'+g).html()
        ,digital_perm_amount : $('#digital_perm_'+g).val()
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
