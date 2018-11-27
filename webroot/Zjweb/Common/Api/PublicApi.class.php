<?php

namespace Common\Api;
class PublicApi {

    //成员抽成
	/***
	orderid  订单ID
	adminid  操作者用户ID
	monuid   成员ID（抽成者ID）
	price    总金额
	isact    2 代练
	**/
    public static function AsMember($orderid,$adminid,$monuid,$price,$isact=1){
	       if ($isact==2){  //是否为代练
		     $dprice=round($price*0.95,2);  //抽成95%
		   }else{
	         $dprice=round($price*0.8,2);  //抽成80%
		   }
			 $memberdata['money1']=array("exp",'money1+'.$dprice);
			 $memberdata['ord_num']=array("exp",'ord_num+1');  //成员订单累计
			// $menret=M("member")->where("id=".$monuid)->setInc('money1',$dprice);
			 $menret= M('Member')->where('id='.$monuid)->data($memberdata)->save();
		     if ($menret){
			     $mlogdata['mid']=$monuid;
				 $mlogdata['orderid']=$orderid;
				 $mlogdata['money']=$dprice;
				 $mlogdata['adminid']=$adminid;
				 $mlogdata['time']=time();
				 $mlogret =M('MemberLog')->add($mlogdata);
				 
				 
			}
	    
	}
	
/*	//获取积分，经验
	//score  积分
	userid   用户
	content   说明
	gainid   获取原ID
	sorttype  积分类别
	type   操作类行 1，积分 ，2，经验*/
	
	public static function ScoreMember($score,$userid,$content,$gainid,$sorttype,$type){
	 
	          
			    //用户积分添加
				$scoredata['mid']=$userid;
				$scoredata['score']=$score;
				$scoredata['content']=$content;
				$scoredata['gainid']=$gainid;
				$scoredata['type']=$sorttype;
				$scoredata['sortid']=$type;
				$scoredata['sign_date']=time();
				$scoreres =M('member_sign')->add($scoredata);
				if ($type==1){
				  $members= M('Member')->where('id='.$userid)->setInc("score",$score);
				}
				if ($type==2){
			      $members= M('Member')->where('id='.$userid)->setInc("level",$score);
			    }
			 
			   
				
	 }
	 
  /*	消费积分，经验记录
	//score  积分
	userid   用户
	content   说明
	gainid   获取原ID
	sorttype  积分类别
	type   操作类行 1，积分 ，2，经验*/
	
	public static function ScoreMemberLog($score,$userid,$content,$gainid,$sorttype,$type){
	 
	          
			    //用户积分添加
				$scoredata['mid']=$userid;
				$scoredata['score']=$score;
				$scoredata['content']=$content;
				$scoredata['gainid']=$gainid;
				$scoredata['type']=$sorttype;
				$scoredata['sortid']=$type;
				$scoredata['sign_date']=time();
				$scoreres =M('member_sign')->add($scoredata);
				
			 
			   
				
	 }
  
    //结单刷新排行榜
    public static function  updateRank($userid,$price,$sortid){
  
				$field = "week_num,month_num,week_sum,month_sum,money_sum,charm_sum";
				
				$mlist = M('member_list')->where('mid ='.$userid." and sortid=".$sortid)->field($field)->find();
				
				$nowweek = (int)date('W');
				$nowmonth = (int)date('m');
				
				if(!$mlist){
					
					$mlistinfo['mid'] = $userid;
					$mlistinfo['week_num'] = $nowweek;
					$mlistinfo['month_num'] = $nowmonth;
					$mlistinfo['week_sum']  = $price;  //周总额
					$mlistinfo['month_sum'] = $price;  //月总额
					$mlistinfo['money_sum'] = $price;  //总额
					$mlistinfo['charm_sum'] = $price;  //魅力值
					$mlistinfo['sortid'] = $sortid;  //分类
					M('member_list')->add($mlistinfo);
				}else{
					if($mlist['week_num']!=$nowweek){
						$mlistinfo['week_sum']=$price;
					}else{
						$mlistinfo['week_sum']=$mlist['week_sum']+$price;
					}
					if($mlist['month_num']!=$nowmonth){
						$mlistinfo['month_sum']=$price;
					}else{
						$mlistinfo['month_sum']=$mlist['month_sum']+$price;
					}
					
					$mlistinfo['month_num']=$nowmonth;
					$mlistinfo['week_num']=$nowweek;
					$mlistinfo['money_sum'] = $mlist['money_sum']+$price;
					$mlistinfo['charm_sum'] = $mlist['charm_sum']+$price;
					
					M('member_list')->where('mid ='.$userid." and sortid=".$sortid)->save($mlistinfo);
				}
				
		
		}	
				
	 //更新成员热度
	 public function updateHot($mid){
	 
	 
	     	//热度= 【【总金额/10】 *【评分总数/5】】+【订单总小时*10】
			$techniquenum=M('member_comment')->where('uid ='.$mid)->sum('technique'); //总评
		    $attitudenum=M('member_comment')->where('uid ='.$mid)->sum('attitude'); //总评
		    $speednum=M('member_comment')->where('uid ='.$mid)->sum('speed'); //总评
			$count=M('member_comment')->where('uid ='.$mid)->count(); //总评
		    $pricenum=M('order')->where('uid ='.$mid.' and status = 3')->sum('price');  //订单总价格
		    $hournum=M('order')->where('uid ='.$mid.' and status = 3')->sum('hour');  //订单总小时数
            
		    $hot = round((($pricenum/10)*(($techniquenum+$attitudenum+$speednum)/(5*$count)))+$hournum*10); 
			
			$memdata["hot"]=$hot;
			$members= M('Member')->where('id='.$mid)->save($memdata);
	        
	 }
	 	 
	 	
}

?>