<?php

	// JSON 转换
	
function jsonShow($code,$message="",$data=""){
  
    $jsonArray=array();
	
    
    if (!is_numeric($code)){
	
	    $jsonArray['code']=400;
	    $jsonArray['message']="参数有误！！";
		$jsonArray['time']=time();
	    $jsonArray['data']="";
	    return json_encode($jsonArray);
	    exit;
	   
	}
  
	$jsonArray['code']=$code;
	$jsonArray['message']=$message;
	$jsonArray['time']=time();
    $jsonArray['data']=$data;
	
	return  json_encode($jsonArray);
	
}

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word='') {
    $fp = fopen("log.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
	/**
	 * 手机短信验证
	 */
function getSmsbao($mcontent,$mphone){
    $smsapi = "https://api.smsbao.com/";
    $user = "xmday"; //短信平台帐号
    $pass = md5("day17819"); //短信平台密码
    $content=$mcontent;//要发送的短信内容
    $phone = $mphone;//要发送短信的手机号码
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    $result =file_get_contents($sendurl) ;
    return $result;
}
/**
 * 资讯内容处理
 */
function getConent($content){
    $_arr = preg_split('/(<img.*?>)/i', $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $_r = array();
    foreach($_arr as $_txt) {
        if(substr($_txt, 0, 4) == '<img') {
            $_matchs = array();
            preg_match('/<img.*?src="(.*?)"/i', $_txt, $_matchs);
            $_txt = $_matchs[1];
            if(preg_match('/^\//', $_txt)) $_txt = $gupload.$_txt;
            $_r[]= array('type'=>'img', 'data'=>$_txt);
        }else {
            $_txt = preg_replace('/&.*?;/', ' ', $_txt);
            $_txt = preg_replace('/\s+/', ' ', $_txt);
            $_txt = preg_replace(array('/<br.*?>/i', '/<p.*?>/i', '/<li.*?>/i', '/<div.*?>/i', '/<tr.*?>/i', '/<th.*?>/i'),
                "\n", $_txt);
            $_txt = preg_replace('/<.*?>/', '', $_txt);
            $_r[]= array('type'=>'txt', 'data'=>$_txt);
        }
    }
    return $_r;
}
?>