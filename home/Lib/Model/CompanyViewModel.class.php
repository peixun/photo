<?php
// Formйсм╪дёпм
import('ViewModel');
class CompanyViewModel extends ViewModel {
    protected $viewFields = array(
		'User'=>array('id','status','type','active'),//,'_type'=>'RIGHT'
		'Company' =>array('id'=>'com_id','company_name','logo','service_area','business_scope','decoration_pattern','main_price','good_style', 'content', 'uid', 'create_time', '_on'=>'Company.uid=User.id'),
    );
}
?>