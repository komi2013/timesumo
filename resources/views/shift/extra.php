<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Extra</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<style>
    th {
       text-align: center; 
    }
    input[type=text]  {
        width: 40px;
        height:30px;
    }
</style>
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

<div id="content" >
    <select style="height:40px;" v-model="target" @change="changeUsr">
        <option v-for="(d,k) in usrs" :value="k">{{ d }}</option>
    </select>
    <template v-for="(d,k) in extra">
    <div style="width:100%;text-align:right;"><span style="font-size:20px;background-color: silver;" v-on:click="del(k)">&nbsp;-&nbsp;</span></div>
    <table>
    <tr><th>手当の割合(%)</th>
        <td>
            <input type="text" v-value="d['extra_ratio']" v-model="d['extra_ratio']">
            <i style="color: red;font-size: 12px;" v-if="d['extra_ratio'] < 1 || isNaN(d['extra_ratio'])" ><br>数値１以上お願いします</i>
        </td>
    </tr>
    <tr>
        <th>残業時間　数/帯</th>
        <td><input type="checkbox" :value="d['over_flg']" v-model="d['over_flg']"></td>
    </tr>
    <tr v-if="d['over_flg'] == 0" >
        <th>残業時間帯</th>
        <td>
          <input type="text" :value="d['extra_start']" v-model="d['extra_start']" @change="time(k,'extra_start')">
          ~
          <input type="text" :value="d['extra_end']" v-model="d['extra_end']" @change="time(k,'extra_end')">
        </td>
    </tr>
    <tr v-if="d['over_flg'] == 0"><th>休日出勤</th><td>
        <input type="checkbox" v-value="d['dayoff_flg']" v-model="d['dayoff_flg']"></td></tr>
    <tr v-if="d['over_flg'] > 0">
        <th>勤務時間超単位</th>
        <td>
            <select style="height:30px;" v-model="d['over_flg']">
                <option v-for="(d,k) in over_flg" v-bind:value="k">{{d}}</option>
            </select>
        </td>
    </tr>
    <tr v-if="d['over_flg'] > 0"><th>〜時間以上</th><td>
        <input type="text" :value="d['hour_start']" v-model="d['hour_start']">
        <i style="color: red;font-size: 12px;" v-if="d['hour_start'] < 1 || isNaN(d['hour_start'])" ><br>数値１以上お願いします</i>
        </td></tr>
    </table>
    </template>
    <div style="width:100%;text-align:right;"><span style="font-size:20px;background-color:silver;" v-on:click="add">&nbsp;+&nbsp;</span></div>
    <div style="width:100%;text-align:center;">
        <input type="submit" value="更新" style="padding:10px;" v-on:click="update">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var app = new Vue({
  el: '#content',
  data: {
    extra: eval(<?=json_encode($extra)?>),
    over_flg: eval(<?=json_encode($over_flg)?>),
    addData: eval(<?=json_encode($add)?>),
    usrs: eval(<?=json_encode($usrs)?>),
    target: eval(<?=json_encode($target_usr)?>),
  },
  methods: {
    time: function (k,ini) {
        var t = this.extra[k][ini].replace(/[^0-9]/g,'');
        var hour = t.substr(0,2) * 1;
        hour = hour > 23 ? 23 : hour ;
        hour = hour < 10 ? '0'+hour : hour ;
        hour = hour < 1 ? '00' : hour ;
        var minute =  t.substr(2,2) * 1;
        minute = minute > 59 ? 59 : minute;
        minute = minute < 10 ? '0' + minute : minute;
        minute = minute < 1 ? '00' : minute;
        this.extra[k][ini] = hour + ':' + minute;
    },
    del: function (k) {
        this.$delete(this.extra,k);
    },
    add: function (e) {
        this.$set(this.extra,this.extra.length,this.addData);
    },
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,extra: this.extra
        }
        $.post('/Shift/ExtraEdit/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    changeUsr: function (e) {
        location.href = '/Shift/Extra/index/' + this.target + '/';
    },
  }
});


</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
