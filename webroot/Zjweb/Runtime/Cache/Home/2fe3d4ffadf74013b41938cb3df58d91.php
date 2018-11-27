<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="/Public/Home/css/details.css" />
 	<link rel="stylesheet" href="/Public/Home/css/rocketTop.css">
	<link rel="stylesheet" href="/Public/Home/css/sass-compiled.css">
	<link rel="stylesheet" href="/Public/Home/css/Message.css">
	<link rel="stylesheet" href="/Public/Home/css/top.css">
		<!--js-->
	<script type="text/javascript" src="/Public/Home/js/jquery-1.js"></script>
	<script src="/Public/Home/js/rocketTop.js"></script>
	<script src="/Public/Home/js/tab.js"></script>
	<title>掌住-掌家门户</title>
</head>

<body>
		<div class="nav-height" id="navHeight">
         <div class="logoimg"><a href="<?php echo U('Index/index');?>"><img  src="/Public/Home/images/logo.png"></a></div>
				<nav class="nav-wrap" id="nav-wrap">	  
				    <ul class="clearfix">
				     	 <li><a class="" href="<?php echo U('Index/index');?>">首页</a></li>
                     	<!-- <li><a class="" href="Message.html">资讯</a></li>-->
					  	<li><a class="" href="<?php echo U('ProService/index');?>">产品介绍</a></li>
					  	<li><a class="" href="<?php echo U('AboutUs/index');?>">关于我们</a></li>
						<li><a class="" href="<?php echo U('ZJstore/index');?>">掌Go便利店</a></li>
						<li><a class="active" href="<?php echo U('Investment/index');?>">招商加盟</a></li>
						<div class="right"><a href="https://www.zhangjiamenhu.com/admin.php?s=/Public/logout.html">物业入口</a></div>
						<div class="change">
							<span>中文 &nbsp&nbsp|&nbsp&nbsp</span>
							<a href="<?php echo U('Investment/index_en');?>">English</a>
						</div>
				    </ul>
			 	</nav>
		</div>
        <div class="construction">
        <img src="/Public/Home/images/pictures_1.png" />
        </div>
		<div id="section4" class="section-content">
			<img style="width:100%;" src="/Public/Home/images/contract-us.jpg">
		</div>
	 	<!--内容信息导航吸顶及锚点引入JS-->
	<div id="shape">
		<div class="shapeColor">
			<div class="shapeFly">
			</div>
		</div>
	</div>

</body>
</html>