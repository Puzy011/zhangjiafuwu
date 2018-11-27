<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/9
 * Time: 15:02
 */

namespace Home\Controller;

class MainController extends HomeController
{
    public function index(){
        $this->display("index");
    }
}