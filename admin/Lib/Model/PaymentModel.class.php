<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 支付模型
class PaymentModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require',PAYMENT_NAME_REQUIRE), 
		);
	public function save($data)
	{
		require_once(VENDOR_PATH.'payment/'.$data['class_name'].'Payment.class.php');
		$class_name = $data['class_name']."Payment";
		if(class_exists($class_name))
		{
			$model = new $class_name;
			foreach($model->config as $k=>$item)
			{
				if(is_array($item))
				{					
					foreach($item as $kk=>$vv)
					{
						$data['config'][$k][$kk] = intval($data[$k][$kk]);
					}
				}
				else
				$data['config'][$k] = $data[$k];
			}
			
			$data['config'] = serialize($data['config']);	
			
		if($data['fee_type']==0)
			{
				$data['fee'] = setBaseMoney($data['fee'],intval($data['currency']));
			}
		if($data['cost_fee_type']==0)
			{
				$data['cost_fee'] = setBaseMoney($data['cost_fee'],intval($data['currency']));
			}
			
			return parent::save($data);
		}
		else
		{
			return 0;
		}
	}
}
?>