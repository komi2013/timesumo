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
    .note {
        width: 150px;
    }
</style>
<body>

<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    <?=$month->format(__('calendar.month'))?>
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

<div id="content" >
    <table>
        <tr><th style="width:60px;"></th><th>出社</th><th>退社</th><th>備考</th></tr>
        <template v-for="(d,k) in days">
        <tr>
            <td>{{d['date']}} {{d['day']}}</td>
            <td><input type="text" v-value="d['time_in']" v-model="d['time_in']" placeholder="00:00" class="time"></td>
            <td><input type="text" v-value="d['time_out']" v-model="d['time_out']" placeholder="00:00" class="time"></td>
            <td><input type="text" v-value="d['note']" v-model="d['note']" class="note"></td>
        </tr>
        </template>
    </table>
    <div style="width:100%;text-align:center;">
        <input type="submit" value="更新" style="padding:10px;" v-on:click="update">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
var month = '<?=$month->format('Y-m')?>';
var content = new Vue({
  el: '#content',
  data: {
      days:eval(<?=json_encode($days)?>)
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,days: this.days
            ,month: month
        }
        $.post('/Shift/TimeSheetEdit/',param,function(){},"json")
        .always(function(res){
            if(res[0]){
                location.href = '/Shift/TimeSheet/index/'+month+'/';
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
