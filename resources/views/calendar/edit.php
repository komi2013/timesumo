<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?=date('Y/m/d',strtotime($date))?></title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-3"></script>
    <script src="/js/analytics.js<?=config('my.cache_v')?>"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>

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
            <option value="0">タグ</option>
            <?php foreach($tags as $d){ ?>
            <option value="<?=$d[0]?>" v-bind:style="'background-color:'+tag_color[<?=$d[0]?>]" <?=$d[1]?>><?=trans('tag.'.$d[0])?></option>
            <?php } ?>
        </select><br>
        <input type="text" placeholder="タイトル" id="title" value="<?=$a['title']?>" style="height:50px;width:80%;">
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
    <div class="centerize">
        <textarea id="todo" style="width:90%;height:120px;" placeholder="内容"><?=$a['todo']?></textarea>
    </div>
    <br>
    <div class="centerize">
    <input type="button" value="登録・更新"　style="height:30px;width:80%;" id="submit">
    </div>
    <br>
    <div class="centerize">
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="ユーザー検索" style="height:40px;">
        <img src="/img/icon/magnifier.png" id="search" oauth="people" class="icon">
        <select class="group" id="people_group" style="height:30px;width:80%;" oauth="people">
            <option disabled>所属グループ</option>
            <?php foreach($arr_group as $d){ ?>
            <option <?=$d['priority'] == 1 ? 'selected' : '' ?> value="<?=$d['group_id']?>"><?=$d['group_name']?></option>
            <?php }?>
        </select><br>
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
        <div>{{d[1]}}</div>
    </template>
        <div>参加者</div>
    </div>
    </div>
    <br>
    <a target="_blank" v-bind:href="'/Calendar/Space/hours12/<?=$date?>/'+checkSchedule+'/'">空き時間を確認</a>
    <br>
    <div class="centerize">
        <input type="radio" name="group" value="0" id="public" v-model="group_radio">
        <label for="public"> 公開</label>
        <input type="radio" name="group" value="1" id="private" v-model="group_radio">
        <label for="private"> 非公開</label>
        <input type="radio" name="group" value="2" id="group" v-model="group_radio">
        <label for="group"> 一部</label><br>
        <div v-if="group_radio == 2">
        <select id="select_group" style="height:30px;width:80%;" oauth="people" >
            <option>所属グループ</option>
            <?php foreach($arr_group as $d){ ?>
            <option <?=$d['selected']?> value="<?=$d['group_id']?>"><?=$d['group_name']?></option>
            <?php }?>
        </select>
        <br>
        </div>
    </div>
    <br>
    <div class="centerize">
        <select name="public_tag" style="height:30px;width:80%;">
            <option value="0">公開タグ</option>
            <?php foreach($public_tags as $d){ ?>
            <option value="<?=$d[0]?>" v-bind:style="'background-color:'+tag_color[<?=$d[0]?>]" <?=$d[1]?>><?=trans('tag.'.$d[0])?></option>
            <?php } ?>
        </select><br>
        <input type="text" placeholder="公開タイトル" id="public_title" value="<?=$a['public_title']?>" style="height:50px;width:80%;">
    </div>
    <br>
    <div class="centerize">
        <?php foreach($arr_group as $d){ if($d['owner_flg'] == 1){ ?>
        <div style="width:90%;text-align:left;display:inline-block;">
        <a href="/Calendar/Group/edit/<?=$d['group_id']?>/" target="_blank"><?=$d['group_name']?>
            <img src="/img/icon/pencil.png" style="height:20px;width:20px;"></a>
        </div>
        <?php }}?>
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var tag_color = ['','rgba(0,0,255,0.2)','rgba(0,128,0,0.2)','rgba(255,255,0,0.2)','rgba(255,0,0,0.2)','rgba(128,0,128,0.2)'];
//1=meeting, 2=off, 3=out, 4=task, 5=shift
var group_ids = '<?=$group_ids?>';
var group_radio = '<?=$group_radio?>';
var date = '<?=$date?>';
var common_id = '<?=$common_id?>';
var usr_id = '<?=$usr_id?>';
var arr_usr = <?=$arr_usr?>;
var content = new Vue({
  el: '#content',
  data: {
      join_usrs:[]
      ,group_usrs:[]
      ,join_facility:[]
      ,group_facility:[]
      ,group_radio:group_radio
  },
  computed: {
    reverseUsrs() {
        return this.join_usrs.slice().reverse();
    },
    reverseFacility() {
        return this.join_facility.slice().reverse();
    },
    checkSchedule() {
        var arr = [];
        for (var i = 0; i < this.join_usrs.length; i++) {
            arr.push(this.join_usrs[i]);
        }
        for (var i = 0; i < this.join_facility.length; i++) {
            arr.push(this.join_facility[i]);
        }
        return encodeURIComponent(JSON.stringify(arr));
    },
  }
});
$('#submit').click(function(){
    var validate = 1;
    if($('[name=tag]').val()==0){
        $('[name=tag]').css({'border-color':'red'});
        validate=2;
    }else{
        $('[name=tag]').css({'border-color':''});
    }
    if($('#title').val()==''){
        $('#title').css({'border-color':'red'});
        validate=2;
    }else{
        $('#title').css({'border-color':''});
    }
    if($('#todo').val()==''){
        $('#todo').css({'border-color':'red'});
        validate=2;
    }else{
        $('#todo').css({'border-color':''});
    }
    if(validate==2){
      return;
    }
    var arr = [];
    for (var i = 0; i < content.join_usrs.length; i++) {
        arr.push(content.join_usrs[i]);
    }
    for (var i = 0; i < content.join_facility.length; i++) {
        arr.push(content.join_facility[i]);
    }
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,tag : $('[name=tag]').val()
        ,title : $('#title').val()
        ,time_start : date +' '+ $('[name=hour_start]').val() + ':' + $('[name=minute_start]').val() + ':00'
        ,time_end : date +' '+ $('[name=hour_end]').val() + ':' + $('[name=minute_end]').val() + ':00'
        ,todo : $('#todo').val()
        ,usrs : arr
        ,public : $('[name=group]:checked').val()
        ,group_id : $('#select_group').val()
        ,common_id : common_id
        ,public_tag : $('[name=public_tag]').val()
        ,public_title : $('#public_title').val()
    }
    $.post('/Calendar/Update/',param,function(){},"json")
    .always(function(res){
        console.log(res);
    });
});
$('#search').click(function(){
    var param = {group_ids:JSON.parse(group_ids)};
    $.get('/Calendar/Get/searchUsr/'+$('#searchUsr').val() +'/'+$('#search').attr('oauth')+'/',param,function(){},"json")
    .always(function(res){
        content.group_usrs = res;
    });
});
groupChange($('#people_group option:selected').val(),'people');
groupChange($('#facility_group option:selected').val(),'facility');
$('.group').change(function(){
    var group_id = $(this).val();
    var oauth = $(this).attr('oauth');
    groupChange(group_id,oauth);
});
function groupChange(group_id,oauth){
    $.get('/Calendar/Get/groupUsr/'+group_id +'/'+oauth+'/',{},function(){},"json")
    .always(function(res){
        if(oauth == 'facility'){
//            var join = [];
//            var i = 0;
//            var duplicate = false;
//            for (var k in usrs) {
//                if(res[k]){
//                    for (var i3=0; content.join_facility.length > i3; i3++) {
//                        if(content.join_facility[i3]['usr_id'] == res[k]['usr_id']){
//                            duplicate = true;
//                        }
//                    }
//                    if(!duplicate){
//                       join[i] = res[k];
//                    }
//                    i++;
//                }
//            }
//            for (var i3=0; content.join_facility.length > i3; i3++) {
//                join.push(content.join_facility[i3]);
//            }
//            content.group_facility = res;
//            content.join_facility = join;
        }else{
//            var join = [];
//            var i = 0;
//            var duplicate = false;
//            for (var k in arr_usr) {
//                console.log(k);
//                if(res[k]){
//                    for (var i3=0; content.join_usrs.length > i3; i3++) {
//                        if(content.join_usrs[i3]['usr_id'] == res[usrs[k]]['usr_id']){
//                            duplicate = true;
//                        }
//                    }
//                    if(!duplicate){
//                       join[i] = res[usrs[k]];
//                    }
//                    i++;
//                }
//            }
//            for (var i3=0; content.join_usrs.length > i3; i3++) {
//                join.push(content.join_usrs[i3]);
//            }
            content.group_usrs = res;
//            content.join_usrs = join;
        }
        
    });
}
//$(function(){ ga('send', 'pageview'); });
</script>
</body>
</html>
