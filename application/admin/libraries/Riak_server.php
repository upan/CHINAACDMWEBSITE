<?php
/**
* Riak 服务器;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-10-30T16:37:18+0800
*/
use \Riak\Output\KeyStreamOutput;
class KeyStreamer implements KeyStreamOutput {

    public function __construct() {
    }

    public function process($key) {
    }
}
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

    // 列出所有缓存.
    public function key_list(){
        try{
            $streamer = new KeyStreamer();
            $this->bucket->getKeyStream($streamer);
            var_dump($streamer);
        }catch(Exception $e){
            echo 'fail stream : '. $e->getMessage();
        }
        die;
    }

    public function upload($key = FALSE,$buffer_stream = FALSE){
        if (empty($key) || empty($buffer_stream)) {
            return FALSE;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($buffer_stream);
        $object = new \Riak\Object($key);
        $object->setContentType($mime_type);
        $object->setContent($buffer_stream);
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
}
