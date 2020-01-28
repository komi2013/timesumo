<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>１２時間</title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-3"></script>
    <script src="/js/analytics.js<?=config('my.cache_v')?>"></script>
    <meta name="viewport" content="width=device-width, user-scalable=no" >
  </head>
<body>

<style>

</style>
<div id="content">
<template v-for="n in u_num">
    <template v-for="d in 12">
        <div class='time' v-bind:style="'position:absolute;display:inline-block;width:30px;'+
             'height:630px;margin-left:'+((d*30)-30)+'px;border-left: solid 1px rgba(0,128,0,0.2);'+
             'margin-top:'+(n-1)*630+'px;'">
            {{d+7}}</div>
    </template>
</template>

    <template v-for="(d,k) in arr_usr">
        <span style='display:none;'>{{im = Math.floor(k/20)*30 + 30}}</span>
    <div v-bind:style="'height:30px;width:360px;position:absolute;margin-top:'+((k*30)+im)+'px;'">
            {{d[1]}}</div>
        <template v-for="(d2,k2) in s[d[0]]">
        <div v-bind:style="'height:30px;margin-left:'+d2['left']+'px;margin-top:'+((k*30)+im)+'px;'+
             'width:'+d2['width']+'px;background-color:'+tag_color[d2['tag']]+'; position:absolute;'">
            &nbsp;</div>
        </template>
        
    </template>
</div>

<script>
var arr_usr = JSON.parse('<?=json_encode($arr_usr)?>');
var s = JSON.parse('<?=json_encode($s)?>');
var u_num = JSON.parse('<?=$u_num?>');
var tag_color = ['','rgba(0,0,255,0.2)','rgba(0,128,0,0.2)','rgba(255,255,0,0.2)','rgba(255,0,0,0.2)','rgba(128,0,128,0.2)'];
//1=meeting, 2=off, 3=out, 4=task, 5=shift
var content = new Vue({
  el: '#content',
  data: {
      arr_usr:arr_usr
      ,s:s
  },
  computed: {
  }
});

$('#checkArr').click(function(){
    var arr = [];
    for (var i = 0; i < content.join_usrs.length; i++) {
        arr.push(content.join_usrs[i]);
    }
    for (var i = 0; i < content.join_facility.length; i++) {
        arr.push(content.join_facility[i]);
    }
    console.log(encodeURIComponent(arr));
    console.log(JSON.stringify(arr));
});
//$(function(){ ga('send', 'pageview'); });
</script>
</body>
</html>
