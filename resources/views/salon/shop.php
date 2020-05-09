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


<div style="width:100%;text-align: center;">
    <input type="text" placeholder="<?=__('salon.shop_name')?>" class="column1" :value="shop_name" v-model="shop_name" ><br>
    <div style="color:red;" v-if="shopNameError"><?=__('salon.shopNameError')?></div>
</div>

<table style="width:100%;"><tbody>
    <tr style="height:50px;" v-for="(d,k) in facilities" >
        <td style="width:50%;text-align: center;">{{d['facility_name']}}</td>
        <td style="width:49%;">
            <input type="number" :value="d['amount']" v-model="d['amount']">
        </td>
    </tr>
</tbody></table>
<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('salon.update')?>" class="column1" @click="update" ><br>
</div>
<br>
<div style="border-top: solid 1px gray; width:100%;">&nbsp;</div>


</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    facilities: eval(<?=$facilities?>),
    shop_name: <?= json_encode($shop_name)?>,
    shopNameError:false
  },
  methods: {
    update: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,facilities : this.facilities
            ,shop_name : this.shop_name
        }
        $.post('/Salon/ShopUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0]){
                location.href = '/Salon/MenuEdit/edit/'+res[1];
            }else{
                alert('system error');
            }
        });
    },
  },
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>
