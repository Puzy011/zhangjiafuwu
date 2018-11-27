<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">

table{
	font-size:48px;
	border:1px solid #101010;
}
tr{
	border:1px solid #101010;
}
td{
	border:1px solid #101010;
}
th{
	border:1px solid #101010;
}
.page{
	font-size:58px;
}
.form-item{
	font-size:48px;
}
select{
	width:360px;
	height:80px;
	font-size:48px;
}
option{
	font-size:48px;
}
input{
	font-size:48px;
}
button{
	font-size:48px;
	
}
a{
	
	text-decoration:none;
}
</style>

<body>
	<!-- 标题栏 -->
	 <form action="<?php echo U();?>" method="post" class="form-horizontal">
	  <div class="form-item">
	  			<div>
                <select name="type">
				   <option value="0">请选择服务类型</option>
                    <?php if(is_array($servicelist)): $i = 0; $__LIST__ = $servicelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$service): $mod = ($i % 2 );++$i;?><option value="<?php echo ($service["id"]); ?>" ><?php echo ($service["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
                <select name="residential_id">
                <option value="0">请选择小区</option>
					<?php if(is_array($residentiallist)): $i = 0; $__LIST__ = $residentiallist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$residential): $mod = ($i % 2 );++$i;?><option value="<?php echo ($residential["id"]); ?>"><?php echo ($residential["residential_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
				 <select name="status">
                	<option value="-4">状态</option>
                	<option value="0">已接单</option>
					<option value="1">进行中</option>
					<option value="2">收件中</option>
					<option value="3">寄件中</option>
					<option value="-1">已完成</option>
				</select>
				</div>
				
				<div class="form-item">
				<label>订单编号</label>
				<input type="text" name="order_number" value="">
				<button class="btn" type="submit" target-form="form-horizontal">查询</button>
        </div>
        </form>
    <!-- 数据列表 -->
    <div>
	<table>
    <thead>
        <tr>
		<th>类型</th>
		<th>业主姓名</th>
		<th>小区</th>
		<th>详细地址</th>
		<th>手机号码</th>
		<th>备注</th>
		<th>订单编号</th>
		<th>金额</th>
		<th>操作</th>
		</tr>
    </thead>
    <tbody>
		<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td><?php echo ($vo["title"]); ?></td>
			<td><?php echo ($vo["name"]); ?></td>
			<td><?php echo ($vo["residential_name"]); ?></td>
			<td><?php echo ($vo["address"]); ?></td>
			<td><?php echo ($vo["phone"]); ?></td>
			<td><?php echo ($vo["describe"]); ?> </td>
			<td><?php echo ($vo["order_number"]); ?> </td>
			<td><?php echo ($vo["price"]); ?></td>
			<td><?php if(($vo["status"]) == "0"): ?><a href="<?php echo U('ServiceOrder/phoneChangeStatus?id='.$vo['id']);?>" class="confirm"  style="color:#74d833;">已接单</a><?php endif; ?>
				<?php if(($vo["status"]) == "2"): ?><a href="<?php echo U('ServiceOrder/phoneChangeStatus?id='.$vo['id']);?>" class="confirm" style="color:#0000E3;">收件中</a><?php endif; ?>
				<?php if(($vo["status"]) == "3"): ?><a href="<?php echo U('ServiceOrder/phoneChangeStatus?id='.$vo['id']);?>" class="confirm" style="color:#0000E3;">寄件中</a><?php endif; ?>
				<?php if(($vo["status"]) == "1"): ?><a href="<?php echo U('ServiceOrder/phoneChangeStatus?id='.$vo['id']);?>" class="confirm" style="color:#0000E3;">进行中</a><?php endif; ?>
				<?php if(($vo["status"]) == "-1"): ?><span style="color:#FF0000;">已完成</span><?php endif; ?>
				</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
		<td colspan="9" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
	</tbody>
    </table>
	</div>
    <div class="page">
        <?php echo ($_page); ?>
         <a class="btn" href="<?php echo U('Index/index');?>">返回首页</a>
    </div>
</body>