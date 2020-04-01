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
    input[name="group"]{
        display:none;
    }
    input[name="group"] + label {
        display:inline-block;
        width: 30%;
        padding: 10px 0px 10px 0px;
    }
    input[name="group"]:checked + label {
        display:inline-block;
        width: 30%;
        padding: 10px 0px 10px 0px;
        font-weight: bold;
        background-color: gray;
    }
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
</style>
<div id="content">
    <div class="centerize">
        <select name='tag' style="height:30px;width:80%;">
            <?php foreach($tags as $k =>  $d){ ?>
            <option value="<?=$k?>" style="background-color: <?=$d[1]?>" <?=$d[2]?>><?=$d[0]?></option>
            <?php } ?>
        </select><br>
        <input type="text" placeholder="タイトル" id="title" value="<?=htmlspecialchars($title)?>" style="height:50px;width:80%;">
        <div style="color:red;display:none;" id="titleErr">1文字以上10文字以内でお願いします</div>
    </div>
    <div class="centerize">
        <?=date('m/d',strtotime($date))?>&nbsp;
        <select name='hour_start' style="height:30px;">
        <?php foreach($hour_start as $d){?>
            <option <?=$d[1]?>><?=$d[0]?></option>
        <?php }?>
        </select>
        <select name='minute_start' style="height:30px;">
        <?php foreach($minute_start as $d){?>
            <option <?=$d[1]?>><?=$d[0]?></option>
        <?php }?>
        </select>
        <span>~</span>
        <select name='hour_end' style="height:30px;">
        <?php foreach($hour_end as $d){?>
            <option <?=$d[1]?>><?=$d[0]?></option>
        <?php }?>
        </select>
        <select name='minute_end' style="height:30px;">
        <?php foreach($minute_end as $d){?>
            <option <?=$d[1]?>><?=$d[0]?></option>
        <?php }?>
        </select>
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
    <div class="centerize">
        <textarea style="width:90%;height:120px;" placeholder="内容" id="todo"><?=htmlspecialchars($todo)?></textarea>
    </div>
    <br>
    <div id="app">
    <div class="centerize">
        <select style="height:30px;width:80%;" v-model="group_id" v-on:change="groupChange(group_id)">
            <option disabled>所属グループ</option>
            <template v-for="(d,k) in arr_group">
            <option v-bind:value="d['group_id']">{{d['group_name']}}</option>
            </template>
        </select><br>
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="ユーザー検索" style="height:40px;">
        <img src="/img/icon/magnifier.png" v-on:click="search" class="icon">
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
    </div>
    </div>
    <a target="_blank" v-bind:href="'/Calendar/Space/hours12/<?=$date?>/'+checkSchedule+'/'">空き時間を確認</a>
    </div> <!-- end vue app -->
    <div class="centerize">
        <select name="public_tag" style="height:30px;width:80%;">
            <option value="0">公開タグ</option>
            <?php foreach($public_tags as $k => $d){ ?>
            <option value="<?=$k?>" style="background-color: <?=$d[1]?>" <?=$d[2]?>><?=$d[0]?></option>
            <?php } ?>
        </select><br>
        <input type="text" placeholder="公開タイトル" id="public_title" value="<?=$public_title?>" style="height:50px;width:80%;">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>
let date = '<?=$date?>';
var app = new Vue({
  el: '#app',
  data: {
//      join_usrs:[]
      join_usrs:eval(<?=$join_usrs?>)
//      ,group_usrs:[[1,'hi'],[2,'ddkk']]
      ,group_usrs:[]
      ,join_facility:[]
      ,group_facility:[]
      ,group_ids: eval(<?=$group_ids?>)
      ,arr_group:eval(<?=$arr_group?>)
      ,schedule_id:'<?=$schedule_id?>'
      ,group_id: <?=$group_id?>
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
  }
});
app.groupChange(app.group_id);

function update(){
    var validate = 1;
    if($('[name=tag]').val()==0){
        $('[name=tag]').css({'border-color':'red'});
        validate=2;
    }else{
        $('[name=tag]').css({'border-color':''});
    }
    if($('#title').val().length < 1 || $('#title').val().length > 10){
        $('#title').css({'border-color':'red'});
        $('#titleErr').css({'display':''});
        validate=2;
    }else{
        $('#title').css({'border-color':''});
        $('#titleErr').css({'display':'none'});
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
        ,tag : $('[name=tag]').val()
        ,title : $('#title').val()
        ,time_start : date +' '+ $('[name=hour_start]').val() + ':' + $('[name=minute_start]').val() + ':00'
        ,time_end : date +' '+ $('[name=hour_end]').val() + ':' + $('[name=minute_end]').val() + ':00'
        ,todo : $('#todo').val()
        ,usrs : arr
        ,group_id : app.group_id
        ,schedule_id : app.schedule_id
        ,public_tag : $('[name=public_tag]').val()
        ,public_title : $('#public_title').val()
    }
    var post_url =  app.schedule_id ? '/Calendar/ScheduleEdit/' : '/Calendar/ScheduleAdd/' ;
    $.post(post_url,param,function(){},"json")
    .always(function(res){
        if(res[0]){
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
    $.post('/Calendar/ScheduleDelete/',param,function(){},"json")
    .always(function(res){
        if(res[0]){
            location.href = '/Calendar/Top/index/'+date.substr(0,7)+'/';
        }else{
            alert('system error');
        }
    });
}
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
