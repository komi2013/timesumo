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

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>
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
</style>
<div id="content">
    <div class="centerize">
        <select style="width:80%;" v-model="tag" @change="copy">
            <option v-for="(d,k) in tags" v-bind:value="k" 
                    v-bind:style="'background-color:' + d[1]">{{d[0]}}</option>
        </select>
        <template v-if="tag==2">
        <select style="width:80%;" v-model="leave_id">
            <option v-for="(d,k) in off_tags" v-bind:value="d['leave_id']">{{d['leave_name']}}</option>
        </select>
        </template>
        <template v-else>
            <input type="text" placeholder="タイトル" style="height:50px;width:80%;" v-bind:value="title" v-model="title" @blur="copy" >
            <div style="color:red;" v-if="titleErr">1文字以上10文字以内でお願いします</div>
        </template>
    </div>
    <br>
    <div class="centerize">
        <?=date(__('calendar.date'),strtotime($date))?>&nbsp;
        <template v-if="tag == 2 && off_base == 'day'">
            <template v-if="nextDay.length > 0 && off_base == 'day'">
                ~
            <select  v-model="date_end">
            <option v-for="d in nextDay" v-bind:value="d[0]">{{d[1]}}</option>
            </select>
            </template>
        </template>
        <template v-else>
        <select v-model="hourStart">
        <option v-for="d in hours" v-bind:value="d">{{d}}</option>
        </select>
        <select v-model="minuteStart">
        <option v-for="d in minutes" v-bind:value="d">{{d}}</option>
        </select>
        <span>~</span>
        <select v-model="hourEnd">
        <option v-for="d in hours" v-bind:value="d">{{d}}</option>
        </select>
        <select v-model="minuteEnd">
        <option v-for="d in minutes" v-bind:value="d">{{d}}</option>
        </select>
        </template>
        <table style="width:100%;" v-if="tag == 2"><tr>
            <td style="width:49%;">
                <input type="radio" id="day" value="day" v-model="off_base">
                <label for="day"><?=__('calendar.day')?></label></td>
            <td style="width:49%;">
                <input type="radio" id="hour" value="hour" v-model="off_base">
                <label for="hour"><?=__('calendar.hour')?></label></td>
        </tr></table>
    </div>
    <br>
    <div class="centerize">
    <?php if($schedule_id){?>
        <input type="button" value="更新" style="height:30px;width:40%;" onclick="update()">
        <input type="button" value="削除" style="height:30px;width:40%;" onclick="del()">
    <?php }else{?>
        <input type="button" value="登録" style="height:30px;width:80%;" onclick="update()">
    <?php }?>
    </div>
    <br>
    <div style="width:97%;padding-left:2%;">
    <div style="width:97%;text-align:right;">
      <img src="/img/icon/pencil.png" style="max-height:20px;" v-if="!todoEdit" @click="editTodo" >
      <img src="/img/icon/list.png" style="max-height:20px;" v-if="todoEdit" @click="editTodo" >
    </div>
    <div style="font-size: 12px;"
         v-if="!todoEdit" v-html="AutoLink(nl2br(todo))"></div>
    <textarea style="width:95%;height:120px;font-size:12px;position:relative;background-color: white;"
              placeholder="内容"　wrap="off" v-if="todoEdit" v-model="todo" v-html="todo"></textarea>
    </div>
    <br>
    <template v-if="tag!=2">
    <div class="centerize">
        <select style="height:30px;width:80%;" v-model="group_id" @change="groupChange(group_id)">
            <option disabled>所属グループ</option>
            <template v-for="(d,k) in arr_group">
            <option v-bind:value="d['group_id']">{{d['group_name']}}</option>
            </template>
        </select>
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="ユーザー検索" style="height:40px;">
        <img src="/img/icon/magnifier.png" @click="search" class="icon">
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
    <template v-for="(d,k) in reverseUsrs">
        <template v-for="(d2,k2) in d">
        <div v-if="k2 == 1">{{d2}}</div>
        </template>
    </template>
        <div>参加者</div>
    </div>
    </div>
    <div class="centerize">
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="施設検索" style="height:40px;">
        <img src="/img/icon/magnifier.png" fac_usr="facility" class="icon">
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
    <a target="_blank" v-bind:href="'/Calendar/Space/hours12/<?=$date?>/'+checkSchedule+'/'">空き時間を確認</a>
    </div>
    </template>
    <div class="centerize">
        <input type="text" placeholder="公開タイトル" style="height:50px;width:80%;"
               v-model="public_title" v-bind:value="public_title" >
    </div>
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
      ,next:[]
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
      ,join_usrs:eval(<?=$join_usrs?>)
