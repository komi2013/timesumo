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
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>

    <style>
        table {
            border-collapse: collapse;
        }
        .day_th {
            border-style: solid solid solid none;
            border-width: 1px;
            border-color: silver;
            font-size: 10px;
            width : 7%;
        }
        .sunday_th {
            border-style: solid;
            border-width: 1px;
            border-color: silver;
            font-size: 10px;
            width : 7%;
        }
        .weekday {
            border-right-style: solid;
            border-bottom-style: solid;
            border-color: silver;
            border-width: 1px;
            line-height: 20px;
            height: 30px;
            font-size: 10px;
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
    </style>

<table id="head_menu" style="width: 100%;">
<tr>
  <td id="menu_td">
    <img src="/img/icon/menu.png" class="icon" id="menu_button">
  </td>
  <td style="text-align: center;">
    <?=$today->format(__('calendar.month'))?>
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

<div id="content">
<table style="width:100%;">
    <thead><tr>
        <th class="sunday_th"><?=__('hair_salon.day0')?></th>
    <?php $i = 1; while($i < 7){ ?>
        <th class="day_th"><?=__('hair_salon.day'.$i)?></th>
    <?php ++$i; } ?>
    </tr></thead>
    <?php foreach ($day35 as $date => $d) {?>
        <?php $u = strtotime($date);?>
        <?php if(date('D',$u) == 'Sun'){?>
        <tr>
        <?php }?>
        <td class="<?= date('D',$u) == 'Sun' ? 'sunday' : 'weekday'?>" <?=count($d)? 'style="width:14%;"' : '' ?> >
            <div style="text-align: center;"><a href="/Calendar/Schedule/edit/<?=date('Y-m-d',$u)?>/"><?=date('d',$u)?></a></div>
            <?php foreach($d as $k2 => $d2){?>
                <div><a href="/Calendar/Schedule/edit/<?=$k2?>/">
                    {{$d2}}
                </a></div>
            <?php } ?>
        </td>
        <?php if(date('D',$u) == 'Sat'){?>
        </tr>
        <?php }?>
    <?php } ?>
</table>

</div>

<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
