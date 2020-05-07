<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>space</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>

<style>
.axis {
    position: absolute;
    display: inline-block;
    width: 30px;
    height: 630px;
    /*margin-left: 30px;*/
    border-left: 1px solid rgba(0, 128, 0, 0.2);
    margin-top: 0px;
}

</style>
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
    <?php foreach($axis as $d){ ?>
        <div class="axis" style="margin-left:<?=$d[1]?>px;" ><?=$d[0]?></div>
    <?php } ?>
    <?php foreach($space as $usr_id => $d){ ?>
        <div style="position:absolute;z-index:2;margin-top:<?=$d['top']?>px;" ><?=$d['name']?></div>
        <?php foreach($d['schedules'] as $d2) {?>
        <div style="position:absolute;
             margin-left:<?=$d2['left']?>px;
             margin-top:<?=$d['top']?>px;
             width:<?=$d2['width']?>px;
             background-color:silver;" >&nbsp;</div>
    <?php }} ?>
</div>
<div id="ad_right" class="right_ad"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
