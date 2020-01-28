<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?=$m_group->group_name?></title>
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
    .centerize {
        width: 100%;
        text-align: center;
    }
    .joining {
        width: 80%;
        height: 120px;
        border: 1px solid #000000;
        display: inline-block;
        overflow: scroll;
        text-align: left;
    }
    .owner {
        background-color: yellowgreen;
    }
</style>
<div id="content">
    <div class="centerize">
    <input type="text" placeholder="グループ名" id="group_name" value="<?=$m_group->group_name?>" style="height:50px;width:80%;">
    <br><br>
    <select name='category_id' style="height:30px;width:80%;">
        <option value="0" <?=$people?> >人</option>
        <option value="1" <?=$facility?> >施設</option>
    </select><br><br>
    <a v-bind:href="'/test/auth/'+password+'/'" target="_blank" id="password">{{password}}</a>
    &nbsp;&nbsp;<img src="/img/icon/pencil.png" class="icon" id="shuffle">
    </div>

    <br>
    <br>
    <div class="centerize">
    <div style="width:100%;display:inline-block;">
        <input type="text" placeholder="ユーザー検索" style="height:40px;">
        <img src="/img/icon/magnifier.png" id="search" oauth="people" class="icon">
        <select class="group" id="people_group" style="height:30px;width:80%;" oauth="people">
            <option disabled>所属グループ</option>
            <?php $i = 0; foreach($arr_group as $d){ if($d['category_id'] == 0){ ?>
            <option <?=$i == 0 ? 'selected' : '' ?> value="<?=$d['group_id']?>"><?=$d['group_name']?></option>
            <?php $i++; }}?>
        </select><br>
    </div>
    <div class="joining">
        <div>候補者</div>
    <template v-for="(d,k) in group_usrs">
        <label v-bind:for="'g_u_'+d[0]"><div style="margin:5px;">
        <div style="width:80%;display:inline-block;">{{d[1]}}</div>
        <div style="width:15%;display:inline-block;text-align:right;">
            <input type="checkbox"　v-bind:value="d[0]" v-model="arr_usrs" v-bind:id="'g_u_'+d[0]">
        </div></div></label>
    </template>
    </div>
    <div class="centerize">↓</div>
    <div class="joining">
        <div style="width:80%;display:inline-block;">参加者</div>
        <div style="width:15%;display:inline-block;text-align:right;">管理者</div>
    <template v-for="(d,k) in reverseUsrs">
        <label v-bind:for="'j_u_'+d[0]"><div style="margin:5px;">
        <div style="width:80%;display:inline-block;">{{d[1]}}</div>
        <div style="width:15%;display:inline-block;text-align:right;">
            <input type="checkbox" v-bind:value="d[2]" v-model="d[2]" v-bind:id="'j_u_'+d[0]" v-bind:disabled="d[4]" >
         </div></div></label>
    </template>
    </div>
    </div>
    <br>
    <div class="centerize">
    <div style="width:100%;display:inline-block;">
        <select class="group" id="facility_group" style="height:30px;width:80%;" oauth="facility">
            <option disabled>施設カテゴリ</option>
            <?php $i = 0; foreach($arr_group as $d){ if($d['category_id'] == 1){ ?>
            <option <?=$i == 0 ? 'selected' : '' ?> value="<?=$d['group_id']?>"><?=$d['group_name']?></option>
            <?php $i++; }}?>
        </select><br>
    </div>
    <div class="joining">
    <div>候補施設　全て</div>
    <template v-for="(d,k) in group_facility">
        <label v-bind:for="'g_f_'+d[0]"><div style="margin:5px;">
            <div style="width:80%;display:inline-block;">{{d[1]}}</div>
            <div style="width:15%;display:inline-block;text-align:right;">
                <input type="checkbox"　v-bind:value="d[0]" v-model="arr_facility" v-bind:id="'g_f_'+d[0]" v-bind:disabled="d[5]">
            </div></div></label>
    </template>
    </div>
    <div class="centerize">↓</div>
    <div class="joining">
    <div>利用施設</div>
    <template v-for="(d) in reverseFacility">
        <div style="margin:5px;" v-bind:class="{ owner: d[4] != 5 }">
        <div style="width:80%;display:inline-block;">{{d[1]}}</div>
        <div style="width:15%;display:inline-block;text-align:right;"></div>
        </div>
    </template>
    </div>
    </div>
    <br>
    <div class="centerize">
    <input type="button" value="登録・更新"　style="height:30px;width:80%;" id="submit">
    </div>
</div>
<br>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>


