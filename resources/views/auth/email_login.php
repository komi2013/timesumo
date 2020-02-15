<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>login</title>
    <meta name="google-site-verification" content="" />

    <link rel="shortcut icon" href="" />

    <script src="/plugin/jquery-3.4.0.min.js"></script>
    <script src="/plugin/jquery.cookie.js"></script>
    <script src="/plugin/vue.min.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-57298122-3"></script>
    <script src="/js/analytics.js<?=config('my.cache_v')?>"></script>
    <link rel="stylesheet" href="/css/basic.css<?=config('my.cache_v')?>">
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
    <input type="text" placeholder="email" id="email" v-model="email" v-on:change="checkEmail" class="column1"><br>
    <div v-if="email_error" style="color: red;"><?=__('email_login.mailError')?></div>
    <input type="password" placeholder="password" id="password" class="column1" v-model="password" v-on:change="checkPassword"><br>
    <div v-if="password_error1" style="color: red;"><?=__('email_login.passwordError1')?></div>
    <div v-if="password_error2" style="color: red;"><?=__('email_login.passwordError2')?></div>
    <input type="submit" value="login" class="column1" v-on:click="login('login')"><br>
    <div v-if="password_error4" style="color: red;"><?=__('email_login.passwordError4')?></div>
    <input type="submit" value="register" class="column1" v-on:click="login('reg')"><br>
    <input type="submit" value="reissue passowrd" class="column1" v-on:click="login('reg')"><br>
    <div v-if="sent" style="color: green;"><?=__('email_login.sent')?></div>
</div>

</div>
<div id="ad_right"><iframe src="/htm/ad_right/" width="160" height="600" frameborder="0" scrolling="no"></iframe></div>

<script>

const app = new Vue({
  el: '#content',
  data: {
    email: null,
    email_error: false,
    password: null,
    password_error1: false,
    password_error2: false,
    password_error3: false,
    password_error4: false,
    sent: false,
  },
  methods: {
    checkEmail: function (e) {
      if (MailCheck(this.email)) {
        this.email_error = false;
      }else{
        this.email_error = true;
      }
      e.preventDefault();
    },
    checkPassword: function (e) {
      if (this.password.length < 8 || this.password.length > 20) {
        this.password_error1 = true;
      }else{
        this.password_error1 = false;
      }
      if (this.password.search(/\d/) == -1 || this.password.search(/[a-zA-Z]/) == -1) {
        this.password_error2 = true;
      }else{
        this.password_error2 = false;
      }
      e.preventDefault();
    },
    login: function (event) {
        if(this.email_error || this.password_error1 || this.password_error2 ){
            return;
        }
        var param = {
            _token : $('[name="csrf-token"]').attr('content')
            ,email : this.email
            ,password : this.password
        }
        if(event === 'login'){
            $.post('/Auth/EmailCheck/',param,function(){},"json")
            .always(function(res){
                if(res[0] === 1 && res[1]){
                    location.href = res[2];
                }else if(res[0] === 1 && !res[1]){
                    app.password_error4 = true;
                }else{
                    alert('system error');
                }
            });
        }else{
            $.post('/Auth/EmailSend/',param,function(){},"json")
            .always(function(res){
                console.log(res);
                if(res[0] == 1){
                    app.sent = true;
                }else{
                    alert('system error');
                }
            });
        }
    },
  }
});

function MailCheck( mail ) {
    var mail_regex1 = new RegExp( '(?:[-!#-\'*+/-9=?A-Z^-~]+\.?(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*|"(?:[!#-\[\]-~]|\\\\[\x09 -~])*")@[-!#-\'*+/-9=?A-Z^-~]+(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*' );
    var mail_regex2 = new RegExp( '^[^\@]+\@[^\@]+$' );
    if( mail.match( mail_regex1 ) && mail.match( mail_regex2 ) ) {
        // 全角チェック
        if( mail.match( /[^a-zA-Z0-9\!\"\#\$\%\&\'\(\)\=\~\|\-\^\\\@\[\;\:\]\,\.\/\\\<\>\?\_\`\{\+\*\} ]/ ) ) { return false; }
        // 末尾TLDチェック（〜.co,jpなどの末尾ミスチェック用）
        if( !mail.match( /\.[a-z]+$/ ) ) { return false; }
        return true;
    } else {
        return false;
    }
}

setTimeout(function(){ga('send', 'pageview')},2000);

</script>
</body>
</html>