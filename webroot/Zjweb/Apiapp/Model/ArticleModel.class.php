<?php

namespace Apiapp\Model;
use Think\Model;


/**
 * 文档基础模型
 */
class ArticleModel extends Model{

  
     /**
      * 资讯模型
   */
  public function index($where, $order = 'id DESC'){
      
        $data=M('Article')
        ->field('zj_article.id,title,content,author,zj_article.pic,desc,vote,up,down,zj_article.time')->limit($length=1)->where($where)->order($order)->select();
         return $data;
  }
  /**
   * 资讯首页
   */
  public function home($where,$length, $order = 'id DESC'){
       
      $data=M('Article')
      ->field('id,title,content,desc,keyword,click,pic,vote,up,down,time,author')->limit($length)->where($where)->order($order)->select();
      return $data;
  }
    /*
     * 根据ID查询资讯具体内容并更新点击量
     */
    public function info($id){
        $data=M('Article')->field('id,title,content,keyword,click,pic,time,author')->where("id = ".$id)->select();
        $click['click'] = $data[0]['click'] + 1;
        M('Article')->where("id = ".$id)->save($click);
        return $data;
    }
  
    //Member业主投票信息保存 
    public function vote_save($id,$voteclick){
             $data = M('Article')->where($id)->save($voteclick);
            return $data;
    }
    //获取openid账号对应的文章里面的小区id 同时也用于小程序显示
    public function article_select($id){
                 $data = M('Article')
                       ->field('id,vote,click,up,down,endtime')
                       ->where($id)
                       ->select();
                return $data;       
    }
 public function getUser($where){
     $data=M('Admin')->where($where)->field('username,pic')->select();
     return $data;
 }
    
}

?>