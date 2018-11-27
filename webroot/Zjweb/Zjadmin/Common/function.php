<?php
use Common\Api\ChatApi;
// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

// 获取数据的状态操作
function show_status_op($status) {
   if(!isset($status)){
        return false;
    }
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';		break;
		case -1  : return    '禁用';     break;
        default : return    false;      break;
    }
}

// 获取数据的处理状态操作
function show_status_do($status) {

    switch ($status){
        case 0  : return    '处理';     break;
        case 1  : return    '取消处理';     break;
        default : return    false;      break;
    }
}



/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '禁用';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审核';   break;
        default : return    false;      break;
    }
}

function get_status_order($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '已删除';   break;
        case 1  : return    '预约';     break;
        case 2  : return    '下单';     break;
        case 3  : return    '结单';   break;
		case 4  : return    '已付款';   break;
		case 5  : return    '未付款';   break;
        default : return    false;      break;
    }
}

/**
 * 获取当前文章文档的分类
 * @param int $id
 * @return array 文档类型数组
 */
function get_cate($cate_id = null){
    if(empty($cate_id)){
        return false;
    }
    $cate   =   M('ArticleSort')->where('id='.$cate_id)->getField('name');
    return $cate;
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
/**
 * 发送邮件
 * @param string $address
 * @param string $title
 * @param string $message
 * @return true or false
 */
function SendMail($address,$title,$message)
{
    Vendor('PHPMailer.PHPMailer','','.class.php');
    $mail=new PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    //设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet='UTF-8';
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body=$message;
    //设置邮件头的From字段。
    $mail->From=C('MAIL_ADDRESS');
    //设置发件人名字
    $mail->FromName='zwtest';
    //设置邮件标题
    $mail->Subject=$title;
    //设置SMTP服务器。
    $mail->Host=C('MAIL_SMTP');
    //设置为“需要验证”
    $mail->SMTPAuth=true;
    
    $mail->IsHTML(true);
    
    //设置用户名和密码。
    $mail->Username=C('MAIL_LOGINNAME');
    $mail->Password=C('MAIL_PASSWORD');
    //发送邮件。
    return($mail->Send());
}

/**
 一个用户向一个或多个用户发送系统消息

 */
 
 function mepush($mid,$message,$megid,$type,$userid,$nickname) {


	if ($userid){
		$fromUserId=$userid;
	}else{
		$fromUserId=616;
	}

	$toUserId[]=$mid;

	$objectName="RC:TxtMsg";
	$pushContent=$message;

	//附加信息
	$pudata['msgid']=$megid;
	$pudata['userid']=$userid;
	$pudata['nickname']=$nickname;
	$pudata['type']=$type;

	$contentdata['content']=$message;
	$contentdata['extra']=$pudata;
	$pushextra['payload']=$pudata;
	$pushextra['pushData']=$pudata;

	$content=json_encode($contentdata);
	$pushData=json_encode($pushextra);
	//调用API接口
	$pchat = new ChatApi();
	//测试
	//$pchat = new ChatApi();
	$ret = $pchat->messageSystemPublish($fromUserId,$toUserId,$objectName,$content,$pushContent,$pushData);

}

//推送服务 推送 方法
/*
 $mid 推送用户ID
$message 推送内容
$megid 推送源ID
$userid;   推送用户ID(评论)
$nickname;  推送用户昵称（评论）
$type    推送源 1， 为订单推送 2，为评论推送 3，订单结单推送
*/
 function mepush_push($mid,$message,$megid,$type,$userid,$nickname){

	//附加信息
	$pudata['msgid']=$megid;
	$pudata['userid']=$userid;   //推送用户ID
	$pudata['nickname']=$nickname;  //推送用户昵称
	$pudata['type']=$type;

	$platform[]='ios';
	$platform[]='android';
	$audience['userid'][]=$mid;
	$audience['is_to_all']=false;
	$notification["alert"]=$message;
	$notification["android"]["extras"]=$pudata;
	$notification["ios"]["extras"]=$pudata;

	//$pchat = new ChatApi();
	$pchat = new ChatApi();
	$ret = $pchat->push($platform,$audience,$notification);

}
/**
 * 根据小区ID获取小区名称
 */
 function get_residential($rid){
     $where['id']=$rid;
     return $residential_name=M('residential')->where($where)->getField('residential_name');
 }
 function getRegion($code){
     $where['code']=$code;
     $data=M('Region')->where($where)->getField('name');
     return $data;
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
  * 获取用户头像
  */
function getAdinPic(){
    $user=session('admin_auth.id');
    $where['id']=$user;
    $admin=M('Admin')->where($where)->getField('pic');
    return $admin;
}
?>