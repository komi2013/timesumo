<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Rule & Shift</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<style>
    #content th {
       text-align: center; 
    }
    .time {
        width: 40px;
    }
    .disable {
        background-color: silver;
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
    <table style="text-align: center; margin-left:10px;">
    <tr v-for="(d,k) in week" :class="{disable:routine[0]['disable_'+k]}" >
        <th style="width:100px;color:blue;" v-on:click="activate(k)" >{{d}}</th>
        <td><input type="text" class="time" v-if="!routine[0]['disable_'+k]" :value="routine[0]['start_'+k]" v-model="routine[0]['start_'+k]" @change="format('start_',k)"></td>
        <td> ~ </td>
        <td><input type="text" class="time" v-if="!routine[0]['disable_'+k]" :value="routine[0]['end_'+k]" v-model="routine[0]['end_'+k]" @change="format('end_',k)"></td>
    </tr>
    </table>
    <table>
        <tr><th>祝日休み</th><td><input type="checkbox" v-value="rule[0]['holiday_flg']" v-model="rule[0]['holiday_flg']"></td></tr>
        <tr><th>承認者①</th><td>
            <select style="height:30px;" v-model="rule[0]['approver1']">
                <option v-for="(d,k) in groups" v-bind:value="k">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>承認者②</th><td>
            <select style="height:30px;" v-model="rule[0]['approver2']">
                <option v-for="(d,k) in groups" v-bind:value="k">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>代休取得期限</th><td>
            <select style="height:30px;" v-model="rule[0]['compensatory_within']">
                <option v-for="d in compensatory" v-bind:value="d">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>時間給</th><td>
            <input type="number" style="height:30px;width:50px;" :value="rule[0]['wage']" v-model="rule[0]['wage']">
            </td></tr>
    </table>
    <div style="width:100%;text-align:center;">
        <input type="submit" value="更新" style="padding:10px;" v-on:click="update">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var content = new Vue({
  el: '#content',
  data: {
    routine: eval(<?=json_encode($routine)?>),
    rule: eval(<?=json_encode($rule)?>),
    week: eval(<?=json_encode($week)?>),
    groups: eval(<?=json_encode($groups)?>),
    time_unit: eval(<?=json_encode($time_unit)?>),
    compensatory:[30,60,90],
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,routine: this.routine
            ,rule: this.rule
        }
        $.post('/Shift/RoutineEdit/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    format: function (ini,k) {
        var t = this.routine[0][ini+k].replace(/[^0-9]/g,'');
        this.routine[0][ini+k] = t.substr(0,2) + ':' + t.substr(2,2);
    },
    activate: function (k) {
        if (this.routine[0]['disable_'+k]) {
            this.routine[0]['disable_'+k] = 0;
        }else{
            this.routine[0]['disable_'+k] = 1;
        }
        
    },
  }
});

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