//      ,group_usrs:[[1,'hi'],[2,'ddkk']]
      ,group_usrs:[]
      ,join_facility:[]
      ,group_facility:[]
      ,group_ids: eval(<?=$group_ids?>)
      ,arr_group:eval(<?=$arr_group?>)
      ,schedule_id:'<?=$schedule_id?>'
      ,group_id: <?=$group_id?>
      ,public_title : <?=json_encode($public_title)?>
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
        for (var i = 0; i < this.off_tags.length; i++) {
            if(this.off_tags[i]['leave_id'] == this.leave_id){
                var available = this.off_tags[i]['available'] -1;
            }
        }
        return this.next.slice(0,available);
    },
    time_start: function () {
        var hourStart = '00';
        var minuteStart = '00';
        if(this.tag != 2 || this.off_base == 'hour'){
            hourStart = this.hourStart;
            minuteStart = this.minuteStart;
        }
        return date +' '+ hourStart + ':' + minuteStart + ':00';
    },
    time_end: function () {
        var hourEnd = '23';
        var minuteEnd = '59';
        if(this.tag != 2 || this.off_base == 'hour'){
            hourEnd = this.hourEnd;
            minuteEnd = this.minuteEnd;
        }
        return this.date_end +' '+ hourEnd + ':' + minuteEnd + ':00';
    },
  },
  methods: {
    groupChange: function (group_id) {
        $.get('/Calendar/GroupGet/get/'+group_id +'/',{},function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                app.group_usrs = res[1];
                app.group_facility = res[2];   
            }else{
                alert('system error');
            }
        });
    },
    search: function (group_id) {
        var param = {group_ids:this.group_ids};
        $.get('/Calendar/Get/searchUsr/'+$('#searchUsr').val() +'/'+$('#search').attr('fac_usr')+'/',param,function(){},"json")
        .always(function(res){
            this.group_usrs = res;
        });
    },
    editTodo: function () {
        this.todoEdit = this.todoEdit ? false : true;
    },
    copy: function () {
        if(this.tag == 2 && !this.public_title){
            this.public_title = this.tags[2][0];
            this.title = this.off_tags[0]['leave_name'];
        }else if(this.tag == 2){
            this.title = this.off_tags[0]['leave_name'];
        }else if(!this.public_title){
            this.public_title = this.title;
        }
    },
  }
});

app.groupChange(app.group_id);

function update(){

    var validate = 1;
    if( (app.title.length < 1 || app.title.length > 10) & 
            app.tag != 2 ){
        app.titleErr = true;
        validate=2;
    }else{
        app.titleErr = false;
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
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,tag : app.tag
        ,title : app.title
        ,time_start : app.time_start
        ,time_end : app.time_end
        ,todo : app.todo
        ,usrs : arr
        ,group_id : app.group_id
        ,schedule_id : app.schedule_id
        ,public_title : app.public_title
        ,leave_id : app.leave_id
    }
    var post_url = '/Calendar/ScheduleAdd/';
    if(app.tag == 2 && app.schedule_id){
        post_url = '/Calendar/OffEdit/';
    }else if(app.tag == 2){
        post_url = '/Calendar/OffAdd/';
    }else if(app.schedule_id){
        post_url = '/Calendar/ScheduleEdit/';
    }
//    var post_url =  app.schedule_id ? '/Calendar/ScheduleEdit/' : '/Calendar/ScheduleAdd/' ;
    $.post(post_url,param,function(){},"json")
    .always(function(res){
        if(res[0] == 1){
//            location.href = '/Calendar/Top/index/'+date.substr(0,7)+'/';
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
//    app.date_end = '04/30';
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
