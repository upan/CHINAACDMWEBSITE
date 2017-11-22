<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 网站文章;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-25T11:10:58+0800
*/
class Download extends Admin_Controller {
    public $category = array();
    public function __construct(){
        parent::__construct();
        $this->load->model("download_model","m_download");
        $this->load->library("tree");
        $reuslt = $this->m_download->simple_select(array("status"=>1,"type"=>3),"id desc","category");
        $this->tree->tree($reuslt);
        $this->categorys = $this->tree->getArray();
    }

    public function index(){
        $_filter = array(
            "status"    => "",
            "title"     => "",
            "author"    => "",
            "catid"     => 0,
        );
        $filter = $this->I("filter",$_filter);
        $page = $this->I("page",1);
        $where = $like = array();
        foreach ($filter as $key => $value) {
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'catid':
                    $where["catid"] = $value; 
                    break;
                case 'title':
                    $like = array('field'=>'title','match'=>$value,'mode'=>'both');
                    break;
                case 'status':
                    $where["status"] = $value;
                    break;
            }
        }
        $list = $this->m_download->search_listinfo($where,$like,$page,'id asc');
        $data = array(
            "filter"    => $filter,
            "list"      => $list,
            "pagesInfo" => $this->m_download->pagesInfo,
        );
        $this->view($data);
    }
    
    public function add($id = 0){
        $post = $this->I();
        if ($post) {
            $id = isset($post["id"]) ? (int)$post["id"] : 0;
            unset($post["id"]);
            $post["update_time"] = time();
            if ($id) {
                $post["update_uid"] = $this->session->uid;
                $tag = $this->m_download->update($post,array("id"=>$id));
            }else{
                $post["create_time"] = time();
                $post["create_uid"] = $this->session->uid;
                $tag = $this->m_download->insert($post);
            }
            $response = $this->_format_response($tag,"文章 \"{$post['title']}\"",$id);
            $this->_log_desc = $response["msg"];
            $this->json_exit($response);
        }
        $id = (int)$id;
        if ($id) {
            $download = $this->m_download->get_one(array("id"=>$id));
        }else{
            $download = $this->m_download->_default();  
        }
        $data = array(
            "download"   => $download,
        );
        $this->view($data);
    }

    // 软删除;
    public function delete($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $download = $this->m_download->get_one(array("id"=>$id));
        if (empty($download)) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"Sorry! 文章不存在"));
        }
        $tag = $this->m_download->delete(array("id"=>$id));
        $response = $this->_format_response($tag,"删除文章[{$download["id"]}] {$download['title']}");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }

    // 关闭 隐藏 存草稿;
    public function disabled($id = 0){
        $id = (int)$id;
        !$id && $this->json_exit();
        $downInfo = $this->m_download->get_one(array('id'=>$id));
        empty($downInfo) && $this->json_exit();
        $status = abs(1 - $downInfo['status']);
        $tag = $this->m_download->update(array('status'=>$status),array('id'=>$id));
        $this->_log_desc = array(
            "desc" => "更改网站导航 {$downInfo['name']} 状态值 为{$status}",
            "status" => $tag
        );
        $this->json_exit(array("status"=>$tag));
    }

    // public function upload_thumb(){
    //     if (empty($_FILES) || !isset($_FILES['thumb'])) {
    //         $this->json_exit();
    //     }
    //     list($name,$ext) = explode(".",$_FILES['thumb']['name']);
    //     $file = date('YmdHis').rand(10,99).".{$ext}";
    //     $path = "/upload/download_thumb/{$file}";
    //     //===========================================
    //     $config['file_name'] = $file;
    //     $config['upload_path'] = "upload/download_thumb/";
    //     $config['allowed_types'] = 'jpg|gif|png';
    //     $config['remove_spaces'] = true;
    //     $config['max_size'] = 2048;//2M
    //     //===========================================
    //     $this->load->library('upload', $config);
    //     if (!$this->upload->do_upload("thumb")){
    //         $response = array("status"=>FALSE,"msg"=>strip_tags($this->upload->display_errors()));
    //     }else{
    //         $response = array("status"=>TRUE,"path"=>$path);
    //     }
    //     $this->json_exit($response);
    // }

    // // 下载文件;
    // public function upload_file(){
    //     if (empty($_FILES) || !isset($_FILES['download_file'])) {
    //         $this->json_exit();
    //     }
    //     list($name,$ext) = explode(".",$_FILES['download_file']['name']);
    //     $file = date('YmdHis').rand(10,99).".{$ext}";
    //     $path = "/upload/download_file/{$file}";
    //     //===========================================
    //     $config['file_name'] = $file;
    //     $config['upload_path'] = "upload/download_file/";
    //     $config['allowed_types'] = 'doc|pdf|docx';
    //     $config['remove_spaces'] = true;
    //     $config['max_size'] = 51200; //50M
    //     //===========================================
    //     $this->load->library('upload', $config);
    //     if (!$this->upload->do_upload("download_file")){
    //         $response = array("status"=>FALSE,"msg"=>strip_tags($this->upload->display_errors()));
    //     }else{
    //         $size = $this->upload->data("file_size");
    //         $response = array("status"=>TRUE,"path"=>$path,"size"=>$size);
    //     }
    //     $this->json_exit($response);   
    // }

    public function upload_thumb(){
        if (empty($_FILES) || !isset($_FILES['thumb'])) {
            $this->json_exit();
        }
        $this->load->library("Riak_server");
        list($name,$ext) = explode(".",$_FILES['thumb']['name']);
        if (!in_array(strtolower($ext), array("jpg","gif","png","jpeg"))) {
            $this->json_exit(array("status"=>TRUE,"msg"=>"请上传图片类型文件！"));
        }
        $size = $_FILES["thumb"]["size"]/1024;
        if ($size > 2048) {
            $this->json_exit(array("status"=>TRUE,"msg"=>"图片超过2M！"));   
        }
        $file_name = "download_thumb_".date('YmdHis').rand(1000,9999).".{$ext}";
        if ($this->riak_server->upload($file_name,file_get_contents($_FILES["thumb"]["tmp_name"]))) {
            $response = array("status"=>TRUE,"path"=>base_url("file/riak_get/{$file_name}"));
        }else{
            $response = array("status"=>FALSE,"msg"=>strip_tags($this->riak_server->error));
        }
        $this->json_exit($response);
    }

    public function upload_file(){
        if (empty($_FILES) || !isset($_FILES['download_file'])) {
            $this->json_exit();
        }
        $this->load->library("Riak_server");
        list($name,$ext) = explode(".",$_FILES['download_file']['name']);
        if (!in_array(strtolower($ext), array("doc","docx","pdf"))) {
            $this->json_exit(array("status"=>TRUE,"msg"=>"请上传doc,docx,pdf类型文件！"));
        }
        $size = $_FILES["download_file"]["size"]/1024;
        if ($size > 51200) { // 50M
            $this->json_exit(array("status"=>TRUE,"msg"=>"文件超过50M！"));   
        }
        $file_name = "download_file_".date('YmdHis').rand(1000,9999).".{$ext}";
        if ($this->riak_server->upload($file_name,file_get_contents($_FILES["download_file"]["tmp_name"]))) {
            $response = array("status"=>TRUE,"path"=>base_url("file/riak_get/{$file_name}"),"size"=>$size);
        }else{
            $response = array("status"=>FALSE,"msg"=>strip_tags($this->riak_server->error));
        }
        $this->json_exit($response);
    }

}