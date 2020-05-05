window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-57298122-1');
var drawerIsOpen = false;
$('#menu_td').click(function(){
  if(drawerIsOpen){
    $('#drawer').css({'left':'-100%'});
    drawerIsOpen = false;
  }else{
    $('#drawer').css({'left': '-1px','top':$(window).scrollTop()+51+'px'});
//    $('#ad_menu').empty().append(ad_menu_iframe);
    drawerIsOpen = true;
  }
});
  