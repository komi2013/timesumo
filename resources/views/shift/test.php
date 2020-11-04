<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Test</title>
    <link rel="shortcut icon" href="" />
    <script src="/plugin/min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basic.css<?=config('my.cache_v')?>" />
    <link rel="stylesheet" href="/css/pc.css<?=config('my.cache_v')?>" media="only screen and (min-width : 711px)">
    <link rel="stylesheet" href="/css/sp.css<?=config('my.cache_v')?>" media="only screen and (max-width : 710px)">
    <meta name="viewport" content="width=device-width, user-scalable=no" >
    <meta name="csrf-token" content="<?=csrf_token()?>" />
  </head>
<style>
    input[type="button"] {
        width: 100px;
        height: 100px;
        padding: 10px;
    }
</style>
<body>

<input type="text" id="test">
<input type="submit" id="submit">
<script>


$('#submit').click(function(){
    var param = {
        _token : $('[name="csrf-token"]').attr('content')
        ,test : $('#test').val()
    }
    $.post('/Shift/Test/sessionSave/',param,function(){},"json")
    .always(function(res){
        console.log(res);
    });
});

</script>
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-1"></script>
<script defer src="/js/common.js<?=config('my.cache_v')?>"></script>

</body>
</html>
