<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>booked</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>
<style>

</style>
<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    <?=date(__('salon.today'))?>
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
<table>
    <tr><th>予定日時</th><th>予約した時</th><th>店名</th><th>メニュー名</th><th>予約番号</th></tr>
    <?php  foreach ($booked as $d) {?>
    <tr>
        <td><?=substr($d->time_start,0,10)?><br><?=substr($d->time_start,11,5)?></td>
        <td><?=substr($d->updated_at,0,10)?><br><?=substr($d->updated_at,11,5)?></td>
        <td><?=$d->group_name?></td>
        <td><?=$d->menu_name?></td>
        <td><?=$d->book_id?></td>
    </tr>
    <?php } ?>
</table>
<br><br>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

//let target = document.getElementById('content');

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
