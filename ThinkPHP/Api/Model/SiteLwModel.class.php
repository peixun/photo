<?php

class SiteLwModel extends LW_Model {

	public function get($format="php"){

		$result = $this->find();

		switch($format) {
			case "json": return json_encode($result);
			default    : return $result;
		}
	}



}

?>