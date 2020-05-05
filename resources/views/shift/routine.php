<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<style>
    th {
       text-align: center; 
    }
    .time {
        width: 40px;
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
<?php $side = new \App\Data\Side(); ?>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
<?php foreach ($side->gets() as $d) {?>
  <tr><td <?=$d['thisPage']?> ><a href="<?=$d['url']?>" >&nbsp;<?=$d['name']?></a></td></tr>
<?php }?>
</table>

<div id="content" >
    <table style="text-align: center; width:100%;">
    <tr v-for="(d,k) in week" v-bind:class="routine['shift_'+k]">
        <th>{{d}}</th>
        <td>
            <select style="height:30px;" v-model="routine['Hstart_'+k]">
                <option v-for="i in hours" v-bind:value="i">{{i}}</option>
            </select>
            <select style="height:30px;" v-model="routine['Mstart_'+k]">
                <option v-for="i in minutes" v-bind:value="i">{{i}}</option>
            </select>
        </td>
        <td> ~ </td>
        <td>
            <select style="height:30px;" v-model="routine['Hend_'+k]">
                <option v-for="i in hours" v-bind:value="i">{{i}}</option>
            </select>
            <select style="height:30px;"  v-model="routine['Mend_'+k]">
                <option v-for="i in minutes" v-bind:value="i">{{i}}</option>
            </select>
        </td>
    </tr>
    </table>
    <table>
        <tr><th>祝日休み</th><td><input type="checkbox" v-value="routine['holiday_flg']" v-model="routine['holiday_flg']"></td></tr>
        <tr><th>承認者①</th><td>
            <select style="height:30px;" v-model="routine['approver1']">
                <option v-for="(d,k) in groups" v-bind:value="k">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>承認者②</th><td>
            <select style="height:30px;" v-model="routine['approver2']">
                <option v-for="(d,k) in groups" v-bind:value="k">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>時間単位</th><td>
            <select style="height:30px;" v-model="routine['work_time_unit']">
                <option v-for="(d,k) in time_unit" v-bind:value="k">{{d}}</option>
            </select>
            </td></tr>
        <tr><th>代休</th><td>
            <select style="height:30px;" v-model="routine['compensatory_within']">
                <option v-for="d in compensatory" v-bind:value="d">{{d}}</option>
            </select>
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
    hours: eval(<?=json_encode($hours)?>),
    minutes: eval(<?=json_encode($minutes)?>),
    week: eval(<?=json_encode($week)?>),
    groups: eval(<?=json_encode($groups)?>),
    time_unit: eval(<?=json_encode($time_unit)?>),
    compensatory:[10,20,30,40,50,60,70,80,90],
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,routine: this.routine
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
  }
});

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