<script>
var join_usrs1 = <?=$join_usrs?>;
var join_facility1 = <?=$join_facility?>;
var content = new Vue({
  el: '#content',
  data: {
      password:'<?=$m_group->password?>'
      ,usr_id : <?=$usr_id?>
      ,group_usrs:[]
      ,arr_usrs: <?=$usr_ids?>
      ,join_usrs:[]
      ,group_facility:[]
      ,arr_facility:<?=$usr_ids?>
      ,join_facility:[]
      ,j_u:[]
  },
  computed: {
    reverseUsrs() {
        var join = [];
        var i = 0;
        for (var k in this.arr_usrs) {
            if(this.group_usrs[this.arr_usrs[k]]){
                join[i] = this.group_usrs[this.arr_usrs[k]];
                i++;
            } else {  //nor group_usrs but join_usrs has. it should show
                for (var i2=0; this.join_usrs.length > i2; i2++) {
                    if(this.join_usrs[i2][0] == this.arr_usrs[k]){
                        join[i] = this.join_usrs[i2];
                        i++;
                    }
                }
            }
        }
        this.join_usrs = join;
        return this.join_usrs.slice().reverse();
    },
    reverseFacility() {
        console.log(this.group_facility);
        if(join_facility1){
            this.join_facility = join_facility1;
            join_facility1 = null;
            return this.join_facility.slice().reverse();
        }
        var join = [];
        var i = 0;
        for (var k in this.arr_facility) {
            if(this.group_facility[this.arr_facility[k]]){
                join[i] = this.group_facility[this.arr_facility[k]];
                i++;
            } else {  //nor group_facility but join_facility has. it should show
                for (var i2=0; this.join_facility.length > i2; i2++) {
                    if(this.join_facility[i2][0] == this.arr_facility[k]){
                        join[i] = this.join_facility[i2];
                        i++;
                    }
                }
            }
        }
        
        this.join_facility = join;
        return this.join_facility.slice().reverse();
    },
  },
    watch: { // after, before same value somehow
        join_usrs: { handler: function (after, before) {
            var join = [];
            var arr_is = [];
            var is = false;
            var i2 = 0;
            for (var i=0; this.join_usrs.length > i; i++) {
                if(this.join_usrs[i][2]){
                    arr_is[i2] = true;
                    i2++;
                }
                is = true;
                join[i] = this.join_usrs[i];
                join[i][4] = null;
            }
            if(arr_is.length == 1 && is){
                for (var i=0; join.length > i; i++) {
                    if(join[i][2]){
                        join[i][4] = 'disabled';
                    }
                }
            }
            if(join_usrs1){
//                this.join_usrs = join_usrs1;
//                join_usrs1 = null;
//                console.log('within initial');
            } else {
                this.join_usrs = join; 
                
            }
            console.log(join_usrs1);
            console.log('watch after');
        },deep : true},
/*        arr_facility: { handler: function (after, before) {
            var arr_is = [];
            var i = 0;
            var join = [];
            var is = false;
            for (var k in this.arr_facility) {
                for (var i2=0; this.join_facility.length > i2; i2++) {
                    if(this.join_facility[i2][0] == this.arr_facility[k]){
                        if(this.join_facility[i2][4] != 5){
                            arr_is[i] = this.join_facility[i2][0];
                            i++;
                        }
                    }
                    is = true;
                }
            }
            for (var i in this.group_facility) {
                if(this.group_facility[i]){
                    join[i] = this.group_facility[i];
                    join[i][5] = null;
                }
            }
            for (var i=0; arr_is.length > i; i++) {
                if(join[arr_is[i]]){
                    join[arr_is[i]][5] = 'disabled';
                }
            }
//            this.group_facility = join;
        },deep : true},
*/
    },
  methods : {
//    checkIs : function(){
//    }
  },
});

function randomString(length) {
    var result = '';
    var length = 16
    var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}
content.password = content.password ? content.password : randomString(16);
$('#shuffle').click(function(){
    content.password = randomString(16);
});

$('#submit').click(function(){
    console.log($('.j_u').val());
    console.log($('.j_u').attr("checked"));
    console.log($('.j_u').val());
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
//            var arr_is = [];
//            var is = 0;
//            for (var i=0; content.arr_facility.length > i; i++) {
//                if(res[content.arr_facility[i]] && res[content.arr_facility[i]][4] != 5){
//                    res[content.arr_facility[i]][5] = null;
//                    arr_is[is] = content.arr_facility[i];
//                }
//            }
//            if(arr_is.length == 1){
//                res[arr_is[0]][5] = 'disabled';
//            }
            content.group_facility = res;
        }else if(oauth == 'people'){
            content.group_usrs = res;
        }
    });
}

//$(function(){ ga('send', 'pageview'); });
</script>
</body>
</html>
