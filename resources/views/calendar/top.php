<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>My Calendar</title>
    <meta name="description" content="美容室、理容店、サロン検索">
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
  </head>
<body>

<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>


    <style>
        .offwork{
            color:orange;
        }
        th {
            font-size:10px;
            width: 14.28%;
            
        }
        table td {
            border-width: 0px;
/*            text-align: center;
            font-size: 10px;
            padding: 20px 0px 10px 0px;*/
        }
/*        .graph_frame {
            position: absolute;
        }*/
        .graph_bar {
            height: 7px;
            text-align: center;
        }
    </style>
<table>
    <thead><tr>
    <th>Sun</th>
    <th>Mon</th>
    <th>Tue</th>
    <th>Wed</th>
    <th>Thu</th>
    <th>Fri</th>
    <th>Sat</th>
    </tr></thead>
    <?php foreach ($arr_35days as $k => $d) {?>
        <?php if($d['day'] == 'Sun'){?> <tr> <?php }?>
        <td border="0" date="<?=$k?>">
            <?php for($i = 0; $i < 10; $i++){?>
            <div class="graph_bar d-<?=$k.'-'.$i.$d['css_class']?>">
                <?php if($i == 5){?>
                    <?=$d['j']?>
                <?php }else{?>
                    &nbsp;
                <?php }?>
            </div>
            <?php } ?>
        </td>
        <?php if($d['day'] == 'Sat'){?> </tr> <?php }?>
    <?php } ?>
</table>
<div id="content">
    <template v-for="(d,k) in detail">
        <div style="width:100%;margin-top:20px;">{{d['agenda']}}</div>
        <div style="width:100%;">　{{d['time_start']}} ~ {{d['time_end']}}</div>
        <div style="width:100%;">{{d['todo']}}</div>
        <div>{{d['file_paths']}}</div>
    </template>
</div>

<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

var tag_color = ['','rgba(0,0,255,0.2)','rgba(0,128,0,0.2)','rgba(255,255,0,0.2)','rgba(255,0,0,0.2)','rgba(128,0,128,0.2)'];
//1=meeting, 2=off, 3=out, 4=task, 5=shift
var obj = {
    agenda:'新規作成'
    ,time_start:'00:00'
    ,time_end:'00:00'
    ,todo:'...'
    ,file_paths:''
}
var content = new Vue({
  el: '#content',
  data: {
      detail:[obj]
  },
  computed: {

  }
});
var today = '<?=$today?>';
console.log(today);
var month = '<?=$month?>';

$.get('/Calendar/My/index/'+month +'/',{},function(){},"json")
.always(function(res){
    for (var i1 in res) {
        var arr = ['','','','','','','','','',''];
        var i3 = 0;
        for (var i2 in res[i1]) {
            
            var start_basic = 9;
            var time0 = new Date(i1 +" "+(start_basic+1)+":00:00");
            var time1 = new Date(i1 +" "+(start_basic+2)+":00:00");
            var time2 = new Date(i1 +" "+(start_basic+3)+":00:00");
            var time3 = new Date(i1 +" "+(start_basic+4)+":00:00");
            var time4 = new Date(i1 +" "+(start_basic+5)+":00:00");
            var time5 = new Date(i1 +" "+(start_basic+6)+":00:00");
            var time6 = new Date(i1 +" "+(start_basic+7)+":00:00");
            var time7 = new Date(i1 +" "+(start_basic+8)+":00:00");
            var time8 = new Date(i1 +" "+(start_basic+9)+":00:00");
            var time_start = new Date(i1 +" "+res[i1][i2]['time_start']);
            console.log(res[i1][i2]['time_start']);
            if(time_start < time0){
                i3 = 0;
            } else if(time_start < time1){
                i3 = 1;       
            } else if(time_start < time2){
                i3 = 2;
            } else if(time_start < time3){
                i3 = 3;
            } else if(time_start < time4){
                i3 = 4;
            } else if(time_start < time5){
                i3 = 5;
            } else if(time_start < time6){
                i3 = 6;
            } else if(time_start < time7){
                i3 = 7;
            } else if(time_start < time8){
                i3 = 8;
            } else if(time_start > time8){
                i3 = 9;
            }
            while(i3 < 10){
                if(i3 == 9 && arr[i3]){
                    var i4 = 9;
                    while(arr[i4]){
                        i4--;
                    }
                    while(i4 < 10){
                        arr[i4] = arr[i4+1];
                        i4++;
                    }
                }
                if(!arr[i3]){
                    arr[i3] = res[i1][i2]['tag'];
                    i3 = 10;
                }
                i3++;
            }
            for (var i5 in arr) {
                $('.d-'+i1+'-'+i5).css({'background-color': tag_color[arr[i5]]});
            }
        }
    }
    $('td').click(function(){
        showDetail(res,$(this).attr('date'));
        content.detail = res[$(this).attr('date')];
    });
//    console.log(month + '' + today);
    showDetail(res,today);
});

function showDetail(d,date){
    console.log(d[date]);
    if(d[date]){
        content.detail = d[date];
    }

}
//$(function(){ ga('send', 'pageview'); });
</script>
</body>
</html>
