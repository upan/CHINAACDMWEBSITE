<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * +-----------------------------------
 *  基础数据表模型 表名可通过构造函数传递
 * +-----------------------------------
 *
 * @Author: yuanyu <yuanyu@feeyo.com>
 */

class Basic_table_model extends CI_Model
{
    
    private $table_name = NULL;
    
    function __construct($init = array())
    {
        // Assign the CodeIgniter super-object
        if (isset($init['table_name']) && !empty($init['table_name']))
        {
        	$this->table_name = $init['table_name'];
        }
    }

    /**
     * WHERE条件构造器
     * @param $where
     * @param null $master_db_source 基本用于主从 可以直接传主库对象过来 这里先预留
     */
    protected function _where_builder($where, $master_db_source = NULL)
    {
        if (!empty($where))
        {
            $master_db = !empty($master_db_source) ? $master_db_source : $this->db;
            foreach($where as $key => $val)
            {
                if(is_array($val))
                {
                    //数组形式
                    $master_db->where_in($key, $val);
                }
                else
                {
                    //字符串形式
                    $master_db->where($key, $val);
                }
                if (empty($key))
                {
                    //自定义字符串 传$param[''] = 'name=XX AND value=YY';
                    $master_db->where($val);
                }
            }
        }
    }

    /**
	 * 获取单条数据中某字段值
	 * @param array $where where条件
	 * @param string $table_name 表名
	 * @param string $field 返回字段
	 * @return array
     */
    public function bas_get_record_field($table_name = '', $where = array(), $field = '')
    {
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
    	$return = NULL;
    	if (!empty($this->table_name) && !empty($where) && !empty($field))
		{
			$this->db->select($field)->from($this->table_name);
			$this->_where_builder($where);
			$result = $this->db->get()->row_array();
			if (isset($result[$field]))
			{
				$return = $result[$field];
			}
		}
		return $return;
    }
    
    /**
	 * 获取单条数据
	 * @param array $where where条件
	 * @param string $table_name 表名
	 * @param string $field select字段
	 * @return array
     */
    public function bas_get_record($table_name = '', $where = array(), $field = '*')
    {
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
    	$return = NULL;
    	if (!empty($this->table_name) && !empty($where))
		{
			$this->db->select($field)->from($this->table_name);
			$this->_where_builder($where);
			$return = $this->db->get()->row_array();
		}
		return $return;
    }

	/**
	 * 获取数据列表
	 * @param array $where where条件
	 * @param string $table_name 表名
	 * @param string $field select字段
	 * @param string $get 获取list:列表 count:条数
	 * @param int $page 分页用 当前第几页
	 * @param int $limit 分页用 获取多少数据
	 * @param string $order_by 排序字段
	 * @param string $direction 排序方向 DESC ASC RANDOW
	 * @return array
	 */
    public function bas_get_record_list($table_name = '', $where = array(), $field = '*', $get = 'list', $page = '', $limit = '', $order_by = '', $direction = 'ASC')
	{
		$return = $get == "list" ? array() : 0;
		$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
		if (!empty($this->table_name))
		{
			$this->db->select($field)->from($this->table_name);
			$this->_where_builder($where);
			if ($get == 'list')
			{
                if (!empty($order_by))
                {
                    $_orderby = isset($order_by) ? $order_by : "{$this->table_name}_id";
                    $this->db->order_by($_orderby, $direction);
                }
				if (!empty($page) && !empty($limit))
				{
					$_offset = ($page - 1) * $limit;
					$this->db->limit($limit, $_offset);
				}
				$return = $this->db->get()->result_array();
			}
			else
			{
				$return = $this->db->count_all_results();
			}
		}
		return $return;
	}
	
