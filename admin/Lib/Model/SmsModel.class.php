<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(67877605@qq.com)
// +----------------------------------------------------------------------

// 短信模型
class SmsModel extends CommonModel {
	protected $_validate = array(
			array('name','require',SMS_NAME_REQUIRE), 
		);
	
	public function save($data)
	{
		$class_name = $data['class_name']."Sms";
		
		if(class_exists($class_name))
		{
			$model = new $class_name;
			foreach($model->config as $k=>$item)
			{
				$data['config'][$k] = $data[$k];
			}
			$data['config'] = serialize($data['config']);	
			return parent::save($data);
		}
		else
		{
			return 0;
		}
	}
}
?>