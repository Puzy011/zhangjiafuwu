<?php
require './Zjweb/Apiapp/Common/function.php';


$xmlData = file_get_contents('php://input');
libxml_disable_entity_loader(true);
$data = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $verify_result = $data['return_code'];
        $price=$data['total_fee'];
        if ($verify_result == "SUCCESS") {//验证成功
            $type=$data['attach'];
            if($type=='掌家服务'){
                $order_number=$data['out_trade_no'];
                //连接数据库
                $conn = conn();
                //查询语句
                //$data['out_trade_no'] = "ZJ32017042797551011";
                $sql1 = "select id,price,status from zj_service_order where order_number = '".$order_number."'";
                $result1 = mysql_fetch_array(mysql_query($sql1,$conn));
                $prices=$result1['price'];
                $money=number_format($prices,2,".","")*100;
                if($money==$price){
                    $sql2 = "update zj_service_order set status = 0 where id = '".$result1['id']."'";
                    $result2 = mysql_query($sql2,$conn);
                    echo "SUCCESS";
                }else{
                    logResult('成功-3');
                    $sql2 = "update zj_service_order set status = -3 where id = '".$result1['id']."'";
                    $result2 = mysql_query($sql2,$conn);
                    echo "SUCCESS";
                }
                logResult('订单编号'.$order_number.'数据库金额：'.$prices.'处理后的数据库金额：'.$money.'传入金额：'.$price.'时间：'.time());
                mysql_close(); //关闭MySQL连接
            }
            if($type=='掌家缴费'){
                $order_number=$data['out_trade_no'];
                //连接数据库
                $conn = conn();
                //查询语句
                //$data['out_trade_no'] = "ZJ32017042797551011";
                $sql1 = "select paymoney,admin_id,member_id,time from zj_payment where order_number = '".$order_number."'";
                $result1 = mysql_fetch_array(mysql_query($sql1,$conn));
                $paymoney=$result1['paymoney'];
                $member = $result1['member_id'];
                $time = $result1['time'];
                
                $money=number_format($paymoney,2,".","")*100;
                if($money==$price){
                    $sql2 = "update zj_payment set status = 1 where order_number = '".$order_number."'";
                    $result2 = mysql_query($sql2,$conn);
                    $admin=$result1['admin_id'];
             
                 
                    $sqld2 = "update zj_payment_order set aprice = yprice , status = 1 , paymentime = '" .$time."'  where status = 0 and member_id = '".$member."'";
                    $resultd2 = mysql_query($sqld2,$conn);
                   
                    $sql3 = "select id,price from zj_admin where id = '".$admin."'";
                    $result3 = mysql_fetch_array(mysql_query($sql3,$conn));
                    
                    $yprice=$result3['price'];
                    $xaymoney=$paymoney+$yprice;
                    $sql4 = "update zj_admin set price = '".$xaymoney."' where id = '".$admin."'";
                    $result4 = mysql_query($sql4,$conn);
                    echo "SUCCESS";
                }else{
                    $sql2 = "update zj_payment set status = -3 where order_number = '".$order_number."'";
                    $result2 = mysql_query($sql2,$conn);
                    echo "SUCCESS";
                }
                logResult('订单编号'.$order_number.'数据库金额：'.$paymoney.'处理后的数据库金额：'.$money.'传入金额：'.$price.'时间：'.time());
                mysql_close(); //关闭MySQL连接
            }

        } else {
            logResult('支付失败');
            echo "FAIL";
        }

//连接数据库
function conn() {
    $mysql_server_name='localhost';
    $mysql_username='root';
    $mysql_password='10IDCcom';
    $mysql_database='zj';
    $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ; //连接数据库
    mysql_query("set names 'utf8'"); //数据库输出编码
    mysql_select_db($mysql_database); //打开数据库
    return $conn;
}



?>