<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>cancel</title>
    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">

    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>



    <style>
        body {
            width:1180px;
        }
        #drawer {
          position : absolute;
          float : left;
          margin-top : -1px;
          width : 300px;   
          background-color: white;
        }
        #content{
            margin: 0px 0px 0px 310px;
            width: 700px;
            float:left;
        }
        #ad_right{
            margin: 0px 0px 0px 10px;
            width: 160px;
            float:left;
        }
        table {
            border-collapse: collapse;
        }
        table td {
            border-width: 0px;
            width : 100px;
            max-width: 200px;
            border-right-style: solid;
            border-bottom-style: solid;
            border-width: thin;
            line-height: 20px;
            margin-left: -2px;
            height: 30px;
        }
        .sunday {
            border-width: 0px;
            width : 100px;
            max-width: 200px;
            border-right-style: solid;
            border-bottom-style: solid;
            border-width: thin;
            line-height: 20px;
            margin-left: -2px;
            height: 30px;
            border-left-style: solid;
        }
    </style>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>
<div id="content">
<br>
<table>
    <?php foreach ($days21 as $date => $d) {?>
        <?php $u = strtotime($date.' 00:00:00');?>
        <?php if(date('D',$u) == 'Sun'){?>
        <tr>
        <?php }?>
        <td class="<?= date('D',$u) == 'Sun' ? 'sunday' : ''?> ">
            <div class="day"><?=date(__('hair_salon.date'),$u)?> <?=__('hair_salon.day'.date('w',$u))?></div> 
            <?php foreach($d as $k2 => $d2){?>
            <a href="/HairSalon/Canceling/index/<?=$k2?>/">
            <?=$d2?>
            </a><br>
            <?php } ?>
        </td>
        <?php if(date('D',$u) == 'Sat'){?>
        </tr>
        <?php }?>
    <?php } ?>
</table>

</div>

<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

//$(function(){ ga('send', 'pageview'); });
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
