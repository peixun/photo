<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 多语言模型的基类
class MultiLangModel extends CommonModel {
	public function __construct()
	{
		$lang_conf = C("LANG_CONF");
		$lang_envs = D("LangConf")->findAll();
		$validate = $this->_validate;
		$new_validate = array();

		foreach($validate as $item)
		{
			if(isset($lang_conf[parse_name(MODULE_NAME)][$item[0]]))
			{
				$base_name = $item[0];
				//验证字段为多语言字段
				foreach($lang_envs as $lang_item)
				{
					$item[0] = $base_name."_".$lang_item['id'];
					$new_validate[] = $item;
				}
			}
			else 
			{
				$new_validate[] = $item;
			}
		}
		$this->_validate = $new_validate;
		parent::__construct();
	}

}
?>