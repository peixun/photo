<?php
// Formйсм╪дёпм
import('ViewModel');
class WatchlistViewModel extends ViewModel {
    protected $viewFields = array(
		'Watchlist'=>array('id','fid','uid','status','create_time'),//,'_type'=>'RIGHT'
		'Case' =>array('id'=>'uid','user_name', '_on'=>'ReservationSite.uid=User.id'),
    );
}
?>