	/**
	 * 从ID获取该条记录
	 * @param int $id 主键ID
	 * @param string $table_name 表名
	 * @param string $field select字段
	 * @return array
	 */
    public function bas_get_record_by_id($table_name = '', $id, $field = "*", $primary = '')
    {
    	$return = NULL;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($id) && !empty($this->table_name))
        {
        	$primary_key = !empty($primary) ? $primary : "{$this->table_name}_id";
        	$return = $this->db->select($field)->from($this->table_name)->where($primary_key, $id)->get()->row_array();
        }  
        return $return;  	
    }

	/**
	 * 添加一条记录
	 * @param array $data 记录详细值
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_add_record($table_name = '', $data = array())
    {
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
    	$return = FALSE;
        if (!empty($this->table_name) && !empty($data))
        {
	         //切换主库更新插入操作
	        $this->db->insert($this->table_name, $data);
	        $return = $this->db->insert_id();
            if (!$return) {
	           $return = $this->db->affected_rows();
            }
            //$this->db->close();
        }

        return $return;
    }
    
	/**
	 * 添加多条记录
	 * @param array $data 记录详细值数组
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_batch_add_record($table_name = '', $data = array())
    {
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
    	$return = FALSE;
        if (!empty($this->table_name) && !empty($data))
        {
	         //切换主库更新插入操作
	        $this->db->insert_batch($this->table_name, $data);
	        $return = $this->db->affected_rows();
	        //$this->db->close();
        }
        return $return;
    }

	/**
	 * 更新一条记录
	 * @param int $id 主键ID
	 * @param array $data 记录详细值
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_update_record($table_name = '', $id, $data = array(), $primary = '')
     {
    	$return = FALSE;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($id) && !empty($data) && !empty($this->table_name))
        {
            //切换主库更新操作
            $primary_key = !empty($primary) ? $primary : "{$this->table_name}_id";
            $this->db->where($primary_key, $id);
            $return = $this->db->update($this->table_name, $data);
            //$this->db->close();
        }
        return $return;
    }
    
    /**
     * [bas_batch_update_record description]
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2017-05-25T10:47:09+0800
     */
    public function bas_batch_update_record($table_name = '', $data = array(), $primary = ''){
        $return = FALSE;
        $this->table_name = empty($table_name) ? $this->table_name : $table_name;
        if (!empty($data) && !empty($this->table_name)){
            //切换主库更新操作
            $primary_key = !empty($primary) ? $primary : "{$this->table_name}_id";
            $this->db = $this->CI->load->database('master', TRUE);
            $return = $this->db->update_batch($this->table_name,$data,$primary_key);
            //$this->db->close();
        }
        return $return;
    }

    /**
	 * 通过WHERE条件更新记录
	 */
    public function bas_update_record_by_where($table_name = '', $where = array(), $data = array())
     {
    	$return = FALSE;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($data) && !empty($data) && !empty($this->table_name))
        {
            //切换主库更新操作
            $this->_where_builder($where, $this->db);
            $return = $this->db->update($this->table_name, $data);
            //$this->db->close();
        }
        return $return;
    }
    
	/**
	 * 删除一条记录(物理删除)
	 * @param int $id 主键ID
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_delete_record($table_name = '', $id, $primary = '')
    {
    	$return = FALSE;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($id) && !empty($this->table_name))
        {
	        //切换主库更新操作
	        $primary_key = !empty($primary) ? $primary : "{$this->table_name}_id";
	        if (is_array($id))
	        {
	        	$this->db->where_in($primary_key, $id);
	        }
	        else
	        {
	        	$this->db->where($primary_key, $id);
	        }
	        $return = $this->db->delete($this->table_name);
	        //$this->db->close();
        }
        return $return;
    }
    
    /**
	 * 删除一条记录(软删除)
	 * @param int $id 主键ID
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_soft_delete_record($table_name = '', $id, $soft_filed = 'delete_flg', $primary = '')
    {
    	$return = FALSE;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($id) && !empty($this->table_name))
        {
	        //切换主库更新操作
	        $primary_key = !empty($primary) ? $primary : "{$this->table_name}_id";
	        if (is_array($id))
	        {
	        	$this->db->where_in($primary_key, $id);
	        }
	        else
	        {
	        	$this->db->where($primary_key, $id);
	        }
	        $_data = array($soft_filed => 1);
	        $return = $this->db->update($this->table_name, $_data);
	        //$this->db->close();
        }
        return $return;
    }
    
	/**
	 * 删除记录(软删除)
	 * @param int $id 主键ID
	 * @param string $table_name 表名
	 * @param string $soft_filed标识软删除的字段 默认delete_flg
	 * @return boolean 是否成功
	 */
    public function bas_batch_soft_delete_record($table_name = '', $where, $soft_filed = 'delete_flg')
    {
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
    	$return = FALSE;
        if (!empty($where) && !empty($this->table_name))
        {
	        //切换主库更新操作
        	$this->db->from($this->table_name);
			$this->_where_builder($where, $this->db);
			$_data = array($soft_filed => 1);
			$return = $this->db->update($this->table_name, $_data);
			//$this->db->close();
        }
        return $return;
    }
    
    /**
	 * 删除记录(物理删除)
	 * @param int $id 主键ID
	 * @param string $table_name 表名
	 * @return boolean 是否成功
	 */
    public function bas_batch_delete_record($table_name = '', $where)
    {
    	$return = FALSE;
    	$this->table_name = !empty($table_name) ? $table_name : $this->table_name;
        if (!empty($where) && !empty($this->table_name))
        {
        	$this->db->from($this->table_name);
			$this->_where_builder($where, $this->db);
			$return = $this->db->delete($this->table_name);
			//$this->db->close();
        }
        return $return;
    }
}