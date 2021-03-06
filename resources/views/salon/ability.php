<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Ability</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>
<style>

.tag {
  display: inline-block;
  background-color: #f1f1f1;
  margin: 5px;
  padding: 10px;
}
.ability {
    background-color: green;
}
</style>
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

<div id="ad" class="pc_disp_none" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>

<template v-for="(d,k) in service">
    <div class="tag" v-bind:class="service[k]['ability']" v-on:click="setAbility(k)" >{{d['service_name']}}</div>
</template>

<div style="width:100%;text-align: center;">
    <input style="margin: 10px;padding:10px;" type="submit" value="<?=__('salon.update')?>" v-on:click="update">
</div>

<div style="width:100%;text-align: center;">&nbsp</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    service: eval(<?=$service?>)
  },
  methods: {
    update: function (menu_id) {
        var ability = [];
        for (k in this.service) {
            if(this.service[k]['ability']){
                ability.push(k);
            }
        }
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,ability : ability
        }
        $.post('/Salon/AbilityUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    setAbility: function (k) {
        if(this.service[k]['ability']){
            this.$set(this.service[k],['ability'],'');
        }else{
            this.$set(this.service[k],['ability'],'ability');
        }
    },
  }
});

</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>