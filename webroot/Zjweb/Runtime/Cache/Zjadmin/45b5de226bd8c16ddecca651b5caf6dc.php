<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>

    <link rel="stylesheet" type="text/css" href="/Public/Zjadmin/css/styles.css">
</head>
<body>
<form action="<?php echo U('login');?>" method="post" class="login-form">
    <article class="htmleaf-container">
        <div class="panel-lite">
            <div class="form-group">
                <input  class="form-control"  name="username" style="color:#3b72ab; outline:none; "value="请输入用户名" onfocus="this.value=''" />

            </div>
            <div class="form-group">
                <input type="password"  name="password" class="form-control" style="color:#3b72ab; outline:none;" value="请输入密码" onfocus="this.value=''"/>

            </div>
        </div>
    </article>
    <div class="panel-buttom">
        <img src="/Public/Zjadmin/images/logo.png" class="floating-logo">
        <button class="login-btn" type="submit">
        </button>
        <div class="check-tips"></div>
    </div>

</form>
<!--[if lt IE 9]>
<script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
<!--<![endif]-->
<script type="text/javascript">
    /* 登陆表单获取焦点变色 */
    $(".login-form").on("focus", "input", function(){
        $(this).closest('.item').addClass('focus');
    }).on("blur","input",function(){
        $(this).closest('.item').removeClass('focus');
    });

    //表单提交
    $(document)
            .ajaxStart(function(){
                $("button:submit").addClass("log-in").attr("disabled", true);
            })
            .ajaxStop(function(){
                $("button:submit").removeClass("log-in").attr("disabled", false);
            });

    $("form").submit(function(){
        var self = $(this);
        $.post(self.attr("action"), self.serialize(), success, "json");
        return false;

        function success(data){

            if(data.status){
                window.location.href = data.url;
            } else {
                self.find(".check-tips").text(data.info);
                //刷新验证码
                $(".reloadverify").click();
            }
        }
    });

    $(function(){
        //初始化选中用户名输入框
        $("#itemBox").find("input[name=username]").focus();
        //刷新验证码
        var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });

        //placeholder兼容性
        //如果支持
        function isPlaceholer(){
            var input = document.createElement('input');
            return "placeholder" in input;
        }
        //如果不支持
        if(!isPlaceholer()){
            $(".placeholder_copy").css({
                display:'block'
            })
            $("#itemBox input").keydown(function(){
                $(this).parents(".item").next(".placeholder_copy").css({
                    display:'none'
                })
            })
            $("#itemBox input").blur(function(){
                if($(this).val()==""){
                    $(this).parents(".item").next(".placeholder_copy").css({
                        display:'block'
                    })
                }
            })


        }
    });
</script>
</body>
</html>