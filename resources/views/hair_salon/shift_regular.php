<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>shift regular</title>
    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<body>
<style>
    table {
        width:100%;
        border-collapse: collapse;
    }
    td {
        text-align: center;
        height : 50px;
    }
    .X {
        background-color: gray;
        opacity: 0.2;
        color: white;
    }
</style>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content">

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>
<div v-show="!advance">
<table>
    <tr>
        <template v-for="(d,k) in week" >
          <td>{{d}}</td>
        </template>
    </tr>
    <tr>
    <template v-for="(d,k) in week">
        <td v-on:click="toggle(k)" v-bind:class="routine[0]['shift_'+k]">{{routine[0]['shift_'+k]}}</td>
    </template>
    </tr>
</table>

<table>
    <tr>
        <td>
            <select style="height:30px;" v-on:change="startH" v-model="Hstart">
                <template v-for="i in startOption" >
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;" v-on:change="startM" v-model="Mstart">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
        <td> ~ </td>
        <td>
            <select style="height:30px;" v-on:change="endH" v-model="Hend">
                <template v-for="i in endOption">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;" v-on:change="endM" v-model="Mend">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
    </tr>
</table>
<table><tr><td>
    <a style="color: blue;" v-on:click="advanceToggle"> - - <?=__('hair_salon.advance')?> - - </a>
</td></tr></table>
</div>

<table v-show="advance">
    <template v-for="(d,k) in week" >
    <tr v-bind:class="routine[0]['shift_'+k]">
        <td v-on:click="toggle(k)" v-show="!advance2">{{d}}</td>
        <td rowspan="2" v-on:click="toggle(k)" v-show="advance2">{{d}}</td>
        <td v-on:click="toggle(k)" v-show="advance2"><?=__('hair_salon.work')?></td>
        <td>
            <select style="height:30px;" v-model="routine[0]['Hstart_'+k]">
                <template v-for="i in startOption">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;" v-model="routine[0]['Mstart_'+k]">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
        <td> ~ </td>
        <td>
            <select style="height:30px;" v-model="routine[0]['Hend_'+k]">
                <template v-for="i in endOption">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;"  v-model="routine[0]['Mend_'+k]">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
    </tr>
    <tr v-bind:class="routine[0]['shift_'+k]" v-show="advance2">
        <td v-on:click="toggle(k)"><?=__('hair_salon.break')?></td>
        <td>
            <select style="height:30px;">
                <template v-for="i in startOption">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
        <td> ~ </td>
        <td>
            <select style="height:30px;">
                <template v-for="i in endOption">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
            <select style="height:30px;">
                <template v-for="i in minutes">
                <option v-bind:value="i">{{i}}</option>
                </template>
            </select>
        </td>
    </tr>
    </template>
</table>

<table v-show="advance"><tr><td>
    <a style="color: blue;" v-on:click="advanceToggle"> - - <?=__('hair_salon.simple')?> - - </a>
</td></tr></table>
<table v-show="advance && !advance2"><tr><td>
    <a style="color: blue;" v-on:click="advance2Toggle"> - - <?=__('hair_salon.advance')?> - - </a>
</td></tr></table>

<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('hair_salon.update')?>" class="column1" v-on:click="update">
</div>

<div style="width:100%;text-align: center;">
    <input type="submit" value="<?=__('hair_salon.shiftAdd')?>" class="column1" v-on:click="shiftAdd">
</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    routine: eval(<?=$routine?>),
    startOption: eval(<?=$startOption?>),
    endOption: eval(<?=$endOption?>),
    minutes: eval(<?=$minutes?>),
    week: eval(<?=$week?>),
    advance: eval(<?=$advance?>),
    advance2: false,
    Hstart: '<?=$Hstart?>',
    Hend: '<?=$Hend?>',
    Mstart: '<?=$Mstart?>',
    Mend: '<?=$Mend?>',
  },
  methods: {
    update: function (menu_id) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,routine : this.routine
        }
        $.post('/HairSalon/RoutineUpdate/',param,function(){},"json")
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
        $.post('/HairSalon/ShiftAdd/',param,function(){},"json")
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
            if(!this.advance){
                for (  var i = 0;  i < 7;  i++  ) {
                    if(this.routine[0]['Hstart_'+i]){ var Hstart = this.routine[0]['Hstart_'+i]}
                    if(this.routine[0]['Mstart_'+i]){ var Mstart = this.routine[0]['Mstart_'+i]}
                    if(this.routine[0]['Hend_'+i]){ var Hend = this.routine[0]['Hend_'+i]}
                    if(this.routine[0]['Mend_'+i]){ var Mend = this.routine[0]['Mend_'+i]}
                }
                this.$set(this.routine[0],['Hstart_'+k],Hstart);
                this.$set(this.routine[0],['Mstart_'+k],Mstart);
                this.$set(this.routine[0],['Hend_'+k],Hend);
                this.$set(this.routine[0],['Mend_'+k],Mend);
            }

        }
    },
    advanceToggle: function (e) {
        if(this.advance){
            this.advance = false;
            this.advance2 = false;
        }else{
            this.advance = true;
        }
    },
    advance2Toggle: function (e) {
        this.advance2 = true;
    },
    startH: function (e) {
        for (  var i = 0;  i < 7;  i++  ) {
            this.$set(this.routine[0],['Hstart_'+i],e.target.value);
        }
    },
    startM: function (e) {
        for (  var i = 0;  i < 7;  i++  ) {
            this.$set(this.routine[0],['Mstart_'+i],e.target.value);
        }
    },
    endH: function (e) {
        for (  var i = 0;  i < 7;  i++  ) {
            this.$set(this.routine[0],['Hend_'+i],e.target.value);
        }
    },
    endM: function (e) {
        for (  var i = 0;  i < 7;  i++  ) {
            this.$set(this.routine[0],['Mend_'+i],e.target.value);
        }
    },
  },
  computed: {
//    final_end_min() {
//        var final_end_min = 0;
//        for (const k in this.necessary) {
//            if(this.necessary[k].end_minute > final_end_min){
//                final_end_min = this.necessary[k].end_minute;
//            }
//        }
//        return final_end_min;
//    },
  }
});
console.log(app.routine[0]['Hstart_0']);
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>