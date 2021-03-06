<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>  
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">   
<meta name="format-detection" content="telephone=no"/>
<title>登录</title>
<!--css-->
<link rel="stylesheet" href="/Public/Zjadmin/css/enter.css">
</head>

<body>

<div class="main">
	<div class="top_img"><img src="/Public/Zjadmin/images/logoz.png" /></div>
    <form action="<?php echo U('login');?>" method="post"  class="main_form">
    <div class="all_main">
    	<div class="user_name">
            <input  name="username" type="text" placeholder="请输入账号" />
            <img src="/Public/Zjadmin/images/zhanghao.png" />
        </div>
        <div class="user_password">
        	<input  name="password" type="password" placeholder="请输入密码" />
            <img src="/Public/Zjadmin/images/mima.png" />
        </div>
    </div>
    <button class="btn" type="submit">登录</button>
    <div style="color:#FF0000;" class="check-tips"></div>
    </form>
</div>
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