<?php

namespace Zjadmin\Controller;
use Zjadmin\Model\AuthGroupModel;
use Think\Page;
use Think\Upload;


/**
 * 后台内容控制器

 */
class ArticleController extends AdminController {

	/* 保存允许访问的公共方法 */
	//static protected $allow = array( 'draftbox','Article');

   // private $cate_id        =   null; //文档分类id

    /**
     * 检测需要动态判断的文档类目有关的权限
     *
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
	 
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        $cates = AuthGroupModel::getAuthCategories(UID);
        switch(strtolower(ACTION_NAME)){
            case 'index':   //文档列表
                $cate_id =  I('cate_id');
                break;
            case 'edit':    //编辑
            case 'update':  //更新
                $doc_id  =  I('id');
                $cate_id =  M('Document')->where(array('id'=>$doc_id))->getField('category_id');
                break;
            case 'setstatus': //更改状态
            case 'permit':    //回收站
                $doc_id  =  (array)I('ids');
                $cate_id =  M('Document')->where(array('id'=>array('in',$doc_id)))->getField('category_id',true);
                $cate_id =  array_unique($cate_id);
                break;
        }
        if(!$cate_id){
            return null;//不明,需checkRule
        }elseif( !is_array($cate_id) && in_array($cate_id,$cates) ) {
            return true;//有权限
        }elseif( is_array($cate_id) && $cate_id==array_intersect($cate_id,$cates) ){
            return true;//有权限
        }else{
            return false;//无权限
        }
        return null;//不明,需checkRule
    }

    /**
     * 文章列表页
     */
	  public function index(){
	    //获取左边菜单
        // $this->getMenu();
         $nickname = I('nickname');
		 if($nickname){
		     if(is_numeric($nickname)){
                 $map['id|title']=   array('like','%'.$nickname.'%');
             }else{
                 $map['title']  =  array('like', '%'.(string)$nickname.'%');
             }
		 }
		 $article = M('Article');
		 if (IS_ROOT) {
		 $count    = $article->count();// 查询满足要求的总记录数
		 $page=getPage($count);
		 $list  = $article->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
		 }else{
		     $author=session('admin_auth.username');
		     $setrid['author']=$author;
		     $count=M('Article')->where($setrid)->count();
		     $page=getPage($count);
		     $list=D('Article')->search($setrid,$page);
		 }
		 foreach ($list as $k => $v){
		     $list[$k]['time'] = date("Y-m-d H:i:s",$v['time']);
         }
		 $show = $page->show();// 分页显示输出
		 $this->assign('_page',$show);// 赋值分页输出
         $this->assign('list',   $list);
         $this->meta_title = '文章列表管理';
         $this->display();
	  }
	 
  
    /**
     * 默认文档回复列表方法
     * @param $cate_id 分类id
     */
    protected function indexOfReply($cate_id) {
        /* 查询条件初始化 */
        $map = array();
        if(isset($_GET['content'])){
            $map['content']  = array('like', '%'.(string)I('content').'%');
        }
        if(isset($_GET['status'])){
            $map['status'] = I('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( !isset($_GET['pid']) ) {
            $map['pid']    = 0;
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if ( isset($_GET['username']) ) {
            $map['uid'] = M('UcenterMember')->where(array('username'=>I('username')))->getField('id');
        }

        // 构建列表数据
        $Document = M('Document');
        $map['category_id'] =   $cate_id;
        $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        }

        $prefix   = C('DB_PREFIX');
        $l_table  = $prefix.('document');
        $r_table  = $prefix.('document_article');
        $list     = M() ->table( $l_table.' l' )
                       ->where( $map )
                       ->order( 'l.id DESC')
                       ->join ( $r_table.' r ON l.id=r.id' );
        $_REQUEST = array();
        $list = $this->lists($list,null,null,null,'l.id id,l.pid pid,l.category_id,l.title title,l.update_time update_time,l.uid uid,l.status status,r.content content' );
        int_to_string($list);

        if($map['pid']){
            // 获取上级文档
            $article    =   $Document->field('id,title,type')->find($map['pid']);
            $this->assign('article',$article);
        }
        //检查该分类是否允许发布内容
        $allow_publish  =   get_category($cate_id, 'allow_publish');

        $this->assign('status', $status);
        $this->assign('list',   $list);
        $this->assign('allow',  $allow_publish);
        $this->assign('pid',    $map['pid']);
        $this->meta_title = '子文档列表';
        return 'reply';//默认回复列表模板
    }
    /**
     * 默认文档列表方法
     * @param $cate_id 分类id
     */
    protected function indexOfArticle($cate_id){
        /* 查询条件初始化 */
        $map = array();
        if(isset($_GET['title'])){
            $map['title']  = array('like', '%'.(string)I('title').'%');
        }
        if(isset($_GET['status'])){
            $map['status'] = I('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( !isset($_GET['pid']) ) {
            $map['pid']    = 0;
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if ( isset($_GET['nickname']) ) {
            $map['uid'] = M('Member')->where(array('nickname'=>I('nickname')))->getField('uid');
        }

        // 构建列表数据
        $Document = M('Document');
        $map['category_id'] =   $cate_id;
        $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        }

        $list = $this->lists($Document,$map,'level DESC,id DESC');
        int_to_string($list);
        if($map['pid']){
            // 获取上级文档
            $article    =   $Document->field('id,title,type')->find($map['pid']);
            $this->assign('article',$article);
        }
        //检查该分类是否允许发布内容
        $allow_publish  =   get_category($cate_id, 'allow_publish');

        $this->assign('status', $status);
        $this->assign('list',   $list);
        $this->assign('allow',  $allow_publish);
        $this->assign('pid',    $map['pid']);

        $this->meta_title = '文档列表';
        return 'index';
    }

    /**
     * 设置一条或者多条数据的状态
 
     */
    public function setStatus($model='Document'){
        return parent::setStatus('Document');
    }

    /**
     * 文档新增页面初始化

     */
    public function add(){
        $region = M('region');
        $condition['parentCode'] = 100000;
        $sheng = $region->where($condition)->select();
		$sortlist = M('ArticleSort')->order('id')->select();
		$this->assign('sortlist', $sortlist);
		$this->assign('sheng', $sheng);
		$residential=getRlist();
		$this->assign('residential',   $residential);
        $this->meta_title = '新增文章';
        $this->display();
    }
    /**
     * 公告新增页面初始化
    
     */
    public function notice(){
        $user=session('admin_auth');
        $where['id']=$user['id'];
        $admin=D('Admin')->getAdmin($where);
        $rid=$admin[0]['rid'];
        $this->assign('rid',   $rid);
        $this->meta_title = '编辑公告';
        $this->display();
    }
    /**
     * 更新一条数据
     */

    public function updateNotice(){
        $res = D('Article')->update();

        if(!$res){
            $this->error(D('Article')->getError());
        }else{
            $this->success($res['id']?'更新成功':'编辑成功',U('Index/index'));
        }
    }

    /**
     * 文档编辑页面初始化
     */
    public function edit(){
        //获取左边菜单
       // $this->getMenu();


        $id     =   I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        /*获取一条记录的详细数据*/
        $Article = D('Article');
        $data = $Article->detail($id);
        if(!$data){
            $this->error($Article->getError());
        }
        $region = M('region');
        $condition['parentCode'] = 100000;
        $sheng = $region->where($condition)->select();
        $str=$data['proid'];
        $code=substr($str,0,2);
        $wherecity['type']=2;
        $wherecity['code'] =array('like', (string)$code.'%');
        $city = $region->where($wherecity)->select();
        
        $this->assign('sheng', $sheng);
        $this->assign('city', $city);
        $residential=getRlist();
        $this->assign('residential',   $residential);
        $address = M('Article')->select();
        $this->assign('address', $address);
		$sortlist = M('ArticleSort')->order('id')->select();
		
		$this->assign('sortlist', $sortlist);
        $this->assign('data', $data);
        



        //获取当前分类的文档类型
     //   $this->assign('type_list', get_type_bycate($data['category_id']));

        $this->meta_title   =   '编辑文章';
        $this->display();
    }
  /**
     * 文档编辑页面初始化
    
     */
    public function adel(){
	 
	    $id     =   I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }
		
		 /*获取一条记录的详细数据*/
        $Article = M('Article');
		$Article->delete($id);
		$this->success('删除成功',U('Article/index'));
		
	}
	
	
    /**
     * 更新一条数据
     */
	 
	 
    public function update(){
        $res = D('Article')->update();
        if(!$res){
            $this->error(D('Article')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功',U('Article/index'));
        }
    }

    /**
     * 批量操作
     */
    public function batchOperate(){
        //获取左边菜单
        $this->getMenu();

        $pid = I('pid', 0);
        $cate_id = I('cate_id');

        empty($cate_id) && $this->error('参数不能为空！');

        //检查该分类是否允许发布
        $allow_publish = D('Document')->checkCategory($cate_id);
        !$allow_publish && $this->error('该分类不允许发布内容！');

        //批量导入目录
        if(IS_POST){
            $model_id = I('model_id');
            $type = 1;	//TODO:目前只支持目录，要动态获取
            $content = I('content');
            $_POST['content'] = '';	//重置内容
            preg_match_all('/[^\r]+/', $content, $matchs);	//获取每一个目录的数据
            $list = $matchs[0];
            foreach ($list as $value){
                if(!empty($value) && (strpos($value, '|') !== false)){
                    //过滤换行回车并分割
                    $data = explode('|', str_replace(array("\r", "\r\n", "\n"), '', $value));
                    //构造新增的数据
                    $data = array('name'=>$data[0], 'title'=>$data[1], 'category_id'=>$cate_id, 'model_id'=>$model_id);
                    $data['description'] = '';
                    $data['pid'] = $pid;
                    $data['type'] = $type;
                    //构造post数据用于自动验证
                    $_POST = $data;

                    $res = D('Document')->update($data);
                }
            }
            if($res){
                $this->success('批量导入成功！', U('index?pid='.$pid.'&cate_id='.$cate_id));
            }else{
                if(isset($res)){
                    $this->error(D('Document')->getError());
                }else{
                    $this->error('批量导入失败，请检查内容格式！');
                }
            }
        }

        $this->assign('pid',        $pid);
        $this->assign('cate_id',	$cate_id);
        $this->assign('type_list',  get_type_bycate($cate_id));

        $this->meta_title       =   '批量导入';
        $this->display('batchoperate');
    }


    public function region(){
        if (IS_AJAX){
            $region = M('region');
            $parent_id = isset($_GET["parent_id"]) ? $_GET["parent_id"] : "";
            $str=$parent_id;
            $code=substr($str,0,2);
            $condition['type']=2;
            $condition['code'] =array('like', (string)$code.'%');
            $city = $region->where($condition)->select();
            echo json_encode($city);
        }
    }
    public function xian(){
        if (IS_AJAX){
            $region = M('region');
            $parent_id = isset($_GET["parent_id"]) ? $_GET["parent_id"] : "";
            $str=$parent_id;
            $code=substr($str,0,4);
            $condition['type']=3;
            $condition['code'] =array('like',(string)$code.'%');
            $xian = $region->where($condition)->select();
            echo json_encode($xian);
        }
    }

  
}

?>