<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Monitor</title>
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
    <?=date(__('calendar.today').' H:i:s')?>
  </td>
  <td style="text-align:center;width:25%;">
    <a href="/"><img src="/img/icon/home.png" class="icon"></a>
  </td>
  </tr>
</table>
<?php $side = new \App\My\Side(); ?>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content" >
    <div v-if="!work_end">
    <template v-if="time_out || !is">
    <input type="button" value="開始" onclick="stamp('add');">
    </template><template v-else-if="pause">
    <input type="button" value="休憩終了" onclick="stamp('breakEnd');">
    </template><template v-else>
    <input type="button" value="終了" onclick="stamp('edit');">
    <br><br>
    <input type="button" value="休憩" onclick="stamp('breakStart');">
    </template>
    </div>
    <br><br>
    <input type="text" id="channel" style="width:80%;height:40px;margin:10px;"
        value="https://hooks.slack.com/services/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">
    <br>
    <a target="_blank" href="/Shift/TimeSheet/index/" style="margin:10px;display:block;">勤怠表</a>
    <br>
<video autoplay playsinline style="width:200px;height:200px;display:none;" id="video"></video>
<a id="hiddenLink"></a>
<canvas id="hiddenCanvas"></canvas>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
"user strict";

var app = new Vue({
  el: '#content',
  data: {
    time_out: <?=json_encode($time_out)?>,
    is: eval(<?=json_encode($is)?>),
    pause: eval(<?=json_encode($pause)?>),
    work_end:false,
  }
});
var latitude;
var longitude;
navigator.geolocation.getCurrentPosition(
  function(position) {
      latitude = position.coords.latitude;
      longitude = position.coords.longitude;
  }
);
const mediaStreamConstraints = {
  video: true
};
const localVideo = document.querySelector("video");
localVideo.width = 200;
localVideo.height = 200;
let localStream = null;
var actionTxt = 'reload';
function gotLocalMediaStream(mediaStream) {
    localStream = mediaStream;
    localVideo.srcObject = mediaStream;
    if(app.pause || app.time_out || !app.is){
        capturer.stop = true;
    }
    running();
}

function handleLocalMediaStreamError(error) {
  console.log("navigator.getUserMedia error: ", error);
}
navigator.mediaDevices
  .getDisplayMedia(mediaStreamConstraints)
  .then(gotLocalMediaStream)
  .catch(handleLocalMediaStreamError);

function stamp(action){
    if(action == 'add'){
        app.is = true;
        app.time_out = false;
        capturer.stop = false;
        actionTxt = 'start working';
    }else if (action == 'breakStart') {
        app.pause = true;
        capturer.stop = true;
        actionTxt = 'break start';
    }else if (action == 'breakEnd') {
        app.pause = false;
        capturer.stop = false;
        actionTxt = 'break end';
    }else if (action == 'edit') {
        capturer.stop = true;
        app.work_end = true;
        actionTxt = 'working end';
    }
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,action: action
        ,latitude : latitude
        ,longitude : longitude
        ,password : '<?=$password?>'
    }
    $.post('/Shift/Stamping/',param,function(){},"json")
    .always(function(res){});
    capturer.screenshot();
    actionTxt = 'working';
}

class Capturer{
    constructor() {
        this.stop = false;
        this.timeCounterInterval = null;
        this.timeScreenshotInterval = null;
    }
    screenshot(){
        const hiddenCanvas = document.getElementById( 'hiddenCanvas' );
        const ctx = hiddenCanvas.getContext('2d');
        ctx.drawImage( video, 0, 0, hiddenCanvas.width, hiddenCanvas.height );
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,base64: hiddenCanvas.toDataURL()
            ,channel:$('#channel').val()
            ,actionTxt : actionTxt
        }
        $.post('/Shift/Monitor/',param,function(){},"json")
        .always(function(res){});
    }
    getRandomSeconds(min, max) {
        let random = Math.floor(Math.random() * (max - min + 1)) + min;
        return random * 1000;
    }
}
let capturer = new Capturer();
function running() {
    var randomNumber = capturer.getRandomSeconds(60 * 5, 60 * 40);
//    var randomNumber = capturer.getRandomSeconds(2, 2);
    setTimeout(running, randomNumber);
    if(capturer.stop) return;
    console.log(`seconds elapsed = ${Math.floor(Date.now()/1000)}`);
    console.log("Next screenshot in: " + randomNumber / 1000 + 's');
    capturer.screenshot();
    
}

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
