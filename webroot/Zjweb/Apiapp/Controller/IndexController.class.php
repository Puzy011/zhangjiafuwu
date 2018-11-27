<?php
namespace Apiapp\Controller;
use Think\Upload;

class IndexController {

	
	//首页列表
    public function index(){
        //获取二维码
        vendor("Qrcode.phpqrcode");
        $data ="1111";
        $level = 'L';
        $size =5;
        $url = "D:/zhangjiawc/zjphp/temp/11.png";
        $QRcode = new \QRcode();

        $png = $QRcode->png($data,$url,$level,$size,2);
        //$png = $QRcode->png($data,false,$level,$size,2);
        echo $png;
       //echo "<img src=\"".$png ."\" >";

	}

	//上传图片
    public function upload(){

        //var_dump($_POST);die();
        //var_dump($_FILES);die();
        $picconfig = C ( 'PICTURE_UPLOAD' );
        $Upload = new Upload ( $picconfig );

        $info = $Upload->upload ( $_FILES );
        dump($info);die();

    }

    //客服消息记录
    public function irc_service(){
        $signture = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];

        $token = "leilong123";
        $tmpArr = array($token,$timestamp,$nonce);
        sort($tmpArr,SORT_STRING);
        $tmpArr = implode($tmpArr);
        $tmpArr = sha1($tmpArr);

        if($tmpArr == $signture){
            echo $echostr;
            return true;
        }else{
            echo "false";
            return false;
        }
    }

}



?>