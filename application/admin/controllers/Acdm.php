<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* ACDM 基础数据维护;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-09-14T14:38:10+0800
*/
class Acdm extends Admin_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("Acdm_basedata_model","m_acdm");
        $this->load->model("System_setting_model","m_system_setting");
    }

    public function index(){
        $list = $this->m_acdm->whole_select("member_airport","member_airport_id","member_airport_id desc");
        $this->view(array("list"=>$list));
    }

    //编辑添加机场信息;
    public function add_airport($id = 0){
        if ($this->input->is_ajax_request()) {
            $post = $this->I();
            $required_params = array_keys($this->m_acdm->_default_member_airport());
            $data = check_params_validity($required_params,$post);
            if ($data === FALSE || !empty($data["lost"])) {
                $this->json_exit();
            }
            if($id){
                $data["update_time"] = time();
                $tag = $this->m_acdm->update($data,array('member_airport_id'=>$id),'member_airport');
            }else{
                $data["create_time"] = $data["update_time"] = time();
                $tag = $this->m_acdm->insert($data,'member_airport');
            }
            $response = $this->_format_response($tag,"{$data['cn_name']}",$id);
            $this->_log_desc = $response["msg"];
            $this->json_exit($response);
        }else{
            $data = $id ? $this->m_acdm->get_one(array("member_airport_id"=>$id),"member_airport") : $this->m_acdm->_default_member_airport();
            $this->view(array('data'=>$data));
        }
    }

    public function delete_airport($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $airport_info = $this->m_acdm->get_one(array("member_airport_id"=>$id),"member_airport");
        empty($airport_info) && $this->json_exit(array("status"=>FALSE,"msg"=>"成员机场不存在"));
        $tag = $this->m_acdm->delete(array('member_airport_id'=>$id),'member_airport');
        $response = $this->_format_response($tag,"删除成员机场 {$airport_info["cn_name"]}");
        $this->_log_desc = $response["msg"]." 成员机场三字码: {$airport_info['airport_iata']}";
        $this->json_exit($response);
    }

    public function disabled_airport($id = 0){
        $id = (int)$id;
        !$id && $this->json_exit();
        $airport_info = $this->m_acdm->get_one(array('member_airport_id'=>$id),'member_airport');
        empty($airport_info) && $this->json_exit();
        $status = abs(1-$airport_info['status']);
        $tag = $this->m_acdm->update(array('status'=>$status),array('member_airport_id'=>$id),'member_airport');
        $this->_log_desc = array(
            "desc" => "更改{$airport_info['cn_name']} ACDM成员机场状态值 为{$status}",
            "status" => $tag
        );
        $this->json_exit(array("status"=>$tag));
    }

    //更新客户端类型等配置;
    public function update_app_type(){
        $post = $this->I();
        $data = check_params_validity(array("id","name","description"),$post);
        if ($data === FALSE || !empty($data["lost"])) {
            $this->json_exit();
        }
        if (empty($data["id"]) || empty($data["name"])) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"请填写必填项！"));
        }
        $default_config = $this->m_system_setting->_default_system_config("APP类型","app_type_config");
        $config = $this->m_system_setting->get_one(array("name"=>"app_type_config"));
        if (empty($config)) { //insert   
            $default_config["value"] = json_encode(array( $data["id"] => $data));
            $default_config["update_time"] = time();    
            $tag = $this->m_system_setting->insert($default_config);            
        }else{ // update   
            $value = json_decode($config["value"],TRUE);
            $value[$data["id"]] = $data;
            $tag = $this->m_system_setting->update(array("value"=>json_encode($value)),array("name"=>"app_type_config"));
        }
        $response = $this->_format_response($tag,"更新ACDM客户端类型数据");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }

    public function delete_app_type($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $config = $this->m_system_setting->get_one(array("name"=>"app_type_config"));
        if (empty($config) || empty($config["value"])) {
            $this->json_exit();
        }
        $value = json_decode($config["value"],TRUE);
        if (empty($value) || !is_array($value) || !isset($value[$id])) {
            $this->json_exit();
        }
        // 判断是否有该APP的版本信息;有则禁止删除;
        $exist_version = $this->m_acdm->simple_select(array("type"=>$id),"id desc","app_version");
        if ($exist_version) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"请先将APP版本列表中关于{$value[$id]["name"]}的信息调整或删除"));
        }
        unset($value[$id]);
        $tag = $this->m_system_setting->update(array("value"=>json_encode($value)),array("name"=>"app_type_config"));
        $response = $this->_format_response($tag,"删除ACDM客户端类型:{$value[$id]["name"]}");
        $this->_log_desc = $response["msg"];
        $this->json_exit($response);
    }

    // app 客户端类型;
    public function app_list(){
        $result = $this->m_system_setting->get_one(array("name"=>"app_type_config"));
        $list = (empty($result)||empty($result["value"])) ? array() : json_decode($result["value"],TRUE);

        $this->view(array(
            "list" => $list,
            "fields" => $this->m_acdm->_default_app_type(),
        ));
    }

    // app 版本管理;
    public function version_list(){
        $result = $this->m_system_setting->get_one(array("name"=>"app_type_config"));
        $app_type = (empty($result)||empty($result["value"])) ? array() : json_decode($result["value"],TRUE);
        $list = $this->m_acdm->whole_select("app_version");
        $this->view(array(
            "list" => $list,
            "app_type" => $app_type,
        ));
    }

    public function add_version($id = 0){
        if ($this->input->is_ajax_request()) {
            $post = $this->I();
            $required_params = array_keys($this->m_acdm->_default_app_version());
            $data = check_params_validity($required_params,$post);
            if ($data === FALSE || !empty($data["lost"])) {
                $this->json_exit();
            }
            $data["update_time"] = time();
            if($id){
                $tag = $this->m_acdm->update($data,array('id'=>$id),'app_version');
            }else{
                $tag = $this->m_acdm->insert($data,'app_version');
            }
            $response = $this->_format_response($tag,"APP版本",$id);
            $this->_log_desc = $response["msg"];
            $this->json_exit($response);
        }else{
            $data = $id ? $this->m_acdm->get_one(array("id"=>$id),"app_version") : $this->m_acdm->_default_app_version();
            $result = $this->m_system_setting->get_one(array("name"=>"app_type_config"));
            $app_type = (empty($result)||empty($result["value"])) ? array() : json_decode($result["value"],TRUE);
            $airports_list = $this->m_acdm->whole_select("member_airport","member_airport_id","member_airport_id desc");
            $this->view(array('data'=>$data,'app_type'=>$app_type,'airports_list'=>$airports_list));
        }
    }
    
    public function delete_version($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $version_info = $this->m_acdm->get_one(array("id"=>$id),"app_version");
        if (empty($version_info)) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"版本信息不存在"));
        }
        $tag = $this->m_acdm->delete(array('id'=>$id),'app_version');
        $response = $this->_format_response($tag,"删除版本 {$version_info["name"]}");
        $this->_log_desc = $response["msg"]." 版本ID: {$id}";
        $this->json_exit($response);
    }

    public function disabled_version($id = 0){
        $id = (int)$id;
        !$id && $this->json_exit();
        $version_info = $this->m_acdm->get_one(array('id'=>$id),'app_version');
        empty($version_info) && $this->json_exit();
        $status = abs(1-$version_info['status']);
        $tag = $this->m_acdm->update(array('status'=>$status),array('id'=>$id),'app_version');
        $this->_log_desc = array(
            "desc" => "更改{$version_info['name']} APP版本状态值 为{$status}",
            "status" => $tag
        );
        $this->json_exit(array("status"=>$tag));
    }

    public function get_airport_info_by_iata($iata = FALSE){
        empty($iata) && $this->json_exit();
        $result = $this->db->select("*")->from("airport")->where(array("airport_iata"=>$iata))->get()->row_array();
        $response = array("status"=>FALSE);
        if ($result) {
            $response = array(
                "status" => TRUE,
                "data" => array(
                    "airport_iata"  => $result["airport_iata"],
                    "airport_icao"  => $result["airport_icao"],
                    "cn_name"       => $result["cn_name"],
                    "cn_name_short" => $result["cn_name_short"],

                ),
            );
        }
        $this->json_exit($response);
    }
}