<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>book</title>
    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>" />
    <meta name="csrf-token" content="<?=csrf_token()?>" />
    
  </head>
<body>



    <style>
        body {
            width:1180px;
        }
        #drawer {
          position : absolute;
          float : left;
          margin-top : -1px;
          width : 300px;   
          background-color: white;
        }
        #content{
            margin: 0px 0px 0px 310px;
            width: 700px;
            float:left;
        }
        #ad_right{
            margin: 0px 0px 0px 10px;
            width: 160px;
            float:left;
        }
        table {
            border-collapse: collapse;
        }
        table td {
            border-width: 0px;
        }
        .min10 {
            width: 100px;
            height: 30px;
            border-right-style: solid;
            border-width: thin;
            line-height: 12px;
            margin-left: -2px;
        }
        .hour {
            border-bottom-style: dotted;
            border-bottom-color: silver;
        }
        .closeTime {
            border-bottom-style: solid;
        }
        .day {
            border-bottom-style: solid;
            border-right-style: solid;
            border-width: thin;
        }
        .unavailable {
            background: gray;
        }
        .showConfirm {
            position:fixed;
            background-color:silver;
            padding:30px;
            width:220px;
            height:220px;
            z-index: 2;
        }
    </style>
<table id="drawer">
  <tr><td id="ad_menu"><iframe src="/htm/ad_menu/" width="300" height="250" frameborder="0" scrolling="no"></iframe></td></tr>
</table>
<div id="content">
<p id="menu_name"><?=$menu->menu_name?></p>
<input type="text" value="<?=$customer?>" placeholder="<?=__('hair_salon.customer')?>" id="customer">
<table>
    <?php  foreach ($days21 as $date => $d) {?>
        <?php $u = strtotime($date);?>
        <?php if(date('D',$u) == 'Sun' && date('H:i',$u) == $openTime){?> <tr> <?php }?>
            <?php if(date('H:i',$u ) == $openTime){?>  
            <td border="0"> 
            <div class="day"><?=date('m/d',$u)?> <?=__('hair_salon.day'.date('w',$u))?></div>                
            <?php }?>
            <?php if($d['available']) {?> <a href="/HairSalon/Booking/<?=$menu_id?>/<?=$u?>/"> <?php } ?>
            <div date="<?=date('m/d',$u)?> <?=__('hair_salon.day'.date('w',$u))?>"
                 unix="<?=$u?>"
                 start="<?=date('H:i',$u)?>" end="<?=date('H:i',($u + 60 * $end_minute))?>"
                class="min10 
                <?php if(date('H:i',$u ) == $closeTime){
                    echo 'closeTime';
                }else if(date('i',$u ) == '50'){
                    echo 'hour';
                } ?>
                <?=$d['available'] ? 'available' : 'unavailable'?>" 
                >
                
            </div>
            <?php if($d['available']) {?> </a> <?php } ?>
            <?php if(date('H:i',$u ) == $closeTime){?> </td> <?php }?>
        <?php if(date('D',$u) == 'Sat' && date('H:i',$u ) == $closeTime){?> </tr> <?php }?>
    <?php } ?>
</table>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

//let target = document.getElementById('content');
$('.min10').click(function(){
    var check = $('#menu_name').html()+"\r\n";
    check = check + $(this).attr('date')+"\r\n";
    check = check + $(this).attr('start');
    check = check + " ~ " +$(this).attr('end')+"\r\n";
    check = check + $('#customer').val();
    var r = confirm(check);
    if (r == true) {
        console.log(check);
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,unix : $(this).attr('unix')
            ,menu_id : <?=$menu_id?>
            ,customer : $('#customer').val()
            ,staff : <?=$staff?>
            
        }
        $.post('/HairSalon/BookUpdate/',param,function(){},"json")
        .always(function(res){
            if(res[0] == 1){
    //            location.href = '/HairSalon/Cancel/index/';
            }else{
                alert('system error');
            }
        });
    }

});
</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>
</body>
</html>
