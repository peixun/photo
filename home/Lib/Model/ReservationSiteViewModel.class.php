<?php
// Formйсм╪дёпм
import('ViewModel');
class ReservationSiteViewModel extends ViewModel {
    protected $viewFields = array(
		'ReservationSite'=>array('id','construction_id','uid','com_uid','create_time'),//,'_type'=>'RIGHT'
		'Construction' =>array('id'=>'construc_id','name_1','root_unit','budget','area','visit_time', '_on'=>'ReservationSite.construction_id=Construction.id'),
		'User' =>array('id'=>'uid','user_name', '_on'=>'ReservationSite.uid=User.id'),
    );
}
?>