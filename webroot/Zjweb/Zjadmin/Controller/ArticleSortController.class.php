<?php
namespace Zjadmin\Controller;

/**
 * 后台分类管理控制器
 */
class ArticleSortController extends AdminController {

    /**
     * 分类管理列表
     */
    public function index(){

         $articleSort=M('ArticleSort');
        $count    = $articleSort->count();// 查询满足要求的总记录数
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $articleSort->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        $this->assign('tree', $list);
        $this->meta_title = '分类管理';
        $this->display();
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     */
    public function tree($tree = null){
        C('_SYS_GET_CATEGORY_TREE_') || $this->_empty();
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /* 编辑分类 */
    public function edit($id = null, $pid = 0){
        $Category = D('ArticleSort');

        if(IS_POST){ //提交表单
            if(false !== $Category->update()){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $Category->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
      
            /* 获取分类信息 */
            $info = $id ? $Category->info($id) : '';

            $this->assign('info',       $info);
            $this->assign('ArticleSort',   $cate);
            $this->meta_title = '编辑分类';
            $this->display();
        }
    }

    /* 新增分类 */
    public function add($pid = 0){
        $ArticleSort = D('ArticleSort');

        if(IS_POST){ //提交表单
		
            if(false !== $ArticleSort->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $ArticleSort->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = array();

            /* 获取分类信息 */
            $this->assign('ArticleSort', $cate);
            $this->meta_title = '新增分类';
            $this->display('edit');
        }
    }

    /**
     * 删除一个分类
     */
    public function remove(){
        $cate_id = I('id');
        if(empty($cate_id)){
            $this->error('参数错误!');
        }

        //删除该分类信息
        $res = M('ArticleSort')->delete($cate_id);
        if($res !== false){
            $this->success('删除分类成功！');
        }else{
            $this->error('删除分类失败！');
        }
    }

  
}

?>