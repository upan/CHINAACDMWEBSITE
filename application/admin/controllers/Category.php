<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 网站文章导航;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-25T10:28:55+0800
*/
class Category extends Admin_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model("category_model","m_category");
        $this->category_type_config = array(
            1 => array(
                "name" => "单页",
            ),
            2 => array(
                "name" => "文章",
            ),
            3 => array(
                "name" => "下载",
            ),
        );
    }

    public function index(){
        $list = $this->m_category->whole_select();
        $this->load->library("tree");
        $this->tree->tree($list);
        $list = $this->tree->getArray();
        $data = array(
            "list"      => $list ? $list : array(),
            "default"   => $this->m_category->_default(),
        );
        $this->view($data);
    }

    public function add($id = 0){
        $post = $this->I();
        $id = (int)$id;
        $post["update_time"] = time();
        unset($post["id"],$post["_name"]);
        if($id){
            $tag = $this->m_category->update($post,array('id'=>$id));
        }else{
            $tag = $this->m_category->insert($post);
        }
        $response = $this->_format_response($tag,"栏目{$post['name']}",$id);
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }

    public function delete($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit(array("status"=>FALSE,"msg"=>"该栏目不存在"));
        $info = $this->m_category->get_one(array("id"=>$id));
        empty($info) && $this->json_exit(array("status"=>FALSE,"msg"=>"该栏目不存在"));
        $articles = $this->m_category->simple_select(array("catid"=>$id),"id desc","article");
        if (empty($articles)) {
            $tag = $this->m_category->delete(array("id"=>$id));
            $response = array("status"=>$tag,"msg"=>"删除栏目{$info['name']}".($tag ? "成功" : "失败"));
        }else{
            $response = array("status"=>FALSE,"msg"=>"请先处理栏目下的文章数据");
        }
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }
    
    public function disabled($id = 0){
        $id = (int)$id;
        !$id && $this->json_exit();
        $navInfo = $this->m_category->get_one(array('id'=>$id));
        empty($navInfo) && $this->json_exit();
        $status = abs(1 - $navInfo['status']);
        $tag = $this->m_category->update(array('status'=>$status),array('id'=>$id));
        $this->_log_desc = array(
            "desc" => "更改网站导航 {$navInfo['name']} 状态值 为{$status}",
            "status" => $tag
        );
        $this->json_exit(array("status"=>$tag));
    }

    public function try_use_list(){
        $list = $this->m_category->whole_select("try_use_apply");
        $this->view(array("list"=>$list));
    }

    public function article_tags_list(){
        $list = $this->m_category->whole_select("article_tags");
        $this->view(array("list"=>$list));   
    }
}