<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author MAGENJIE(magenjie@feeyo.com)
 * @datetime 2017-08-23T15:50:18+0800
 */
class Auth extends Admin_Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function auth_list(){
        $auth_list = $this->m_admin->simple_select(array('status'=>1),'sort asc','admin_auth','id');
        $second_nav = $this->m_admin->simple_select(array('status'=>1,'pid<>'=>0),'sort asc','admin_nav');
        $list = array();
        foreach ($second_nav as $value) {
            $pid = $value['pid'];
            $parent = $this->m_admin->simple_select(array('id'=>$pid),'sort asc','admin_nav');
            $info = array('first'=>$parent[0]['name'],'child'=>array());
            foreach ($auth_list as $val) {
                if ($value['id'] == $val['pid']){
                    $val['parent'] = $value['name'];
                    $parent_node = empty($val['parent_node']) ? array() : array_map("intval",explode(",",$val["parent_node"]));
                    $parent_node_name = $related_node_name = "";
                    foreach ($parent_node as $parent_node_id) {
                        if (isset($auth_list[$parent_node_id])) {
                            $parent_node_name .= empty($parent_node_name) ? $auth_list[$parent_node_id]['name'] : ", ".$auth_list[$parent_node_id]['name'];
                        }else{
                            $parent_node_name .= empty($parent_node_name) ? "<span class='text-red'>父节点{$parent_node_id}不存在</span>" : ", <span class='text-red'>父节点{$parent_node_id}不存在</span>";
                        }
                    }
                    $related_node = empty($val['related_node']) ? array() : array_map("intval",explode(",",$val["related_node"]));
                    foreach ($related_node as $related_node_id) {
                        if (isset($auth_list[$related_node_id])) {
                            $related_node_name .= empty($related_node_name) ? $auth_list[$related_node_id]['name'] : ", ".$auth_list[$related_node_id]['name'];
                        }else{
                            $related_node_name .= empty($related_node_name) ? "<span class='text-red'>关联节点{$related_node_id}不存在</span>" : ", <span class='text-red'>关联节点{$related_node_id}不存在</span>";
                        }   
                    }
                    $val['parent_node_name'] = $parent_node_name;
                    $val['related_node_name'] = $related_node_name;
                    $info['child'][] = $val;
                }
            }
            if (isset($list[$pid])) {
                $list[$pid]['child'] = array_merge($list[$pid]['child'],$info['child']);   
            }else{
                $list[$pid] = $info;
            }
        }
        $this->view(array("list"=>$list));
    }
    
    public function auth_add($id = 0){
        $id = (int)$id;
        if ($this->input->is_ajax_request()) {
            $post = $this->I();
            isset($post['id']) && $id = (int)trim($post['id']);
            $post["parent_node"] = in_array(0,$post["parent_node"]) ? 0 : join(",",array_unique($post["parent_node"]));
            $post["related_node"] = in_array(0,$post["related_node"]) ? 0 : join(",",array_unique($post["related_node"]));
            unset($post['id']);
            isset($post['uri']) && $post['uri'] = strtolower($post['uri']);
            if($id){
                $tag = $this->m_admin->update($post,array('id'=>$id),'admin_auth');
            }else{
                $tag = $this->m_admin->insert($post,'admin_auth');
            }
            $this->json_exit(array("status"=>$tag));
        }else{
            if($id){
                $info = $this->m_admin->get_one(array('id'=>$id),'admin_auth');
                $info['parent_node'] = explode(",",$info['parent_node']);
                $info['related_node'] = explode(",",$info['related_node']);
            }else{//默认是站点控制
                $info = array(
                    "id"            => 0,
                    "name"          => "",
                    "uri"           => "",
                    "sort"          => 255,
                    "pid"           => 8,
                    "auth_limit"    => 1,
                    "status"        => 1,
                    "is_left_nav"   => 1,
                    "parent_node"   => array(0),
                    "related_node"  => array(0)
                );
            }
            if (empty($info)) redirect(base_url('auth/node_list'));
            $auth = $this->m_admin->simple_select(array('status'=>1,),'id desc','admin_auth');
            $second_nav = $this->m_admin->simple_select(array('pid<>'=>0,'status'=>1),'sort asc','admin_nav');
            $data = array(
                "auth"  => $auth,
                "info"  => $info,
                "second_nav" => $second_nav,
            );
            $this->view($data);
        }
    }

    public function auth_del($id=0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $authInfo = $this->m_admin->get_one(array("id"=>$id),"admin_auth");
        empty($authInfo) && $this->json_exit();
        $tag = $this->m_admin->delete(array('id'=>$id),'admin_auth');
        $response = $this->_format_response($tag);
        $this->json_exit($response);
    }
    
    //禁用/启用权限节点
    public function auth_disabled($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $authInfo = $this->m_admin->get_one(array('id'=>$id),'admin_auth');
        empty($authInfo) && $this->json_exit();
        $status = abs(1-$authInfo['status']);
        $tag = $this->m_admin->update(array('status'=>$status),array('id'=>$id),'admin_auth');
        $this->json_exit(array("status"=>$tag));
    }

    public function group_list(){
        $list = $this->m_admin->whole_select('admin_group','id','id asc');
        $this->view(array("list"=>$list));
    }

    public function group_add($id = 0){
        $id = (int)$id;
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            $id = isset($post['id']) ? (int)$post['id'] : 0;
            if (isset($post["auth"])) {
                sort($post["auth"]);
                $post['auth'] = join(",",$post['auth']);
            }else{
                $post['auth'] = "";
            }
            unset($post['id']);
            $id === 1 && $this->json_exit(array("status"=>FALSE,"msg"=>"系统默认角色分组,禁止操作！"));
            if($id){
                $tag = $this->m_admin->update($post,array('id'=>$id),'admin_group');
            }else{
                $tag = $this->m_admin->insert($post,'admin_group');
            }
            $this->_log_desc = array(
                "desc"      => "添加用户角色分组{$post['name']}",
                "status"    => $tag,
            );
            $this->json_exit(array("status"=>$tag));
        }else{
            if($id){
                $info = $this->m_admin->get_one(array('id'=>$id),'admin_group');
                $info['auth'] = explode(",",$info['auth']);
            }else{
                $info = array("name"=>"","status"=>1,"auth"=>array(),"id"=>0);
            }
            if (empty($info)) redirect(base_url('auth/group_list'));
            $auth_list = $this->m_admin->simple_select(array('status'=>1,),'id desc','admin_auth');
            $second_nav = $this->m_admin->simple_select(array('status'=>1,'pid<>'=>0),'id desc','admin_nav');
            
            $list = $categroy = array();
            foreach ($second_nav as $key => $value) {
                // 超管判断 权限管理不显示在分组权限中 只有超级管理员才有权限
                if ($value['pid'] == 2) {
                    continue;
                }
                $list[$key] = $value;
                if ($value['id'] == 2) {
                    continue;
                }
                $categroy[$value['id']] = array(); 
                foreach ($auth_list as $ke => $val) {
                    if ($val['parent_node']) { // 与关联节点权限绑定 不需要出现在勾选列表中;
                        continue;
                    }
                    if ($value['id'] == $val['pid']) {
                        $list[$key]['child'][] = $val;
                        $categroy[$value['id']][] = $val['id'];
                    }
                }
            }
            $data = array(
                "auth"      => $list,
                "info"      => $info,
                "categroy"  => $categroy,
                "system"    => $this->m_login->systemDefaultAuth()
            );
            $this->view($data);
        }
    }

    public function group_del($id=0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $tag = $this->m_admin->delete(array('id'=>$id),'admin_group');
        $response = $this->_format_response($tag,"角色分组删除");
        $this->_log_desc = $response['msg']." 分组ID: {$id}";
        $this->json_exit($response);
    }
    
    //禁用/启用角色分组
    public function group_disabled($id = 0){
        $id = (int)$id;
        empty($id) && $this->json_exit();
        $groupInfo = $this->m_admin->get_one(array('id'=>$id),'admin_group');
        empty($groupInfo) && $this->json_exit();
        $status = abs(1 - $groupInfo['status']);
        $tag = $this->m_admin->update(array('status'=>$status),array('id'=>$id),'admin_group');
        $this->_log_desc = array(
            "desc"      => "将{$groupInfo['name']}分组状态置为{$status}",
            "status"    => $tag
        );
        $this->json_exit(array("status"=>$tag));
    }
    
}