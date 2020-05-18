<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>QR code</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<style>
    input[type="button"] {
        width: 100px;
        height: 100px;
        padding: 10px;
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

<div id="content" style="text-align: center;">
    <?=$qr?>
    <br><br>
    <a target="_blank" href="<?=$url?>" style="word-break: break-all;"><?=$url?></a>
</div>

<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var latitude;
var longitude;
navigator.geolocation.getCurrentPosition(
  function(position) {
      console.log("緯度:"+position.coords.latitude+",経度"+position.coords.longitude);
      latitude = position.coords.latitude;
      longitude = position.coords.longitude;
  },
  function(error) {
    switch(error.code) {
      case 1: //PERMISSION_DENIED
        console.log("位置情報の利用が許可されていません");
        break;
      case 2: //POSITION_UNAVAILABLE
        console.log("現在位置が取得できませんでした");
        break;
      case 3: //TIMEOUT
        console.log("タイムアウトになりました");
        break;
      default:
        console.log("その他のエラー(エラーコード:"+error.code+")");
        break;
    }
  }
);

function stamp(action){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,action: action
        ,latitude : latitude
        ,longitude : longitude
    }
    $.post('/Shift/Stamping/',param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            location.href = '';
        }else{
            alert('system error');
        }
    });
}
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
