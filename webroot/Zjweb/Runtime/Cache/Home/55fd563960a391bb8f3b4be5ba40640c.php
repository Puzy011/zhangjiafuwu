<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/Public/Home/css/details.css" />
 <link rel="stylesheet" href="/Public/Home/css/rocketTop.css">
     	<link rel="stylesheet" href="/Public/Home/css/sass-compiled.css">
        <link rel="stylesheet" href="/Public/Home/css/Message.css">
		<!--js-->
		<script type="text/javascript" src="/Public/Home/js/jquery-1.js"></script>
       <script src="/Public/Home/js/rocketTop.js"></script>
       <script src="/Public/Home/js/tab.js"></script>
<title>帝安漾</title>
</head>

<body>
		<div class="nav-height" id="navHeight">
							<nav class="nav-wrap" id="nav-wrap">	  
				    <ul class="clearfix">
                    <li><a href="<?php echo U('Index/index');?>"><img class="logoimg" src="/Public/Home/images/logo.png"></a></li>
				      <li><a class="" href="<?php echo U('Index/index');?>">首页</a></li>
                      <li><a class="active" href="<?php echo U('Message/index');?>">资讯</a></li>
				      <li><a class="" href="<?php echo U('ProService/index');?>">产品服务</a></li>
				      <li><a class="" href="<?php echo U('AboutUs/index');?>">关于我们</a></li>
                      <li><a class="" href="<?php echo U('Investment/index');?>">招商加盟</a></li>
                      <li><a href="https://www.zhangjiamenhu.com/admin.php?s=/Public/logout.html"><img class="logoimg_img" src="/Public/Home/images/icon_login.png"></a></li>
				    </ul>
			 	</nav>
		</div>
   		<div class="topbox">
        	<!--<div class="topbox_top">
            
            </div>-->
            <img src="/Public/Home/images/picture_banner1.jpg">
        </div>
<div class="details">
	<h1><?php echo ($data["title"]); ?></h1>
    <img  src="<?php echo ($data["pic"]); ?>">
     <span class="content"><?php echo ($data["content"]); ?></span>
     <span class="details_time"><?php echo (date("Y-m-d",$data["time"])); ?></span>
</div>

		
	 <!DOCTYPE html>
<head>

</head>
	<body style="font-family:微软雅黑;">
	 	<div id="section4" class="section-content">
		  	
            <img style="width:100%;" src="/Public/Home/images/contract-us.jpg">
	 	</div>

</body></html>
	 	<!--内容信息导航吸顶及锚点引入JS-->
	<div id="shape">
		<div class="shapeColor">
			<div class="shapeFly">
			</div>
		</div>
	</div>
</body>
</html>