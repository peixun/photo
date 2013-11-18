<?php

class FeedDelLwModel extends LW_Model {
var $table_name = "feed_del";
	public function getDelList($data){
		return $this->where($data)->findAll();
	}
}

?>