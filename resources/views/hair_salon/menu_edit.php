<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>menu edit</title>
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

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>

<div id="content">

<div id="ad" style="text-align: center;"><iframe src="/htm/ad/" width="320" height="50" frameborder="0" scrolling="no"></iframe></div>

<div style="width:100%;text-align: center;">
    <input type="text" v-model="menu_name" class="column1" v-on:change="checkMenu"><br>
    <div id="menu_name_error" style="color: red;" v-if="menu_error"><?=__('hair_salon.menuNameError')?></div>
</div>

<div v-for="(d,k) in necessary" style="width:100%;text-align: center;">
    <div style="width:100%;text-align:right;"><span style="font-size:20px;background-color: silver;" v-on:click="del(k)">&nbsp;-&nbsp;</span></div>
    <select style="width:90%;height:40px;" v-model="d.service_id" v-on:change="goService">
        <option v-for="(d2,k2) in services" v-bind:value="k2">{{ d2 }}</option>
        <option value="serviceAdd" >&nbsp;&nbsp;&nbsp;<?=__('hair_salon.serviceAdd')?></option>
    </select>
    <select style="width:90%;height:40px;" v-model="d.facility_id">
        <option v-for="(d2,k2) in facilitys" v-bind:value="k2">{{ d2 }}</option>
    </select>
    <br>
    <div v-bind:style="{ 'background-color': 'green', 'margin-top':'5px',
         'margin-left': d.start_minute / final_end_min * 100 + '%',
         'width': (d.end_minute - d.start_minute) / final_end_min * 99 + '%'}">minutes</div>
    <input type="text" v-bind:value="d.start_minute" v-model="d.start_minute" style="width:80px;">
    <input type="text" v-bind:value="d.end_minute" v-model="d.end_minute" style="width:80px;">
    <br><br>
</div>
<div style="width:100%;text-align:right;"><span style="font-size:20px;background-color:silver;" v-on:click="add">&nbsp;+&nbsp;</span></div>
<div style="width:100%;text-align: center;">
<input type="submit" value="<?=__('hair_salon.menu_new')?>" class="column1" v-on:click="update(0)"><br>
<input type="submit" value="<?=__('hair_salon.menu_edit')?>" class="column1" v-on:click="update(<?=$menu->menu_id?>)"><br>
</div>
</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    menu_name: <?=json_encode($menu->menu_name)?>,
    necessary: eval(<?=$necessary?>),
    services: eval(<?=$services?>),
    facilitys: eval(<?=$facilitys?>),
    addObj: eval(<?=$add?>),
    menu_error: false,
  },
  methods: {
    del: function (k) {
        this.$delete(this.necessary,k);
    },
    add: function (e) {
        this.$set(this.necessary,this.necessary.length,this.addObj);
    },
    checkMenu: function (e) {
      if (this.menu_name.length < 3 || this.menu_name.length > 40) {
        this.menu_error = true;
      }else{
        this.menu_error = false;
      }
      e.preventDefault();
    },
    goService: function (e) {
        if(e.target.value === 'serviceAdd'){
            location.href = '/HairSalon/Service/add/';
        }
    },
    update: function (menu_id) {
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,menu_name : this.menu_name
            ,necessary : this.necessary
            ,menu_id : menu_id
        }
        $.post('/HairSalon/MenuUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0]){
                location.href = '/HairSalon/MenuEdit/edit/'+res[1];
            }else{
                alert('system error');
            }
        });
    },
  },
  computed: {
    final_end_min() {
        var final_end_min = 0;
        for (const k in this.necessary) {
            if(this.necessary[k].end_minute > final_end_min){
                final_end_min = this.necessary[k].end_minute;
            }
        }
        return final_end_min;
    },
  }
});
</script>

<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>


</body>
</html>
