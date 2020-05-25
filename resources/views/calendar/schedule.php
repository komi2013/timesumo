<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?=date('Y/m/d',strtotime($date))?></title>
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
    .joining {
        width: 80%;
        height: 120px;
        border: 1px solid #000000;
        display: inline-block;
        overflow: scroll;
        text-align: left;
    }
    .centerize {
        width: 100%;
        text-align: center;
    }
    input[type="radio"]:checked + label {
        background-color: silver;
    }
    input[type="radio"]{
        display:none;
    }
    input[type="radio"] + label {
        width: 100%;
        height: 30px;
        display: block;
        padding-top: 2px;
    }
    select {
       height: 30px;
    }
    .no_arrow {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        min-width: 30px;
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
<div id="ad" class="pc_disp_none"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
    <div class="centerize">
        <template v-if="access_right==7">
        <select style="width:80%;" v-model="tag" @change="copy">
            <option v-for="(d,k) in tags" v-bind:value="k"
                    v-bind:style="'background-color:' + d[1]">{{d[0]}}</option>
        </select>
        <template v-if="tag==2">
        <select style="width:80%;" v-model="leave_id" @change="copy">
            <option v-for="(d,k) in off_tags" v-bind:value="d['leave_id']">{{d['leave_name']}}</option>
        </select>
        </template><template v-else>
            <input type="text" placeholder="タイトル" style="height:50px;width:80%;" v-bind:value="title" v-model="title" @blur="copy" >
            <div style="color:red;" v-if="titleErr">1文字以上10文字以内でお願いします</div>
        </template>
        </template><template v-else>
            <div>{{tags[tag][0]}}</div>
            <div>{{title}}</div>
        </template>
    </div>
    <br>
    <div class="centerize">
        <?=date(__('calendar.date'),strtotime($date))?>
        <template v-if="displayTime">
        <select class="no_arrow" v-model="hourStart" :disabled="access_right < 7">
        <option v-for="d in hours" v-bind:value="d">{{d}}</option>
        </select>
        <select class="no_arrow" v-model="minuteStart" :disabled="access_right < 7">
        <option v-for="d in minutes" v-bind:value="d">{{d}}</option>
        </select>
        </template>
        <span>~</span>
        <select class="no_arrow" v-model="date_end" :disabled="access_right < 7">
            <option v-for="d in nextDay" v-bind:value="d[0]">{{d[1]}}</option>
        </select>
        <template v-if="displayTime">
        <select class="no_arrow" v-model="hourEnd" :disabled="access_right < 7">
        <option v-for="d in hours" v-bind:value="d">{{d}}</option>
        </select>
        <select class="no_arrow" v-model="minuteEnd" :disabled="access_right < 7">
        <option v-for="d in minutes" v-bind:value="d">{{d}}</option>
        </select>
        </template>
    </div>
    <br>
    <div class="centerize">
    <template v-if="schedule_id && tag == 2 && access_right == 7">
        <input type="button" value="削除" style="height:30px;width:80%;" onclick="del()">
    </template><template v-else-if="schedule_id && access_right == 7">
        <input type="button" value="更新" style="height:30px;width:40%;" onclick="update()">
        <input type="button" value="削除" style="height:30px;width:40%;" onclick="del()">
    </template><template v-else-if="schedule_id && access_right == 6">
        <input type="button" value="更新" style="height:30px;width:40%;" onclick="update()">
    </template><template v-else-if="!schedule_id">
        <input type="button" value="登録" style="height:30px;width:80%;" onclick="update()">
    </template>
    </div>
    <br>
    <div style="width:97%;padding-left:2%;">
    <div style="width:97%;text-align:right;">
      <img src="/img/icon/pencil.png" style="max-height:20px;" v-if="!todoEdit && access_right >= 6" @click="editTodo" >
      <img src="/img/icon/list.png" style="max-height:20px;" v-if="todoEdit" @click="editTodo" >
    </div>
    <div style="font-size: 12px;"
         v-if="!todoEdit" v-html="AutoLink(nl2br(todo))"></div>
    <textarea style="width:95%;height:120px;font-size:12px;position:relative;background-color: white;"
              placeholder="内容"　wrap="off" v-if="todoEdit" v-model="todo" v-html="todo"></textarea>
    <div v-for="(d,k) in file_paths">
        <a target="_blank" v-bind:href="d[0]">{{d[1]}}</a>
        <input type="checkbox" v-model="d[2]">
    </div>
    <form id="form">
        <input name="files[]" type="file" id="file" multiple="multiple">
        <div style="color:red;" v-if="fileErr">ファイルが２MB以上です</div>
    </form>
    </div>
    <br>
    
    <div class="centerize">
    <template v-if="tag!=2 && access_right==7 && group_id > 0">
        <select style="height:30px;width:80%;" v-model="group_id" @change="groupChange(group_id)">
            <option disabled>所属グループ</option>
            <template v-for="(d,k) in arr_group">
            <option v-bind:value="d['group_id']">{{d['group_name']}}</option>
            </template>
        </select>
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="ユーザー検索" id="searchUsr" style="height:40px;">
        <img src="/img/icon/magnifier.png" @click="searchUsr" class="icon">
    </div>
    <div class="joining">
        <div>候補者</div>
    <template v-for="(d,k) in group_usrs">
        <label v-bind:for="d[0]"><div style="margin:5px;">
            <div style="width:80%;display:inline-block;">{{d[1]}}</div>
            <div style="width:10%;display:inline-block;">
                <input type="checkbox"　v-bind:value="d" v-model="join_usrs" v-bind:id="d[0]">
            </div></div></label>
    </template>
    </div>
    <div class="centerize">↓</div>
    <div class="joining">
        <div>参加者</div>
    <template v-for="(d,k) in reverseUsrs">
        <template v-for="(d2,k2) in d">
        <div v-if="k2 == 1">{{d2}}</div>
        </template>
    </template>
    </div>
    <div class="joining">
        <div>候補施設</div>
    <template v-for="(d,k) in group_facility">
        <label v-bind:for="d[0]"><div style="margin:5px;">
            <div style="width:80%;display:inline-block;">{{d[1]}}</div>
            <div style="width:10%;display:inline-block;">
                <input type="checkbox"　v-bind:value="d" v-model="join_facility" v-bind:id="d[0]">
            </div></div></label>
    </template>
    </div>
    <div class="centerize">↓</div>
    <div class="joining">
    <template v-for="(d,k) in reverseFacility">
        <div>{{d[1]}}</div>
    </template>
        <div>使用施設</div>
    </div><br>
    <a target="_blank" v-bind:href="'/Calendar/Space/index/<?=$date?>/'+checkSchedule+'/'">空き時間を確認</a>
    </template><template v-else-if="tag!=2 && access_right < 7 && group_id > 0">
        <div class="joining">
            <div>参加者</div>
        <template v-for="(d,k) in reverseUsrs">
            <template v-for="(d2,k2) in d">
            <div v-if="k2 == 1">{{d2}}</div>
            </template>
        </template>
        </div>
        <div class="joining">
        <template v-for="(d,k) in reverseFacility">
            <div>{{d[1]}}</div>
        </template>
            <div>使用施設</div>
        </div>
    </template>
    </div>
    <div class="centerize">
        <input type="text" placeholder="公開タイトル" style="height:50px;width:80%;"
               v-model="public_title" v-bind:value="public_title" >
    </div>
    <div class="centerize" style="padding:10px;">
        公開
        <input type="checkbox" v-model="open" :value="open" >
    </div>
    <br><br><br>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

let date = '<?=$date?>';
var app = new Vue({
  el: '#content',
  data: {
      tag:<?=$tag?>
      ,tags:eval(<?=json_encode($tags)?>)
      ,title:<?=json_encode($title)?>
      ,titleErr:false
      ,off_tags:[]
      ,leave_id:null
      ,displayTime:true
      ,next:[]
      ,next_date:eval(<?=json_encode($next)?>)
      ,date_end:'<?=$date_end?>'
      ,hours:eval(<?=json_encode($hours)?>)
      ,minutes:['00','15','30','45']
      ,hourStart:'<?=$hourStart?>'
      ,minuteStart:'00'
      ,hourEnd:'<?=$hourEnd?>'
      ,minuteEnd:'00'
      ,off_base : 'day'
      ,todo : <?=json_encode($todo)?>
      ,todoEdit : <?=json_encode($todo)?> ? false : true
      ,file_paths: eval(<?=$file_paths?>)
      ,join_usrs:eval(<?=$join_usrs?>)
      ,group_usrs:[]
      ,join_facility:[]
      ,group_facility:[]
      ,group_ids: []
      ,arr_group:[]
      ,schedule_id:'<?=$schedule_id?>'
      ,group_id: <?=$group_id?>
      ,public_title : <?=json_encode($public_title)?>
      ,access_right : eval(<?=$access_right?>)
      ,fileErr:false
      ,open: <?=$open?>
  },
  computed: {
    reverseUsrs() {
        var arr = [];
        for(var i = 0; i < this.join_usrs.length; i++){
//            arr.push(this.join_usrs[i][0]);
            var param = this.join_usrs[i][0];
            if(this.join_usrs[i][0]){
                arr[this.join_usrs[i][0]] = this.join_usrs[i];
            }
        }
        return arr.slice().reverse();
    },
    reverseFacility() {
        return this.join_facility.slice().reverse();
    },
    checkSchedule() {
        var arr = [];
        for (var i = 0; i < this.join_usrs.length; i++) {
            arr.push(this.join_usrs[i][0]);
        }
        for (var i = 0; i < this.join_facility.length; i++) {
            arr.push(this.join_facility[i][0]);
        }
        return encodeURIComponent(JSON.stringify(arr));
    },
    nextDay: function () {
        var next = this.next_date;
        var available = 30;
        this.displayTime = true;
        if(this.tag == 2){
            for (var i = 0; i < this.off_tags.length; i++) {
                if(this.off_tags[i]['leave_id'] == this.leave_id && this.off_tags[i]['leave_amount_flg']){
                    available = this.off_tags[i]['available'] -1;
                }
                if(this.off_tags[i]['leave_id'] == this.leave_id && !this.off_tags[i]['leave_amount_flg']){
                    this.displayTime = false;
                    this.hourStart = '00';
                    this.minuteStart = '00';
                    this.hourEnd = '23';
                    this.minuteEnd = '59';
                }
            }
            var next = next.slice(0,available);
        }
        return next;
    },
  },
  methods: {
    groupChange: function (group_id) {
        $.get('/Calendar/GroupGet/get/'+group_id +'/',{},function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                app.group_usrs = res[1];
                app.group_facility = res[2];
                app.group_ids = res[3];
                app.arr_group = res[4];
                app.group_id = res[5];
            }else{
                alert('system error');
            }
        });
    },
    searchUsr: function () {
        $.get('/Calendar/GroupUsr/get/'+$('#searchUsr').val() +'/',{},function(){},"json")
        .always(function(res){
            app.group_usrs = res;
        });
    },
    editTodo: function () {
        this.todoEdit = this.todoEdit ? false : true;
    },
    copy: function () {
        for (var i = 0; i < this.off_tags.length; i++) {
            if(this.off_tags[i]['leave_id'] == this.leave_id){
                var title = this.off_tags[i]['leave_name']
            }
        }
        if(this.tag == 2 && !this.public_title){
            this.public_title = this.tags[2][0];
            this.title = title;
        }else if(this.tag == 2){
            this.title = title;
        }else if(!this.public_title){
            this.public_title = this.title;
        }
    },
  }
});
setTimeout(function(){
  app.groupChange(app.group_id);
},1000);


