<?php
/**
* Riak 服务器;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-10-30T16:37:18+0800
*/
class Riak_server{
    public $ip = "10.46.1.11";  
    public $port = 8087;
    public $bucket_name = "chinaacdm";
    public $error = "";
    public function __construct(){
        try{
            $conn = new \Riak\Connection( $this->ip, $this->port );
        }catch(\Riak\Exception\ConnectionException $e){
            $this->error = 'fail connect : '. $e->getMessage();
            return FALSE;
        }
        $this->bucket = new \Riak\Bucket($conn, $this->bucket_name);
        if (!$this->bucket) {
            return FALSE;
        }

        // 设置属性,关闭siblings  
        $newProps = new \Riak\BucketPropertyList();  
        $newProps->setAllowMult(0);  
        $this->bucket->setPropertyList($newProps);
    }

    //上传文件;
    public function upload($key = FALSE,$field = FALSE){
        if (empty($key) || empty($field)) {
            return FALSE;
        }
        // 文件拓展名
        $object = new \Riak\Object($key);
        $object->setContentType($_FILES[$field]["type"]);
        $object->setContent(file_get_contents($_FILES[$field]["tmp_name"]));
        //设置文件类型；
        $ret = $this->bucket->put($object);
        return TRUE;
    }

    //删除文件;
    public function delete($key = FALSE){
        if (empty($key)) {
            return FALSE;
        }
        $content = $this->bucket->get($key);
        if ($content->hasObject()) {
            $this->bucket->delete($key);
            return TRUE;
        }else{
            return FALSE;
        }
    }

    // 获取文件;
    public function get($key = FALSE){
        if (empty($key)) {
            return FALSE;
        }
        $content = $this->bucket->get($key);
        if ($content->hasObject()) {
            $object = $content->getFirstObject();
            header("Content-type:".$object->getContentType());
            echo $object->getContent();
        } else { 
            echo "";
        }
    }

    // private function json_exit($response = array("status"=>FALSE,"msg"=>"操作失败！"),$is_unescaped = FALSE){
    //     if (empty($response["msg"]) && isset($response["status"])) {
    //         $response["msg"] = $response["status"] ? "操作成功！" : "操作失败！";
    //     }
    //     header('Cache-Control: no-cache, must-revalidate');
    //     header("Content-Type: text/plain; charset=utf-8");
    //     if ($is_unescaped) {
    //         echo json_encode($response,JSON_UNESCAPED_UNICODE);
    //     }else{
    //         echo json_encode($response);
    //     }
    //     exit;
    // }

    // private function I($var = '', $default = NULL){
    //     $get = $this->input->get(NULL, TRUE);
    //     $post = $this->input->post(NULL, TRUE);
    //     if(empty($var)){
    //         if(!empty($get)){
    //             return $get;
    //         }elseif(!empty($post)){
    //             return $post;
    //         }else{
    //             return $default;
    //         }
    //     }else{
    //         if(array_key_exists($var, $get)){
    //             return $get[$var];
    //         }elseif(array_key_exists($var, $post)){
    //             return $post[$var];
    //         }else{
    //             return $default;
    //         }
    //     }
    // }
}
