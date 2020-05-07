<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Setting</title>
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
<?php $side = new \App\Data\Side(); ?>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
<?php foreach ($side->gets() as $d) {?>
  <tr><td <?=$d['thisPage']?> ><a href="<?=$d['url']?>" >&nbsp;<?=$d['name']?></a></td></tr>
<?php }?>
</table>

<div id="content">

<div id="ad" class="pc_disp_none" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
<input type="text" class="column1" :value="usr_name" v-model="usr_name" ><br>

<select style="margin:10px;width:60%;height:40px;" v-model="lang" v-on:change="changeLang">
    <option v-for="(d,k) in langs" v-bind:value="d">{{ d }}</option>
</select>
<div style="width:100%;" v-for="(d,k) in arr_group" v-if="group_id == d['group_id'] && owner">
    <input type="text" class="column1" :value="d['group_name']" v-model="d['group_name']" ><br>
    <input style="margin: 10px;padding:10px;" type="submit" value="<?=__('auth.update')?>" v-on:click="changeName">
</div>
<select style="height:30px;width:80%;" v-model="group_id" @change="groupChange(group_id)">
    <option disabled>所属グループ</option>
    <template v-for="(d,k) in arr_group">
    <option v-bind:value="d['group_id']">{{d['group_name']}}</option>
    </template>
</select>
<div style="width:80%;display:inline-block;"></div>
<div style="width:10%;display:inline-block;">外す</div>
<template v-for="(d,k) in group_usrs">
    <label v-bind:for="d[0]"><div style="margin:5px;">
        <div style="width:80%;display:inline-block;">{{d[1]}}</div>
        <div style="width:10%;display:inline-block;" v-if="owner">
            <input type="checkbox"　v-bind:value="d[0]" v-model="removeUsr" v-bind:id="d[0]">
        </div></div></label>
</template>
<template v-for="(d,k) in group_facility">
    <label v-bind:for="d[0]"><div style="margin:5px;">
        <div style="width:80%;display:inline-block;">{{d[1]}}</div>
        <div style="width:10%;display:inline-block;" v-if="owner">
            <input type="checkbox"　v-bind:value="d[0]" v-model="removeUsr" v-bind:id="d[0]">
        </div></div></label>
</template>
<div style="width:100%;text-align: center;">
    <input style="margin: 10px;padding:10px;" type="submit" value="外す" v-on:click="remove">
</div>
<div style="width:100%;text-align: center;">
    <input style="margin: 10px;padding:10px;" type="submit" value="<?=__('auth.signout')?>" v-on:click="signout">
</div>

<div style="width:100%;text-align: center;">&nbsp</div>
<?=\Cookie::get('lang')?>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    usr_name: '',
    lang: "<?=\Cookie::get('lang')?>",
    langs: ['ja','en'],
    group_usrs: [],
    group_facility: [],
    arr_group: [],
    group_id: <?=$group_id?>,
    removeUsr: [],
    owner: 0
  },
  methods: {
    signout: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
        }
        $.post('/Auth/Session/signout/',param,function(){},"json")
        .always(function(res){
            if(res[0]){
//                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    changeLang: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,lang : this.lang            
        }
        $.post('/Auth/Session/lang/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    groupChange: function (group_id) {
        $.get('/Calendar/GroupGet/get/'+group_id +'/',{},function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                app.group_usrs = res[1];
                app.group_facility = res[2];
                app.arr_group = res[4];
                app.group_id = res[5];
                app.owner = res[4][res[5]]['owner_flg'];
            }else{
                alert('system error');
            }
        });
    },
    remove: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,removeUsr : this.removeUsr           
        }
        $.post('/Auth/Group/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    changeName: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,arr_group : this.arr_group
            ,group_id : this.group_id
        }
        $.post('/Auth/GroupName/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    updateName: function () {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,arr_group : this.arr_group
            ,group_id : this.group_id
        }
        $.post('/Auth/GroupName/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
  }
});
setTimeout(function(){
  app.groupChange(app.group_id);
},1000);
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>