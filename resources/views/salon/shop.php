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
    <input type="text" class="column1" :value="shop_name" v-model="shop_name" ><br>
    <div style="color:red;" v-if="shopNameError"><?=__('salon.shopNameError')?></div>
    <table style="width:100%;"><tr><td>
    開始時間:<input type="text" style="width:40px;height:30px;" v-value="open_time" v-model="open_time" @change="time('open')" >
    </td><td>
    終了時間:<input type="text" style="width:40px;height:30px;" v-value="close_time" v-model="close_time" @change="time('close')" >
    </td></tr></table>
</div>

<table style="width:100%;text-align: center;"><tbody>
    <tr style="height:50px;" v-for="(d,k) in facilities" >
        <td style="text-align: center;">{{d['facility_name']}}</td>
        <td>
            <input style="height:40px;width:40px;" type="number" :value="d['amount']" v-model="d['amount']">
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
    shopNameError:false,
    open_time:<?= json_encode($open_time)?>,
    close_time:<?= json_encode($close_time)?>,
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
            if(res[0] == 1){
                location.href = '/Salon/Shop/edit/';
            }else{
                alert('system error');
            }
        });
    },
    time: function (str) {
        var t;
        if(str == 'open'){
            t = this.open_time.replace(/[^0-9]/g,'');
        } else {
            t = this.close_time.replace(/[^0-9]/g,'');
        }
        var hour = t.substr(0,2) * 1;
        hour = hour > 23 ? 23 : hour ;
        hour = hour < 10 ? '0'+hour : hour ;
        hour = hour < 1 ? '00' : hour ;
        var minute =  t.substr(2,2) * 1;
        minute = minute > 59 ? 59 : minute;
        minute = minute < 10 ? '0' + minute : minute;
        minute = minute < 1 ? '00' : minute;
        if(str == 'open'){
            this.open_time = hour + ':' + minute;
        } else {
            this.close_time = hour + ':' + minute;
        }
        
    },
  },
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>
