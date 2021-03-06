<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>top</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>

<style>
    .day_th {
        border-style: solid solid solid none;
        border-width: 1px;
        border-color: silver;
        font-size: 10px;
        width: 100px;
    }
    .sunday_th {
        border-style: solid;
        border-width: 1px;
        border-color: silver;
        font-size: 10px;
        width: 100px;
    }
    .weekday {
        border-right-style: solid;
        border-bottom-style: solid;
        border-color: silver;
        border-width: 1px;
        line-height: 20px;
        height: 30px;
        font-size: 10px;
        background-color: white;
    }
    .sunday {
        border-right-style: solid;
        border-bottom-style: solid;
        border-color: silver;
        border-width: 1px;
        line-height: 20px;
        height: 30px;
        border-left-style: solid;
        font-size: 10px;
    }
    .color2 {
        background-color: rgba(0,0,255,0.2);
    }
    .color3 {
        background-color: rgba(0,128,0,0.2);
    }
    .color4 {
        background-color: rgba(255,255,0,0.2);
    }
    .color5 {
        background-color: rgba(255,0,0,0.2);
    }
    .color6 {
        background-color: rgba(128,0,128,0.2);
    }
</style>

<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
      <a href="/Calendar/Top/index/<?=$prev?>/" style="padding:10px;"> < </a>
        <?=$today?>
      <a href="/Calendar/Top/index/<?=$next?>/" style="padding:10px;"> > </a>
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
<table style="width:700px;z-index:3;position:relative;">
    <thead><tr>
        <th class="sunday_th"><?=__('salon.day0')?></th>
    <?php $i = 1; while($i < 7){ ?>
        <th class="day_th"><?=__('salon.day'.$i)?></th>
    <?php ++$i; } ?>
    </tr></thead>
    <?php foreach ($day35 as $date => $d) {?>
        <?php $u = strtotime($date);?>
        <?php if(date('D',$u) == 'Sun'){?>
        <tr>
        <?php }?>
        <td class="<?= date('D',$u) == 'Sun' ? 'sunday' : 'weekday'?>" <?=count($d)? 'style="width:14%;"' : '' ?> >
            <div style="text-align: center;"><a href="<?=$url.date('Y-m-d',$u)?>/"><?=date('d',$u)?></a></div>
            <?php foreach($d as $k2 => $d2){?>
                <div class="color<?=$d2[1]?>"><a href="<?=$d2[2].$k2?>/">
                    {{$d2[0]}}
                </a></div>
            <?php } ?>
        </td>
        <?php if(date('D',$u) == 'Sat'){?>
        </tr>
        <?php }?>
    <?php } ?>
</table>

</div>
<br>
<div id="ad_right">
    <iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
