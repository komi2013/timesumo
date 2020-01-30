<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>login</title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-3"></script>
    <script src="/js/analytics.js<?=config('my.cache_v')?>"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content">

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
<p>Oauth Login</p>
    <div style="margin:20px;display: inline-block;">
    <a href="<?=$fb_url?>">
        <img src="/img/icon/fb.jpg" class="icon">
    </a>
    </div>
<!--    <div style="margin:20px;display: inline-block;">
    <a href="/Manage/Twoauth/">
        <img src="/img/icon/tw.jpg" class="icon">
    </a>
    </div>-->
    <div style="margin:20px;display: inline-block;">
    <a href="<?=$gp_url?>">
        <img src="/img/icon/gp.png" class="icon">
    </a>
    </div>
<br>
<div style="margin:20px;display: inline-block;">
    <input type="text" placeholder="email" id="email" style="height:30px;"><br>
    <input type="text" placeholder="password" id="manager_pass" style="height:30px;"><br>
    <input type="submit" value="login" id="login" style="padding:10px;">
    <input type="submit" value="reissue passowrd" class="send_mail" style="padding:10px;">
</div>
<br>
<div style="margin:20px;display: inline-block;">
    <input type="submit" value="register" class="send_mail" style="padding:10px;">
</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
$('.send_mail').click(function(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,email : $('#email').val()
        ,manager_pass : $('#manager_pass').val()
    }
    $.post('/Manage/Email/',param,function(){},"json")
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
        ,manager_pass : $('#manager_pass').val()
    }
    $.post('/Manage/Email/login/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            location.href = '/Manage/Applicant/index/';
        }else{
            alert('failed');
        }
    });
});

</script>

<script>
    setTimeout(function(){ga('send', 'pageview')},2000);
</script>
</body>
</html>
