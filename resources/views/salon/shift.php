<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Shift</title>
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
input[type="text"]{
    width: 50px;
    padding: 10px;
}
.available {
    border-collapse: collapse;
}
.available_tr {
    text-align: center;
    height : 50px;
}
.X {
    background-color: gray;
    opacity: 0.2;
    color: white;
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

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
<div v-show="!advance">
<table style="width:100%;">
    <tr class="available_tr">
        <template v-for="(d,k) in week" >
          <td>{{d}}</td>
        </template>
    </tr>
    <tr class="available_tr">
    <template v-for="(d,k) in week">
        <td v-on:click="toggle(k)" v-bind:class="routine[0]['shift_'+k]">{{routine[0]['shift_'+k]}}</td>
    </template>
    </tr>
</table>

<table style="width:100%;">
    <tr class="available_tr">
        <td><input type="text" :value="startTime" v-model="startTime" @change="timeAll('start')" ></td>
        <td> ~ </td>
        <td><input type="text" :value="endTime" v-model="endTime" @change="timeAll('end')" ></td>
    </tr>
</table>
<table style="width:100%;" ><tr class="available_tr"><td>
    <a style="color: blue;" v-on:click="advanceToggle"> - - <?=__('salon.advance')?> - - </a>
</td></tr></table>
</div>

<table style="width:100%;" v-show="advance">
    <template v-for="(d,k) in week" >
    <tr class="available_tr" v-bind:class="routine[0]['shift_'+k]">
        <td>
            <input type="text" :value="routine[0]['start_'+k]" v-model="routine[0]['start_'+k]" @change="timeW('start_'+k)" >
        </td>
        <td> ~ </td>
        <td>
            <input type="text" :value="routine[0]['end_'+k]" v-model="routine[0]['end_'+k]" @change="timeW('end_'+k)" >
        </td>
    </tr>
    </template>
</table>

<table style="width:100%;" v-show="advance"><tr class="available_tr"><td>
    <a style="color: blue;" v-on:click="advanceToggle"> - - <?=__('salon.simple')?> - - </a>
</td></tr></table>

<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('salon.update')?>" class="column1" v-on:click="update">
</div>

<div style="width:100%;text-align: center;" v-if="!routine[0]['fix_flg']">
    <input type="submit" value="<?=__('salon.shiftAdd')?>" class="column1" v-on:click="shiftAdd">
</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    routine: eval(<?=$routine?>),
    week: eval(<?=$week?>),
    advance: eval(<?=$advance?>),
    startTime: '<?=$startTime?>',
    endTime: '<?=$endTime?>',
  },
  methods: {
    update: function (menu_id) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,routine : this.routine
        }
        $.post('/Salon/RoutineUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    shiftAdd: function (e) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
        }
        $.post('/Salon/ShiftAdd/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
                location.href = '';
            }else{
                alert('system error');
            }
        });
    },
    toggle: function (k) {
        if (this.routine[0]['shift_'+k] === 'O') {
            this.$set(this.routine[0],['shift_'+k],'X');
        } else {
            this.$set(this.routine[0],['shift_'+k],'O');
        }
    },
    advanceToggle: function (e) {
        if(this.advance){
            this.advance = false;
        }else{
            this.advance = true;
        }
    },
    timeAll: function (str) {
        var t;
        if(str == 'start'){
            t = this.startTime.replace(/[^0-9]/g,'');
        } else {
            t = this.endTime.replace(/[^0-9]/g,'');
        }
        var hour = t.substr(0,2) * 1;
        hour = hour > 23 ? 23 : hour ;
        hour = hour < 10 ? '0'+hour : hour ;
        hour = hour < 1 ? '00' : hour ;
        var minute =  t.substr(2,2) * 1;
        minute = minute > 59 ? 59 : minute;
        minute = minute < 10 ? '0' + minute : minute;
        minute = minute < 1 ? '00' : minute;
        if(str == 'start'){
            this.startTime = hour + ':' + minute;
        } else {
            this.endTime = hour + ':' + minute;
        }
        for (var i = 0; i<7; i++) {
            this.$set(this.routine[0],[str+'_'+i],hour + ':' + minute);
        }
        console.log(this.routine[0]);
    },
    timeW: function (str) {
        var t;
        t = this.routine[0][str].replace(/[^0-9]/g,'');
        var hour = t.substr(0,2) * 1;
        hour = hour > 23 ? 23 : hour ;
        hour = hour < 10 ? '0'+hour : hour ;
        hour = hour < 1 ? '00' : hour ;
        var minute =  t.substr(2,2) * 1;
        minute = minute > 59 ? 59 : minute;
        minute = minute < 10 ? '0' + minute : minute;
        minute = minute < 1 ? '00' : minute;
        this.routine[0][str] = hour + ':' + minute;
        console.log(this.routine[0]);
    },
  },
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>