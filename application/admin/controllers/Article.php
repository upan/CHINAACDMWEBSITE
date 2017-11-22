<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 网站文章;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-25T11:10:58+0800
*/
class Article extends Admin_Controller {
    public $category = array();
    public function __construct(){
        parent::__construct();
        $this->load->model("article_model","m_article");
        $this->load->library("tree");
        $reuslt = $this->m_article->simple_select(array("status"=>1,"type"=>2),"id desc","category");
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
        $list = $this->m_article->search_listinfo($where,$like,$page,'id asc');
        $data = array(
            "filter"    => $filter,
            "list"      => $list,
            "pagesInfo" => $this->m_article->pagesInfo,
        );
        $this->view($data);
    }
    
    public function add($id = 0){
        if ($this->input->is_ajax_request()) {
            $post = $this->I();
            $id = isset($post["id"]) ? (int)$post["id"] : 0;
            $post["update_time"] = time();
            unset($post["id"]);
            $tags = array_unique(array_filter(explode(",", $post["keywords"])));
            $add_tags = $delete_tags = $update_data = $article_tags = $insert_data = array();
            if ($id) {
                // 判断文章已有的标签,与文章是否存在;
                $articleInfo = $this->m_article->get_one(array("id"=>$id));
                if (empty($articleInfo)) {
                    $this->json_exit();
                }
                $article_tags = array_unique(array_filter(explode(",", $articleInfo["keywords"])));
                $add_tags = array_diff($tags,$article_tags);
                $delete_tags = array_diff($article_tags, $tags);
                $post["update_uid"] = $this->session->uid;
                $result = $this->m_article->update($post,array("id"=>$id));
            }else{
                $add_tags = $tags;
                $post["create_time"] = time();
                $post["create_uid"] = $this->session->uid;
                $result = $this->m_article->insert($post);
                $id = (int)$result;
            }
            // 标签是否存在;更新标签数字;
            if ($result) {
                $tags_result = $this->m_article->where_in(array_unique(array_merge($tags,$article_tags)),"name","article_tags","*","name");

                $diff_tags = array_diff($tags,array_column($tags_result,"name"));
                if (!empty($diff_tags)) {
                    foreach ($diff_tags as $diff_tag) {
                        $insert_data[] = array(
                            "num"   => 0,
                            "name"  => $diff_tag,
                            "article_ids" => "",
                        );
                    }
                    $insert_data && $this->m_article->insert_batch($insert_data,'article_tags');
                }

                foreach ($delete_tags as $delete_tag) {
                    $article_ids = $tags_result[$delete_tag]["article_ids"];
                    $article_ids_flip = empty($article_ids) ? array() : array_flip(explode(",", $article_ids));
                    unset($article_ids_flip[$id]);
                    $update_data[] = array(
                        "name"  => $delete_tag,
                        "num"   => (int)($tags_result[$delete_tag]["num"] - 1),
                        "article_ids" => join(",",array_flip($article_ids_flip)),
                    );
                }

                foreach ($add_tags as $add_tag) {
                    $article_ids = $tags_result[$add_tag]["article_ids"];
                    $article_ids = empty($article_ids) ? $id : "{$article_ids},{$id}";
                    $update_data[] = array(
                        "name"  => $add_tag,
                        "num"   => (int)($tags_result[$add_tag]["num"] + 1),
                        "article_ids" => $article_ids,
                    );
                }
                $update_data && $this->m_article->update_batch($update_data,"article_tags","name");
            }
            $response = $this->_format_response($result,"文章 \"{$post['title']}\"",$id);
            $this->_log_desc = $response["msg"];
            $this->json_exit($response);
        }else{
            $id = (int)$id;
            if ($id) {
                $article = $this->m_article->get_one(array("id"=>$id));
            }else{
                $article = $this->m_article->_default();
            }
            $data = array(
                "article"   => $article,
                "tags"      => $this->m_article->whole_select("article_tags"),
            );
            $this->view($data);
        }
    }
    
    // 软删除;
    public function delete($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $article = $this->m_article->get_one(array("id"=>$id));
        if (empty($article)) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"Sorry! 文章不存在"));
        }
        $tag = $this->m_article->delete(array("id"=>$id));
        $response = $this->_format_response($tag,"删除文章[{$article["id"]}] {$article['title']}");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }

    // 关闭 隐藏 存草稿;
    public function disabled($id = 0){
        $id = (int)$id;
        !$id && $this->json_exit();
        $articleInfo = $this->m_article->get_one(array('id'=>$id));
        empty($articleInfo) && $this->json_exit();
        $status = abs(1 - $articleInfo['status']);
        $tag = $this->m_article->update(array('status'=>$status),array('id'=>$id));
        $this->_log_desc = array(
            "desc" => "更改文章 {$articleInfo['name']} 状态值 为{$status}",
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
    //     $path = "/upload/article_thumb/{$file}";
    //     //===========================================
    //     $config['file_name'] = $file;
    //     $config['upload_path'] = "upload/article_thumb/";
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
        $file_name = "article_thumb_".date('YmdHis').rand(1000,9999).".{$ext}";
        if ($this->riak_server->upload($file_name,file_get_contents($_FILES["thumb"]["tmp_name"]))) {
            $response = array("status"=>TRUE,"path"=>base_url("file/riak_get/{$file_name}"));
        }else{
            $response = array("status"=>FALSE,"msg"=>strip_tags($this->riak_server->error));
        }
        $this->json_exit($response);
    }
}