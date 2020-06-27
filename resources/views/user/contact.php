<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Contact</title>
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
<div style="margin:1%;padding:1%;width:90%;">
    {{msg}}
</div>
<my v-for="(d,k) in my">
  <textarea style="width:90%;height:100px;margin:1%;" v-model="d.contact_txt">{{d.contact_txt}}</textarea>
</my>
<input type="email" placeholder="sample@example.com" style="margin:5px;padding:5px;" v-if="usr_id == 1" v-model="email">
<textarea style="width:90%;height:100px;margin:1%;" placeholder="新規問い合わせ" v-model="inquiry"></textarea>

<div style="width:100%;text-align: center;">
    <input type="submit" value="入力" class="column1" v-on:click="update" ><br>
</div>
<div style="width:100%;margin:3px;" v-for="(d,k) in others">{{d.contact_txt}}</div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var content = new Vue({
  el: '#content',
  data: {
    my: <?=json_encode($my)?>,
    others: <?=json_encode($others)?>,
    inquiry: '',
    usr_id: <?=json_encode($usr_id)?>,
    email: '',
    msg: <?=json_encode($msg)?>,
  },
  methods: {
    update: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,my: this.my
            ,inquiry: this.inquiry
            ,usr_id: this.usr_id
            ,email: this.email
        }
        $.post('/User/ContactUpdate/',param,function(){},"json")
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
