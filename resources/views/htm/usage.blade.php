<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Usage</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" >
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
<p>予約機能が無料で使えます。</p>
<p>&nbsp;</p>
<p>ヘアサロンの予約システムが希望の方は</p>
<p><a href="/Auth/EmailLogin/owner/1/">この登録URL</a></p>
<p>から登録してもらえれば既にヘアサロン用の設定はされています。</p>
<p>&nbsp;</p>
<ul>
<li>使い方</li>
</ul>
<p><a href="https://timesumo.quigen.info/Salon/Ability/edit/">「自分の可能サービス」</a></p>
<p>と</p>
<p><a href="/Salon/Shift/regular/">「シフト編集」</a></p>
<p>を編集して、</p>
<p><a href="https://timesumo.quigen.info/Salon/Menu/index/">「メニュー」</a></p>
<p>から該当の予約URLを取得してご自分のホーページに貼り付ける事が可能です。</p>
<p>自動的に予約の可能時間、不可能を計算して、スタッフのカレンダーに予定が入ります。</p>
<p>休みやキャンセルなどもスタッフ、席の予定と連携しているので、キャンセルが入った時はスタッフ、席の予定が空く事になります。</p>
<p>デフォルトは０ですが円単位のデポジットを設定できます。設定したデポジットをお客様が保持していないと予約できません</p>
<p>キャンセルすればデポジットからひかれるので、キャンセル率を抑える事ができます。</p>
<p>&nbsp;</p>
<p>何か不明点があれば問い合わせページから問い合わせお願いします</p>
</div>
<br>
<div id="ad_right">
    <iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
