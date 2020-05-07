<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Sign In</title>
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

<div id="ad" class="pc_disp_none" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>

<div style="margin-left:20px;">
  <a href="<?=$fb_url?>">
  <div>
    <img src="/img/icon/fb.jpg" class="icon">
    &nbsp;&nbsp;login / register
  </div>
  </a>
</div>
<br>
<div style="margin-left:20px;">
  <a href="<?=$gp_url?>">
  <div>
    <img src="/img/icon/gp.png" class="icon">
    &nbsp;&nbsp;login / register
  </div>
  </a>
</div>
<br>
<div style="margin-left:20px;">
  <a href="/Auth/EmailLogin/index/">
  <div>
    <img src="/img/icon/mail.png" class="icon">
    &nbsp;&nbsp;login / register
  </div>
  </a>
</div>
<div style="width:100%;">
    <input type="text" placeholder="email" id="email" class="column1"><br>
    <input type="password" placeholder="password" id="password" class="column1"><br>
    <input type="submit" value="login" id="login" class="column1"><br>
    <input type="submit" value="reissue passowrd" class="send_mail column1" >
</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
$('.send_mail').click(function(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,email : $('#email').val()
        ,password : $('#password').val()
    }
    $.post('/Auth/EmailSend/',param,function(){},"json")
    .always(function(res){
        console.log(res);
        if(res[0] == 1){
            alert('mail sent');
        }else{
            alert('failed');
        }
    });
});
$('#login').click(function(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,email : $('#email').val()
        ,password : $('#password').val()
    }
    $.post('/Auth/EmailLogin/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            location.href = '/Auth/Applicant/index/';
        }else{
            alert('failed');
        }
    });
});

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
