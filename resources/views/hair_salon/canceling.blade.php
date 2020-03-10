<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>canceling</title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
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
<div style="width:100%;text-align: center;">
    <?=$schedule['title']?>
</div>
<div style="padding:10px;">
    <?php foreach ($todo as $k => $d) {?>
        <div style="padding:10px;"> {{$d['todo']}} </div>
    <?php }?>
</div>

<div style="padding:10px;">
    <?=__('hair_salon.customer')?>
    <?php foreach ($arr_customer as $d) {?>
        <div style="padding:10px;"> {{$d}} </div>
    <?php }?>
</div>

<?php foreach ($arr_staff as $d) {?>
    <div style="padding:10px;display:inline-block;"> {{$d}} </div>
<?php }?>


<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('hair_salon.delete')?>" class="column1 cancel"><br>
</div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

$('.cancel').click(function(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,schedule : eval(<?=json_encode($schedule)?>)
    }
    $.post('/HairSalon/CancelUpdate/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
//            location.href = '/HairSalon/Cancel/index/';
        }else{
            alert('system error');
        }
    });
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>
