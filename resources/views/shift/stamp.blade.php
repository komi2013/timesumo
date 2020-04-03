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
    shift
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

<div id="content" style="width:100%;text-align: center;">
    <?php if($time_out || !$is){?>
    <input type="button" value="START" onclick="stamp('add');">
    <?php }else{?>
    <input type="button" value="END" onclick="stamp('edit');">
    <?php } ?>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
function stamp(action){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,action: action
    }
    $.post('/Shift/Stamping/',param,function(){},"json")
    .always(function(res){
        if(res[0]){
            location.href = '/Shift/TimeSheet/index/';
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
