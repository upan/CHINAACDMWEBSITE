<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 通用网站设置;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-25T14:47:28+0800
*/
class System_setting extends Admin_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model("System_setting_model","m_system_setting");
    }
    
    public function index(){
        $list = $this->m_system_setting->whole_select();
        $config = array_column($list,NULL,"name");
        foreach ($config as $key => $value) {
            $value["value"] = json_decode($value["value"],TRUE);
            $config[$key] = $value;    
        }
        empty($config["site_config"]) && $config['site_config'] = $this->m_system_setting->_default_system_config("网站配置","site_config");
        empty($config["email_config"]) && $config['email_config'] = $this->m_system_setting->_default_system_config("邮件配置","email_config");
        empty($config["imgscroll_config"]) && $config['imgscroll_config'] = $this->m_system_setting->_default_system_config("首页幻灯片","imgscroll_config",FALSE);
        $this->view(array("config"=>$config));
    }
    
    //更新配置信息;
    public function update_system_config(){
        $post = $this->I();
        if (array_keys($post) !== array("id","title","name","value","update_time")) {
            $this->json_exit();
        }
        $post["update_time"] = time();
        $post["value"] = json_encode($post["value"]);
        $config = $this->m_system_setting->get_one(array("name"=>$post['name']));
        if (empty($config)) {
            $tag = $this->m_system_setting->insert($post);
        }else{
            $tag = $this->m_system_setting->update($post,array("name"=>$post['name']));
        }
        $response = $this->_format_response($tag,"更新系统配置 \"{$post['title']}\"");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }
    
    // 首页幻灯上传;
    public function upload_pic(){
        list($name,$ext) = explode(".",$_FILES['imgscroll']['name']);
        $file = date('YmdHis').rand(10,99).".{$ext}";
        $dirPath = "upload/imgscroll/";
        // if (! is_dir($dirPath)) {
        //     $rc1 = mkdir($dirPath, 0777);
        //     $rc2 = chmod($dirPath, 0777);
        // }
        $path = "/{$dirPath}{$file}";
        //========================================
        $config['file_name'] = $file;
        $config['upload_path'] = $dirPath;
        $config['allowed_types'] = 'jpg|gif|png';
        $config['remove_spaces'] = true;
        $config['max_size'] = 10240;//1M
        //===========================================
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload("imgscroll")){
            $response = array("status"=>FALSE,"error"=>strip_tags($this->upload->display_errors()));
        }else{
            $response = array("status"=>TRUE,"path"=>$path);
        }
        $this->json_exit($response);
    }

    public function delete_pic(){
        $this->json_exit($response);   
    }

    public function friend_link(){
        $list = $this->m_system_setting->whole_select("friend_link");
        $this->view(array("list"=>$list));   
    }
    
    public function friend_link_add($id = 0){
        $post = $this->I();
        if ($post) {
            $id = isset($post["id"]) ? (int)$post["id"] : 0;
            unset($post["id"]);
            $post["update_time"] = time();
            if ($id) {
                $tag = $this->m_system_setting->update($post,array("id"=>$id),"friend_link");
            }else{
                $tag = $this->m_system_setting->insert($post,"friend_link");
            }
            $response = $this->_format_response($tag,"友情链接 \"{$post['name']}\"",$id);
            $this->_log_desc = $response["msg"];
            $this->json_exit($response);
        }
        $id = (int)$id;
        if ($id) {
            $friend_link = $this->m_system_setting->get_one(array("id"=>$id),"friend_link");
            empty($friend_link["start_time"]) && $friend_link["start_time"] = "";
            empty($friend_link["end_time"]) && $friend_link["end_time"] = "";
        }else{
            $friend_link = $this->m_system_setting->_default_friend_link();  
        }
        $data = array(
            "friend_link"   => $friend_link,
        );
        $this->view($data);
    }

    public function friend_link_delete($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $friend_link = $this->m_system_setting->get_one(array("id"=>$id),"friend_link");
        if (empty($friend_link)) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"该条记录已不存在！"));
        }
        $tag = $this->m_system_setting->delete(array("id"=>$id),"friend_link");
        $response = $this->_format_response($tag,"删除友情链接{$friend_link['name']}");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }
}