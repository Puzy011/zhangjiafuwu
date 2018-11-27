<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0048)http://www.jq22.com/demo/jQuery-nav-top20160629/ -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title>蒂安漾</title>
        <!--css-->
        <link rel="stylesheet" href="/Public/Home/css/rocketTop.css">
     	<link rel="stylesheet" href="/Public/Home/css/sass-compiled.css">
        <link rel="stylesheet" href="/Public/Home/css/Message.css">
		<!--js-->
		<script type="text/javascript" src="/Public/Home/js/jquery-1.js"></script>
       <script src="/Public/Home/js/rocketTop.js"></script>
       <script src="/Public/Home/js/tab.js"></script>
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
            <img src="/Public/Home/images/picture_banner1.jpg">
        </div>
	 	<div id="section1" class="section-content">
        <div class="message">
         	<div class="recommend_all">
              <?php if(!empty($click)): if(is_array($click)): $i = 0; $__LIST__ = $click;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cl): $mod = ($i % 2 );++$i;?><div class="recommend">
               <a href="<?php echo U('Message/details?id='.$cl['id']);?>">	
               <div class="recommend_one">
                	<img class="recommend_img_one" src="<?php echo ($cl["pic"]); ?>">
                   <img class="recommend_img_two" src="/Public/Home/images/icon_recommend.png">
                    <span><?php echo ($cl["title"]); ?></span>
                </div>
                </a>
               </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
           </div>
           
           
        	<div class="message_us">
                <div class="tab">
				<div class="tab_menu">
					<ul>
						<li><a href="<?php echo U('Message/index?sortid=1');?>">衣</a></li>
						<li><a href="<?php echo U('Message/index?sortid=2');?>">食</a></li>
						<li><a href="<?php echo U('Message/index?sortid=3');?>">住</a></li>
                        <li><a href="<?php echo U('Message/index?sortid=4');?>">行</a></li>
					</ul>
				</div>
				<div class="tab_box">
					<div>
             
          <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="clothing">
                        	<img  src="<?php echo ($vo["pic"]); ?>">
                            <div class="clothing_span">
                            	<h2><?php echo ($vo["title"]); ?></h2>
                                <span><?php echo ($vo["desc"]); ?></span>
                            </div>
                            <div class="clothimg_all_time">
                            	<span class="clothimg_time"><?php echo (date("Y-m-d",$vo["time"])); ?></span>
                                <span class="clothimg_look"><a href="<?php echo U('Message/details?id='.$vo['id']);?>">全文</a></span>
                            </div>
                        </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
         
					</div>
		
		
           
				</div>
			</div>
		</div>
        	
               
            </div>
            <div class="cr"></div>
            </div>
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

</body></html>