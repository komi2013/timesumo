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
/*    th {
       text-align: center;
       border: solid 1px;
    }*/
    .time {
        width: 40px;
    }
    .shift th {
       border: solid 1px;
    }
    .offday {
        background-color: silver;
    }
    .bad_time {
        color : red;
    }
    .manual_flg {
        background-color: orange;
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

<div id="content">
    <table style="border-collapse: collapse; width:100%;" class="shift">
        <tr><th v-bind:rowspan="row"><?=__('calendar.day')?></th><th>出社</th><th>退社</th><th>休憩</th><th>残業</th></tr>
        <tr>                     <th colspan="4">備考</th></tr>
        <tr v-if="geo">    <th>経度</th><th>緯度</th><th>private ip</th><th>public ip</th></tr>
        <template v-for="(d,k) in days">
        <tr v-bind:class="{offday:d['offday']}">
            <td v-bind:rowspan="row">{{d['date']}} {{d['day']}}</td>
            <td v-bind:class="{bad_time:d['routine_start'] < d['time_in'],manual_flg:d['manual_flg']}">{{d['time_in']}}</td>
            <td v-bind:class="{bad_time:d['time_out'] < d['routine_end'],manual_flg:d['manual_flg']}">{{d['time_out']}}</td>
            <td v-bind:class="{manual_flg:d['manual_flg']}">{{d['break']}}</td>
            <td>{{d['overwork']}}</td>
        </tr>
        <tr v-bind:class="{offday:d['offday']}">
            <td colspan="4" style="font-size: 12px;">
                <template v-for="(d2,k2) in d['schedules']">
                <a target="_blank" v-bind:href="'/Calendar/Schedule/edit/'+k2+'/'" >{{d2}}</a>
                </template>
            </td>
        </tr>
        <tr v-if="geo" v-bind:class="{offday:d['offday']}">
            <td>{{d['longitude']}}</td>
            <td>{{d['latitude']}}</td>
            <td>{{d['private_ip']}}</td>
            <td>{{d['public_ip']}}</td>
        </tr>
        <tr>
            <td colspan="5" style="border-bottom: solid 1px; width:100%;"></td>
        </tr>
        </template>
    </table>
    <template v-for="(d,k) in total_wage">
    <br>
    <div>基本給 : {{d['basic']}}</div>
    <table style="width:100%;">
        <tr>
            <td colspan="2"></td><td>残業時間</td><td>割合</td><td>残業代</td>
        </tr>
        <tr v-for="(d2,k2) in d['worked_wage']">
            <td colspan="2">{{d2['title']}}</td><td>{{d2['time']}}</td><td>{{d2['ratio']}}</td><td>{{d2['money']}}</td>
        </tr>
        <tr><th></th><th colspan="3">残業合計</th><th>{{d['ot_wage']}}</th></tr>
    </table>
    <div>合計 : {{d['wage']}}</div>
    </template>
    <div style="width:100%;text-align:center;" v-if="approveButton">
        <input type="submit" value="承認" style="padding:10px;" v-on:click="update">
    </div>
    <br>
    <div style="width:100%;text-align:center;">
        <input type="submit" value="地理情報" style="padding:10px;" v-on:click="swGeo">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var content = new Vue({
  el: '#content',
  data: {
      geo : false,
      days:eval(<?=$days?>),
      total_wage : eval(<?=$total_wage?>),
      approveButton : eval(<?=$approveButton?>),
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
        }
        $.post('/Shift/SheetApprove/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    swGeo: function (e) {
        if(this.geo){
            this.geo = false;
        }else{
            this.geo = true;
        }
    }
  },
  computed: {
    row() {
        return this.geo ? 3 : 2;
    },
  }
});

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
