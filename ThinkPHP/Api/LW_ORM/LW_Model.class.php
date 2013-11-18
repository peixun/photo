<?php

require("LW_DB.class.php");
require("LW_DB_MySQL.class.php");
require("LW_DB_Exception.class.php");

class LW_Model {

    var $db;
    var $table_name;
   // echo $table_name;
    public function __construct($lw_db_configs) {
        $db2 = LW_DB::init($lw_db_configs, 'common', 1);
        $this->db = $db2['common'];
        $this -> _options['table'] = $lw_db_configs['common']["db_pefix"].strtolower($this->getModelName());
    }

    public function getModelName() {
        $this->model_name = $this->table_name;
        if(empty($this->model_name)) {
            $this->model_name =   substr(get_class($this),0,-7);
        }
        return $this->model_name;
    }

	/*-------------------------------------
	= 添加
	-------------------------------------*/
    public function add($data = array()) {
        $cols = array();
        $vals = array();
        $one = reset($data);
        if (is_array($one)) {
            $cols = implode(',', $this -> deal_field(array_keys($one)));
            foreach($data as $val) {
                $vals[] = '(' . implode(',', $this -> deal_value($val)) . ')' ;
            }
            $vals = implode(',', $vals);
        } else {
            $cols = implode(',', $this -> deal_field(array_keys($data)));
            $vals = '(' . implode(',', $this -> deal_value($data)) . ')';
        }
        $sql = "INSERT INTO " .  $this -> parse_table() . " ( {$cols} ) VALUES {$vals}";
        ey_dump($sql);
        $this->last_sql = $sql;
        return $this->db->insert($sql);
    }


	/*-------------------------------------
	= 更新
	-------------------------------------*/
    public function save($data = null) {
        $table = $this -> parse_table();
        $where = $this -> parse_where();
        if (!empty($where)) {
            $set = array();

            foreach ($data as $col => $val) {
                $set[] = $this -> deal_field($col) . ' = ' . $this -> deal_value($val);
            }
            $set = implode(',', $set);
            $where = $this -> deal_where($where);
            !empty($where) && $where = " WHERE {$where}";
            $sql = "UPDATE " . $this -> deal_field($table) . " SET {$set} {$where}";
            $this->last_sql = $sql;
            return $this->db->update($sql);


        } else {
            return $this -> add($data);
        }
    }


	/*-------------------------------------
	= 删除
	-------------------------------------*/
    public function delete() {
        $table = $this -> parse_table();
        $where = $this -> parse_where();
        if (!$where) {
            throw new DB_Exception('删除时的查询为空!');
        }
        $where = $this -> deal_where($where);
        !empty($where) && $where = " WHERE {$where}";
        $sql = "DELETE FROM " . $this -> deal_field($table) . " {$where}";
        $this->last_sql = $sql;

        return $this->db->del($sql);


    }

    public function deleteAll() {
        $table = $this -> parse_table();

        $sql = "DELETE FROM " . $this -> deal_field($table) ;
        $this->last_sql = $sql;

        return $this->db->delAll($sql);

    }

	/*-------------------------------------
	= 查询
	-------------------------------------*/
    public function find($id=null) {
        if($id) $this -> _options['where'] = "id=".intval($id);
        $sql = $this -> parse_select();
        $this->last_sql = $sql;
        return $this->db->getOne($sql);


    }
    public function findAll() {
        $sql = $this -> parse_select();
        $this->last_sql = $sql;
        //echo $sql;
        return $this->db->getAll($sql);
    }
    //
    //	public function count() {
    //		$sql = $this -> parse_select();
    //		$this->last_sql = $sql;
    //		return $this->db->getOne($sql);
    //	}

	/*-------------------------------------
	= 条件处理 --- 添加，更新
	-------------------------------------*/
    public function deal_field($str = '') {
        if (is_array($str)) {
            $str = array_map(array(__CLASS__, __METHOD__), $str);
            return $str;
        }
        $str && $str = "`{$str}`";
        return $str;
    }
    public function deal_value($str = '') {
        if (is_array($str)) {
            $str = array_map(array(__CLASS__, __METHOD__), $str);
            return $str;
        }

        $str = addslashes($str);
        $str = is_string($str)?"'{$str}'":"{$str}";
        return $str;
    }

