<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-31T14:55:37+0800
 */
class Home_model extends ACDM_Model {

    public function __construct(){
        parent::__construct();
    }

    //文章列表 与 下载列表;
    public function content_list(){
        $list = array();
        return $list;
    }

    // 热门文章;
    public function hot_articles($catid = 0){
        $where = $catid ? array("catid"=>$catid,"status"=>1) : array("status"=>1);
        return $this->listinfo($where,"id,title,read",'read DESC,create_time DESC',1,'article');
    }

    // 热门标签;
    public function hot_tags(){
        return $this->listinfo(array("id>"=>0),"id,name,num",'num DESC',1,'article_tags');
    }
    
    // 相关文章;最新文章;
    public function related_articles($catid = 0,$exclude_id = 0){
        $where = $catid ? array("catid"=>$catid,"status"=>1) : array("status"=>1);
        $exclude_id && $where["id!="] = $exclude_id;
        return $this->listinfo($where,'id,title,create_time','create_time DESC',1,'article');
    }

    public function pages($page_config = array()){
        $this->load->library('pagination');
        empty($this->pageUri) && $this->pageUri = $this->_uri_string;
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
            
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '首页';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '尾页';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="zh-cur"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        $page_html = $this->pagination->create_links();
        $total_rows = (int)$page_config["total_rows"];
        if ($page_html) {
            $page_html .= '<span class="zh-total">共'.$total_rows.'条</span>
                <form class="zh-skip" action="'.base_url($this->pageUri).'?">
                    <input type="text" name="page">
                    <button class="zh-btn-blue" type="button" onclick="page_redirect(this)">跳转</button>
                </form>';
        }
        return $page_html;
    }

    // 获取文章 下载列表;
    public function get_content_list($catid,$fields,$page = 1,$table,$order = 't.sort desc,t.create_time desc'){
        $result = $this->db->select($fields)
                           ->from("{$table} as t")
                           ->join("admin_user as au","au.id = t.create_uid","left")
                           ->where(array("t.status"=>1,"t.catid"=>$catid))
                           ->limit($this->pageSize,($page - 1)*$this->pageSize)
                           ->order_by($order)
                           ->get()
                           ->result_array();
        return $result;
    }

    // 获取文章详情;
    public function get_article_detail($id = 0){
        return $this->db->select("a.*,CONCAT(au.company,' ',au.truename) as create_username,au.photo as create_userphoto")
                        ->from("article as a")
                        ->join("admin_user as au","au.id = a.create_uid","left")
                        ->where(array('a.id'=>$id,'a.status'=>1))
                        ->get()
                        ->row_array();
    }

    // 判断是否为手机端访问.
    public function check_is_mobile(){
        return $this->_is_mobile_1() || $this->_is_mobile_2() || $this->_is_mobile_3() || $this->_is_mobile_4();
    }

    /** 
     * 是否是移动端访问 
     * @desc 判断是否是移动端进行访问 
     * @方法一：判断是否有HTTP_X_WAP_PROFILE，有则一定是移动设备。
     */
    private function _is_mobile_1(){
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {  
             return true;  
        } else {  
             return false;  
        }  
    }

    /** 
     * 是否是移动端访问 
     * @desc 判断是否是移动端进行访问 
     * @方法二：判断HTTP_VIA信息是否含有wap信息，有则一定是移动设备。 
     */
    private function _is_mobile_2(){
        if (isset ($_SERVER['HTTP_VIA'])) {  
             return true;  
        } else {  
             return false;  
        }
    }

    /** 
     * 是否是移动端访问 
     * @desc 判断是否是移动端进行访问 
     * @方法三：判断是否有HTTP_USER_AGENT信息是否是手机发送的客户端标志，有则一定是移动设备。 
     */   
    private function _is_mobile_3(){
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {  
            $clientkeywords = array ('nokia',  'sony','ericsson','mot',  
                'samsung','htc','sgh','lg','sharp',  
                'sie-','philips','panasonic','alcatel',  
                'lenovo','iphone','ipod','blackberry',  
                'meizu','android','netfront','symbian',  
                'ucweb','windowsce','palm','operamini',  
                'operamobi','openwave','nexusone','cldc',  
                'midp','wap','mobile'  
                );   
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字  
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){  
                return true;  
            } else {  
                return false;  
            }  
        } else {  
            return false;  
        } 
    }

    /** 
     * 是否是移动端访问 
     * @desc 判断是否是移动端进行访问 
     * @方法四：判断HTTP_ACCEPT信息 
     */
    private function _is_mobile_4(){
        if (isset ($_SERVER['HTTP_ACCEPT'])) {  
            // 如果只支持wml并且不支持html那一定是移动设备  
            // 如果支持wml和html但是wml在html之前则是移动设备  
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {  
                return true;  
            } else {  
                return false;  
            }  
        } else {  
            return false;  
        }
    }
}