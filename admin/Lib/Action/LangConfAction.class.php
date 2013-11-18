<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 语言配置
class LangConfAction extends CommonAction{
	public function add()
	{
		$arr  =   Dir::getList($this->getRealPath()."/home/Tpl/");
		foreach($arr as $item)
		{
			if($item!='..'&&$item!='.')
			{
				$themes[] = $item;
			}
		}
		$this->assign("themes",$themes);
		
		$currency_list = D("Currency")->findAll();
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->getField("id");
		foreach($currency_list as $k=>$v)
		{
			$currency_list[$k]['name'] = $v['name_'.$default_lang_id];
		}
		$this->assign('currency_list',$currency_list);
		parent::add();
	}
	public function edit()
	{
		$arr  =   Dir::getList($this->getRealPath()."/home/Tpl/");
		foreach($arr as $item)
		{
			if($item!='..'&&$item!='.')
			{
				$themes[] = $item;
			}
		}
		$this->assign("themes",$themes);
		
		$currency_list = D("Currency")->findAll();
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->getField("id");
		foreach($currency_list as $k=>$v)
		{
			$currency_list[$k]['name'] = $v['name_'.$default_lang_id];
		}
		$this->assign('currency_list',$currency_list);
		parent::edit();
	}
	//增
	public function insert()
	{
		$lang_conf_table = C("LANG_CONF"); //需要增加字段的表集合
		
		//自动创建数据对象
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}		
		$id = $model->add();
		
		if($id)
		{
			//开始处理相应的多语言表节构
			foreach($lang_conf_table as $table_name => $table)
			{
				$sql = "ALTER TABLE ".C("DB_PREFIX").$table_name." ADD COLUMN ";
				foreach($table as $column_name=>$column_type)
				{
					$sql_tmp=$sql.$column_name."_".$id." ".$column_type." CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
					D()->execute($sql_tmp);
				}			
			}
			$this->success (L('ADD_SUCCESS'));
		}
		else 
		{
			//失败提示
			$this->error (L('ADD_FAILED'));
		}		
	}
	
	//永久删除
	public function foreverdelete()
	{
		$lang_item = D("LangConf")->getById(intval($_REQUEST['id']));
		if($lang_item['lang_name'] == eyooC("DEFAULT_LANG"))
		{
			$this->error ( L('DEFAULT_LANG_CANNOT_DELETE') );
			exit;
		}
		$lang_conf_table = C("LANG_CONF"); //需要增加字段的表集合		
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//成功时删除相关表结构
					
					foreach($lang_conf_table as $table_name=>$table)
					{
						$sql = "ALTER TABLE ".C("DB_PREFIX").$table_name." drop COLUMN ";
						foreach($table as $column_name=>$column_type)
						{
							$sql_tmp = $sql.$column_name."_".$id;
							D()->execute($sql_tmp);
						}
					}
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
		
		//$this->ajaxReturn(array(),"删除失败",0);
	}
	
	public function update()
	{
			//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ($data);
		M("SysConf")->where("name='DEFAULT_LANG'")->setField("val",$data['lang_name']);
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
//开始写入配置文件
		$sys_configs = M()->query("select name,val from ".C("DB_PREFIX")."sys_conf");
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['val'])."',\n";
		}
		$config_str.=");\n ?>";
		@file_put_contents($this->getRealPath()."/Public/sys_config.php",$config_str);
			clear_cache();
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
}
?>