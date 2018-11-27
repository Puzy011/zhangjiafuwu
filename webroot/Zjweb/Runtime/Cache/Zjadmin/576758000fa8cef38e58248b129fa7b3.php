<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>  
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">   
<meta name="format-detection" content="telephone=no"/>
<title>首页</title>
<!--css-->
<link rel="stylesheet" href="/Public/Zjadmin/css/home.css">
</head>

<body>
<div class="main">
    <div class="main_top">
        <div class="user">用户名：<?php echo ($data["name"]); ?></div>
        <div class="user_right">
        	<span>姓名：<?php echo ($data["nickname"]); ?></span>
            <span>工号：<?php echo ($data["name"]); ?></span>
            <span>业务提成：<b class="num">￥<?php echo ($data["prices"]); ?></b></span>
        </div>
    </div>
    <div class="main_center">
    	<div class="center_one">
        	<span><a class="btn" href="<?php echo U('ServiceOrder/breakfast');?>">掌家早餐</a><b></b></span>
            <span><a class="btn" href="<?php echo U('ServiceOrder/courier');?>">掌家快递</a><b></b></span>
        </div>
        <div class="center_one">
        	<span><a class="btn" href="<?php echo U('ServiceOrder/washcar');?>">夜间洗车</a><b></b></span>
            <span>车内清洁<b></b></span>
        </div>
        <div class="center_one">
        	<span><a class="btn" href="<?php echo U('ServiceOrder/theday');?>">当日达</a><b></b></span>
            <span><a class="btn" href="<?php echo U('Maintenance/phoneMain');?>">障碍报修</a><b></b></span>
        </div>
    </div>
    <div class="footer">
    	<a href="<?php echo U('User/updatePhonePassword');?>"><button class="password">修改密码</button></a>
        <a href="<?php echo U('Public/logout');?>"><button class="out">退出</button></a>
    </div>
</div>
</body>
</html>