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

</style>
<body>

<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    <?=$month->format(__('calendar.month_f'))?>
  </td>
  <td style="text-align:center;width:25%;">
    <a href="/"><img src="/img/icon/home.png" class="icon"></a>
  </td>
  </tr>
</table>

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
  <tr><td style="text-align: center;" >
    <a href="/HairSalon/Ability/"> ability </a></td></tr>
</table>

<div id="content">
    <table style="border-collapse: collapse; width:100%;" class="shift">
        <tr><th v-bind:rowspan="row"><?=__('calendar.day')?></th><th>出社</th><th>退社</th><th>休憩</th><th>残業</th></tr>
        <tr>                     <th colspan="4">備考</th></tr>
        <tr v-if="geo">    <th>経度</th><th>緯度</th><th>private ip</th><th>public ip</th></tr>
        <template v-for="(d,k) in days">
        <tr>
            <td v-bind:rowspan="row">{{d['date']}} {{d['day']}}</td>
            <td>{{d['time_in']}}</td>
            <td>{{d['time_out']}}</td>
            <td>{{d['break']}}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size: 12px;">
                <a target="_blank" v-bind:href="'/Calendar/Schedule/edit/'+d['schedule_id']+'/'" >{{d['todo_'+d['schedule_id']]}}</a>
            </td>
        </tr>
        <tr v-if="geo">
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
    <div style="width:100%;text-align:center;">
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
var month = '<?=$month->format('Y-m')?>';
var content = new Vue({
  el: '#content',
  data: {
      days:eval(<?=json_encode($days)?>),
      geo : false,
//      rowspan: 2,
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,month: month
            ,target_usr:'<?=$target_usr?>'
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
