<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 日志列表
class LoginRecordAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}

    public function foreverdeletes() {
        //echo '11';
		//删除指定记录
		$name=$this->getActionName();
       // echo $name;
		$model = D ($name);
        $whee['id']=array('gt',0);
        $res=$model->where($whee)->delete();
         if($res){
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('清空登录错误日志成功');
         }else{
             $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->error ('清空登录错误日志失败');
         }
    }
}
?>