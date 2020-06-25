<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Review</title>
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

<table style="width:100%;" v-for="(d,k) in book">
    <tr><th>予定日時</th><td>{{d['time_start']}} ~ {{d['time_end']}}</td></tr>
    <tr><th>予約した日時</th><td>{{d['booked_at']}}</td></tr>
    <tr><th>メニュー</th><td>{{d['menu_name']}}</td></tr>
    <tr><th>お客様</th><td>{{d['usr_name']}}</td></tr>
    <tr>
        <td style="height:50px;"><input type="radio" :id="k+'_r_1'" value="1" v-model="d['review_to_usr']"><label :for="k+'_r_1'" >悪い</label></td>
        <td style="height:50px;"><input type="radio" :id="k+'_r_5'" value="5" v-model="d['review_to_usr']"><label :for="k+'_r_5'">良い</label></td>
    </tr>
    <tr><td colspan="2"><textarea class="column1" v-model="d['salon_comment']">{{d['salon_comment']}}</textarea></td></tr>
</table>

<div style="width:100%;text-align: center;">
    <input type="submit" value="入力" class="column1" v-on:click="update" ><br>
</div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var content = new Vue({
  el: '#content',
  data: {
    book: eval(<?=json_encode($book)?>),
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,book: this.book
        }
        $.post('/Salon/ReviewUpdate/',param,function(){},"json")
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