	/*-------------------------------------
	= 条件处理 --- 查询
	-------------------------------------*/
    public function parse_select() {
        if (!empty($this -> _options['sql'])) {
            $sql = $this -> _options['sql'];
            unset($this -> _options['sql']);
        } else {
            $field = '*';
            if (!empty($this -> _options['field'])) {
                if (is_array($this -> _options['field'])) {
                    $field = implode(',', $this -> deal_field($this -> _options['field']));
                } else {
                    $field = $this -> _options['field'];
                }
                unset($this -> _options['field']);
            }
            $table = $this -> parse_table();
            $where = $this -> parse_where();
            !empty($where) && $where = ' WHERE ' . $where;
            $order = '';
            if (!empty($this -> _options['order'])) {
                $order = ' ORDER BY ' . $this -> _options['order'];
                unset($this -> _options['order']);
            }
            $limit = '';
            if (!empty($this -> _options['limit'])) {
                $limit = ' LIMIT ' . $this -> _options['limit'];
                unset($this -> _options['limit']);
            }
            if( !empty( $this -> _options[ 'group' ] ) ) {
                $group = ' GROUP BY '.$this->_options['group'];
            }
            $sql = "SELECT {$field} FROM {$table}{$where}{$order}{$limit}{$group}";
        }
        return $this -> deal_table($sql);
    }

    protected function parse_where() {
        $where = '';
        if (!empty($this -> _options['where'])) {
            if (is_array($this -> _options['where'])) {
                $where = $this -> deal_where($this -> _options['where']);
            } else {
                $where = $this -> _options['where'];
            }
            unset($this -> _options['where']);
        }
        return $where;
    }


    public function deal_where($where) {
        if (is_array($where)) {
            if (array_key_exists('_logic', $where)) {
                $logic = strtoupper($where['_logic']);
                unset($where['_logic']);
            } else {
                $logic = 'AND';
            }
            foreach ($where as $key=>$term) {
                if(!is_array($term)) {
                    $term2 = is_string($term)?"(`" . $key. "` = '" .$term . "')":"(`" . $key. "` = " .$term . ")";
                }else {
                    switch(strtoupper($term[0])) {
                        case "IN":
                            $in = $this->parse_in($term[1]);
                            $term2 = "(`" . $key. "` IN " .$in . ")";
                            break;
                        case "NOT IN":
                            $in = $this->parse_in($term[1]);
                            $term2 = "(`" . $key. "` NOT IN " .$in . ")";
                            break;
                        case "GT":
                            $term2 = "(`" . $key. "` > " .$term[1] . ")";
                            break;
                        case "LT":
                            $term2 = "(`" . $key. "` < " .$term[1] . ")";
                            break;
                        case "NEQ":
                            $term2 = "(`" . $key. "` != " .$term[1] . ")";
                            break;
                        case "LIKE":
                            $term2 = "(`" . $key. "` LIKE '" .$term[1] . "')";
                            break;
                        case "BETWEEN":
                            $between = implode(" and ",$term[1]);
                            $term2 = "(`" . $key. "` BETWEEN " .$between . ")";
                            break;
                    }
                }

                $where2[] = $term2;
            }

            $where = implode(' ' . $logic . ' ', $where2);
        }
        return $where;
    }

    protected function parse_in($in_arr) {

        $in_str = "(";
        foreach($in_arr as $key=>$v) {
            $in_str .= "'".$v."'".",";
        }
        $in_str = rtrim($in_str,",");
        $in_str .= ")";
        return $in_str;

    }


