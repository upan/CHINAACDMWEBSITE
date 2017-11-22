<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 增删改
 * @authors MAGENJIE (1299234033@qq.com)
 * @date    2016-06-29 14:06:01
 * @version 1.0.0
 */
class ACDM_Model extends CI_Model{
    public $table;
    public $pageUri = "";//分页的url
    public $pagesInfo = ""; //分页html
    public $pageSize = 10;
    public $show_query = FALSE;// 是否显示执行的sql语句; FALSE,WRITE,ECHO;
    public $query_sql_record = array();
    public function __construct(){
        parent::__construct();
    }

    /**
     * 默认分页样式
     * $config = array('total_rows','uri');
     */
    final public function pages($page_config = array()){
        $this->load->library('pagination');
        empty($this->pageUri) && $this->pageUri = $this->_uri;
        $config['base_url'] = base_url($this->pageUri);
        $config['total_rows'] = 0;
        $config['per_page'] = $this->pageSize;
        $config['uri_segment'] = 3;
        $config['num_links'] = 4;
        $config['use_page_numbers']     = TRUE;
        $config['page_query_string']    = TRUE;
        $config['reuse_query_string']   = TRUE;
        $config['query_string_segment'] = 'page';
        $config = array_merge($config,$page_config);
        
        $config['full_tag_open'] = '<div class="text-right"><ul class="pagination">';
        $config['full_tag_close'] = '</ul></div>';
        $config['first_link'] = '第一页';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '最末页';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    /**
     * [simple_listinfo 简单的查询分页数据]
     * @author MAGENJIE(1299234033@qq.com)
     * @datetime 2016-10-27T22:33:14+0800
     */
    final public function simple_listinfo($where = array(),$fields = '*',$order = '',$page = 1,$table = ''){
        return $this->listinfo($where,$fields,$order,$page,$table);
    }

    /**
     * [search_listinfo 模糊查询分页数据]
     * @author MAGENJIE(1299234033@qq.com)
     * @datetime 2017-02-12T21:18:57+0800
     */
    final public function search_listinfo($where = array(),$like = array(),$page = 1,$order = '',$fields = '*',$table = ''){
        return $this->listinfo($where,$fields,$order,$page,$table,'',$like);
    }

    /**
     * [listinfo 分页数据]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T19:40:22+0800
     */
    final public function listinfo($where = array(),$fields = '*',$order = '',$page = 1,$table = '',$key = '',$like = array()){
        $page = max(intval($page),1);
        $offset = $this->pageSize*($page - 1); 
        $this->total = $this->total_select($where,$table);
        if ($offset > $this->total) {
            $page = round($this->total/$this->pageSize);
            $offset = max($this->pageSize*($page-1),0); 
        }

        if ($this->total > $this->pageSize) {
            $this->pagesInfo = $this->pages(array('total_rows'=>$this->total));
        }

        if ($this->total > 0) {
            $this->select($where, $fields, "$offset, $this->pageSize", $order,'', $key,$table,FALSE,$like);
            return $this->select($where, $fields, "$offset, $this->pageSize", $order,'', $key,$table,FALSE,$like);
        } else {
            return array();
        }
    }

    /**
     * 手动设置获取分页html
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2017-03-22T10:43:43+0800
     */
    final public function set_pagesInfo($page = 1,$total = 0){
        $page = max(intval($page),1);
        $offset = $this->pageSize*($page - 1); 
        if ($offset > $total) {
            $page = round($total/$this->pageSize);
            $offset = max($this->pageSize*($page-1),0); 
        }

        if ($total > $this->pageSize) {
            $this->pagesInfo = $this->pages(array('total_rows'=>$total));
        }
        return $this->pagesInfo;
    }

    /**
     * [simple_select 简单的查询 没有分页]
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2016-10-26T16:51:34+0800
     */
    final public function simple_select($where=array(),$order = '',$table='',$key=''){
        return $this->select($where,'*','',$order,'',$key,$table);
    }

    /**
     * [whole_select 查询整张表]
     * @author MAGENJIE(1299234033@qq.com)
     * @datetime 2016-10-27T22:45:45+0800
     */
    final public function whole_select($table = '',$key = '',$order = 'id desc'){
        return $this->select(array(),'*','',$order,'',$key,$table);
    }

    /**
     * [total_select 根据条件获取数据条目 主要用于分页]
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2016-10-27T17:51:57+0800
     */
    final public function total_select($where = array(),$table = ''){
        $table ? : $table = $this->table;
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        return $this->db->count_all_results();
    }

    /**
     * [select description]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T19:51:18+0800
     * @param $where        查询条件[例`name`='$name']
     * @param $data         需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $limit        返回结果范围[例：10或10,10 默认为空]
     * @param $order        排序方式    [默认按数据库默认方式排序]
     * @param $group        分组方式    [默认为空]
     * @param $key          返回数组按$key索引
     * @param $total        list 返回数组 total 返回查询结果数量
     * @param $like         array('fields'=>'title','match'=>'word','mode'=>'both、left right')
     * @return array        查询结果集数组
     */
    final public function select($where = array(),$fields = '*',$limit = '',$order = '', $group = '',$key = '',$table= '',$total = FALSE,$like = array()){
        $this->db->select($fields);
        if ($like && isset($like['field']) && isset($like['match']) && isset($like['mode'])) {
            $this->db->like($like['field'],$like['match'],$like['mode']);
        }
        $where && $this->db->where($where);
        if($limit){
            $limit_array = explode(",", $limit);
            if(count($limit_array) == 1){
                $this->db->limit($limit);
            }else{
                $this->db->limit($limit_array[1],$limit_array[0]);
            }
        }
        
        $order && $this->db->order_by($order);
        $group && $this->db->group_by($group);
        
        empty($table) ? $this->db->from($this->table) : $this->db->from($table);
        $result = $this->db->get()->result_array();
        
        $key && $result = array_column($result,NULL,$key);
        $this->query_sql_record[] = $this->db->last_query();
        if ($total) {
            return array('total'=>count($result),'data'=>$result);
        }else{
            return $result;
        }
    }
        
    /**
     * [do_select_sql 执行查询sql语句]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T20:06:28+0800
     */
    final public function do_sql($sql,$key = ''){
        $result = $this->db->query($sql)->result_array();
        $key && $result = array_column($result,NULL,$key);
        $this->query_sql_record[] = $this->db->last_query();
        return $key;
    }

    /**
     * [get_one 获取一条数据]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T20:09:51+0800
     */
    final public function get_one($where = array(),$table = '',$fields='*',$key = ''){
        $result = $this->select($where,$fields,'1','','',$key,$table);
        empty($result) ? $r = array() : $r = $result[0];
        return $r;
    }
    
    /**
     * [limit_one 根据排序只获取一条数据]
     * @author MAGENJIE(1299234033@qq.com)
     * @datetime 2017-02-04T21:43:41+0800
     */
    final public function limit_one(){

    }

    /**
     * [insert description]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T20:13:23+0800
     */
    final public function insert($data = array(),$table = '',$return_insert_id = TRUE){
        if (empty($data)) return FALSE;
        $table ? : $table = $this->table;
        $tag = $this->db->insert($table,$data);
        $return_insert_id && $tag = $this->db->insert_id();
        $this->query_sql_record[] = $this->db->last_query();
        return $tag;
    }
    
    /**
     * [insert_batch description]
     * @author MAGENJIE(1299234033@qq.com)
     * @date    2016-10-23T20:17:45+0800
     */
    final public function insert_batch($data = array(),$table = ''){
        if (empty($data)) return FALSE;
        $table ? : $table = $this->table;
        $tag = $this->db->insert_batch($table,$data);
        $this->query_sql_record[] = $this->db->last_query();
        return $tag;
    }
        
    /**
     * [update_batch 批量更新操作]
     * @author MAGENJIE(magenjie@feeyo.com)
     * @datetime 2016-10-27T18:01:23+0800
     * field 更新数据的判断条件
     */
    final public function update_batch($data = array(),$table = '',$field){
        if (empty($data)) return FALSE;
        $table ? : $table = $this->table;
        $tag = $this->db->update_batch($table,$data,$field);
        $this->query_sql_record[] = $this->db->last_query();
        return $tag;
    }

    /**
     * 执行更新记录操作
     * @param $data 要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     *              为数组时数组key为字段值，数组值为数据取值
     *              为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *              为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *              数组的另一种使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $where 更新数据时的条件,可为数组或字符串
     * @return boolean
     */
    final public function update($data = array(), $where = array(),$table = '',$use_set=FALSE,$is=FALSE) {
        if (empty($data)) return FALSE;
        $this->db->where($where);
        if(is_array($data)){
            foreach($data as $k=>$v){
                switch (substr($v, 0, 2)) {
                    case '+=':
                        $this->db->set($k, $k."+".str_replace("+=","",$v), false);
                        unset($data[$k]);
                        break;
                    case '-=':
                        $this->db->set($k, $k."-".str_replace("-=","",$v), false);
                        unset($data[$k]);
                        break;
                    case '<>':
                        $this->db->set($k, $k."<>".$v, false);
                        unset($data[$k]);
                        break;
                    case '<=':
                        $this->db->set($k, $k."<=".$v, false);
                        unset($data[$k]);
                        break;
                    case '>=':
                        $this->db->set($k, $k.">=".$v, false);
                        unset($data[$k]);
                        break;
                    case '^1':
                        $this->db->set($k, $k."^1", false);
                        unset($data[$k]);
                        break;
                    case 'in':
                        if(substr($v, 0, 3)=="in("){
                            $this->db->where_in($k, $v, false);
                            unset($data[$k]);
                            break;
                        }else{
                        }
                    default:
                        $this->db->set($k, $v, true);
                }
            }
        }
        $table ? : $table = $this->table;
        
        $tag = $this->db->update($table, $data);
        $this->query_sql_record[] = $this->db->last_query();
        return $tag;
    }
    
    /**
     * 执行删除记录操作
     * @param $where 删除数据条件,不充许为空
     */
    final public function delete($where = array(),$table = '') {
        $table ? : $table = $this->table;
        $tag = $this->db->delete($table,$where);
        $this->query_sql_record[] = $this->db->last_query();
        return $tag;
    }
    
    /**
     * [where_in in查询]
     * @author MAGENJIE(1299234033@qq.com)
     * @datetime 2017-02-18T09:59:58+0800
     * @param $where array('Frank', 'Todd', 'James')
     * @param $field "username"
     */
    final public function where_in($where,$field,$table='',$fields="*",$key='') {
        $table ? : $table = $this->table;
        $this->db->select($fields);
        $this->db->from($table);
        if (empty($filed)) {
            $this->db->where_in($field,$where);
        }else{
            foreach ($where as $filed => $value) {
                $this->db->where_in($field,$value);
            }
        }
        $result = $this->db->get()->result_array();
        $this->query_sql_record[] = $this->db->last_query();
        $key && $result = array_column($result,NULL,$key);
        return $result;
    }

    /**
     * 计算记录数
     * @param string/array $where 查询条件
     */
    final public function count($where = array(),$table = '') {
        $result = $this->get_one($where,$table,"COUNT(*) AS num");
        return isset($result['num']) ? $result['num'] : 0;
    }

    final public function sum($where = array(),$field,$table = '') {
        $result = $this->get_one($where,$table,"sum({$field}) AS s");
        return $result['s'];
    }
    
    final public function max_one($where = array(),$field,$table = '') {
        $result = $this->get_one($where,$table,"max({$field}) AS s");
        return $result;
    }
    
    /**
     * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
     * @param $data 条件数组或者字符串
     * @param $front 连接符
     * @param $in_column 字段名称
     * @return string
     */
    final public function to_sqls($data, $front = ' AND ', $in_column = false,$is_digt=false) {
        if($in_column && is_array($data)) {
            $ids = '\''.implode('\',\'', $data).'\'';
            if($is_digt)$ids = implode(',', $data) ;
            $sql = "$in_column IN ($ids)";
            return $sql;
        } else {
            if ($front == '') {
                $front = ' AND ';
            }
            if(is_array($data) && count($data) > 0) {
                $sql = '';
                foreach ($data as $key => $val) {
                    $sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";    
                }
                return $sql;
            } else {
                return $data;
            }
        }
    }

    // 记录 MYSQL 执行语句;
    final public function __destruct(){
        if ($this->show_query) {
            foreach ($this->query_sql_record as $sql) {
                if ($this->show_query === "WRITE") {
                    writeLog("SQL",$sql);
                }elseif($this->show_query === "ECHO"){
                    echo "{$sql}<br />";
                }    
            }
        }
    }
}