function update(){
    var validate = 1;
    if( (app.title.length < 1 || app.title.length > 10) && app.tag != 2 ){
        app.titleErr = true;
        validate=2;
    }else{
        app.titleErr = false;
    }
    if($('#file').prop('files')[0] && $('#file').prop('files')[0].size > 2000000){
        app.fileErr = true;
        validate=2;
    }else{
        app.fileErr = false;
    }
    if(validate==2){
      return;
    }
    var arr = [];
    for (var i = 0; i < app.join_usrs.length; i++) {
        arr.push(app.join_usrs[i][0]);
    }
    for (var i = 0; i < app.join_facility.length; i++) {
        arr.push(app.join_facility[i][0]);
    }
    var post_url = '/Calendar/ScheduleAdd/';
    if(app.access_right == 6){
        post_url = '/Calendar/TodoEdit/';
    }else if(app.tag == 2 && app.schedule_id){
        post_url = '/Calendar/OffEdit/';
    }else if(app.tag == 2){
        post_url = '/Calendar/OffAdd/';
    }else if(app.schedule_id){
        post_url = '/Calendar/ScheduleEdit/';
    }
    if($('#file').prop('files')[0]){
        var fd = new FormData($('#form')[0]);
    }else{
        var fd = new FormData();
    }
    fd.append("_token", $('[name="csrf-token"]').attr('content'));
    fd.append("tag", app.tag);
    fd.append("title", app.title);
    fd.append("time_start", date +' '+ app.hourStart+':'+app.minuteStart+':00');
    fd.append("time_end", app.date_end +' '+ app.hourEnd+':'+app.minuteEnd+':59');
    fd.append("todo", app.todo);
    fd.append("usrs", JSON.stringify(arr));
    fd.append("group_id", app.group_id);
    fd.append("schedule_id", app.schedule_id);
    fd.append("public_title", app.public_title);
    fd.append("leave_id", app.leave_id);
    fd.append("file_paths", JSON.stringify(app.file_paths));
    fd.append("open", app.open);
    $.ajax({url:post_url,type:'post',data:fd,processData:false,contentType:false,cache:false,dataType:"json",})
    .always(function(res){
        if(res[0] == 1){
            location.href = '/Calendar/Top/index/'+date.substr(0,7)+'/';
        }else{
            alert('system error');
        }
    });
}
function del(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,schedule_id : app.schedule_id
    }
    var post_url = '/Calendar/ScheduleDelete/';
    if(app.tag == 2){
        post_url = '/Calendar/OffDelete/';
    }
    $.post(post_url,param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
            location.href = '/Calendar/Top/index/'+date.substr(0,7)+'/';
        }else{
            alert('system error');
        }
    });
}
$.get('/Calendar/OffGet/',{date:date,schedule_id:app.schedule_id},function(){},"json")
.always(function(res){
    app.off_tags = res[1];
    app.leave_id = res[2];
    app.next = res[3];
});
function AutoLink(str) {
    var regexp_url = /((h?)(ttps?:\/\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+))/g; // ']))/;
    var regexp_makeLink = function(all, url, h, href) {
        return '<a target="_blank" href="h' + href + '">' + url + '</a>';
    }
 
    return str.replace(regexp_url, regexp_makeLink);
}
function nl2br(str) {
    str = str.replace(/\r\n/g, "<br />");
    str = str.replace(/(\n|\r)/g, "<br />");
    return str;
}
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