    protected function parse_table() {
        $table = $this -> _table[1];
        if (!empty($this -> _options['table'])) {
            if (is_numeric($this -> _options['table'])) {
                $table = $this -> _table[$this -> _options['table']];
            } else {
                $table = $this -> _options['table'];
            }
        //	unset($this -> _options['table']);
        }
        return $table;
    }
    protected function deal_table($sql) {
        return preg_replace_callback('/#(\d+)/', array(__CLASS__, 'deal_table_callback'), $sql);
    }
    protected function deal_table_callback($tab) {
        return $this -> _table[$tab[1]];
    }

    public function where($where=null) {
        $this -> _options['where'] = $where;
        return $this;
    }
    public function order($order=null) {
        $this -> _options['order'] = $order;
        return $this;
    }
    public function table($table=null) {
        $this -> _options['table'] = $table;
        return $this;
    }
    public function field($field=null) {
        $this -> _options['field'] = $field;
        return $this;
    }
    public function group( $field=null ) {
        $this->_options['group'] = $field;
        return $this;
    }
    public function limit($limit=null) {
        $this -> _options['limit'] = $limit;
        return $this;
    }

    public function getLastSql() {
        return $this->last_sql;
    }

    /*
     * 渲染动态和通知中的变量
     */
    protected function _replaceConstant($data,$appid) {
        $result = str_replace("{WR}",SITE_URL,$data);
        if($appid == 10){
            $result = str_replace(array('{SITE_URL}/apps/photo/index.php/','{SITE_URL}Index/','{SITE_URL}/thumb.php','{UPLOAD_URL}','{__TS__}'),array('{'.$appid.'}','{'.$appid.'}/Index/',SITE_URL.'/thumb.php',UPLOAD_URL,__TS__),$result );

   }else{
            $result = str_replace(array('{SITE_URL}','{UPLOAD_URL}','{PUBLIC_URL}','{THEME_URL}','{__TS__}'),array('{'.$appid.'}',UPLOAD_URL,PUBLIC_URL,THEME_URL,__TS__),$result );
        }
        $result = preg_replace_callback('/\{(\d+)\}/i',array($this,'_replaceURL'),$result);
        return $result;
    }
    protected function _replaceURL($var) {
        return getAppInfo($var[1],'APP_URL');
    }


    protected function __getTitle($template,$data) {
        $result = $template; //title的模板
        $title_data = $this->__unserialize($data["title"]);  //title的数据
		if(!$body_data){
			$body_data = unserialize(stripcslashes($data['body']));
		}
        $actor = "<a style='white-space: nowrap;' href='".__TS__."/space/".$data["uid"]."'>".$data["username"].'</a>';
        $title_data["actor"] = $actor;
        //替换自定义变量
        $result = $this->__substitutionVariable($title_data, $result,$data['appid']);
        return $result;
    }

    protected function __getBody($template,$data) {

        $body_data = $this->__unserialize($data["body"]);  //body的数据


        return $this->__substitutionVariable($body_data, $template, $data['appid']);
    }

    /**
     * 替换模板中的变量
     *
     * @param <type> $title_data 数据
     * @param <type> $template 模板
     * @param <type> $appid appid
     * @return array 处理后的数组
     */
    protected function __substitutionVariable($data,$template,$appid) {

        $result = $template;


        foreach($data as $k=>$v) {   //替换
            //$result = str_replace('{'.$k.'}',stripcslashes($v),$result);
			$search[] = '{'.$k.'}';
			$replace[] = stripcslashes($v);

        }


		$result = str_replace($search, $replace, $result);

        //替换特殊变量
        $result = $this->_replaceConstant($result,$appid);
        return $result;
    }

    //分页
    public function __getPageLimitFirst($pageLimit,$page) {
        if(isset($page)){
            $curPage = $page;
        }else{
            $curPage = $_GET["p"]?intval($_GET["p"]):1;
        }
        $firstRow = ($curPage-1)*$pageLimit;
        $result    =	$firstRow.','.$pageLimit;
        return $result;
    }

    public function __getPageLimitSecond($count,$pageLimit) {
        return (int)ceil($count/$pageLimit);
    }
	public function __unserialize($data) {
		$result = unserialize($data);
		if(!$result){
			$result = unserialize(stripcslashes($data));
		}
		return $result;
	}
}
?>
