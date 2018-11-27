<?php

namespace Home\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class ArticleModel extends Model{

    /* 自动验证规则 */
    protected $_validate = array(

        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
              
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('author', 'getName', self::MODEL_BOTH, 'function'),
        array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('time', 'getCreateTime', self::MODEL_BOTH,'callback'),
	    array('order',0, self::MODEL_INSERT),
	    array('cate',0, self::MODEL_INSERT),
		array('color','', self::MODEL_INSERT),
		array('decor','', self::MODEL_INSERT),
		array('click',0, self::MODEL_INSERT),
	);
public function getLists($where,$order, $limit = '10'){
    $data=M('Article')->where($where)->order($order)->limit($limit)->select();
    return $data;
}
public function articleInfo($where){
    $data=M('Article')->where($where)->select();
    return $data;
}
}

?>
