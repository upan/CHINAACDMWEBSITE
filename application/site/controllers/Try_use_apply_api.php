<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 对外申请试用的接口
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-21T15:55:36+0800
*/
class Try_use_apply_api extends API_Controller {
    
    public function __construct(){
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        $this->limit_date = FALSE;// 是否限制申请时间
        $this->limit_min = 0;// 限制同IP x min 只能提交一次;
        $this->mail_conf = array(
            "config"    => array(
                'protocol'  => 'smtp',
                'charset'   => 'utf-8',
                'crlf'      => "\r\n",
                'newline'   => "\r\n",
                'smtp_crypto'=> 'ssl',
            ),
            "content"   => "姓名: [name] \r\n 公司：[company] \r\n 职位：[job] \r\n 电子邮件：[email] \r\n 电话：[phone] \r\n 留言内容：\r\n",
            //"receiver"  => "magenjie@feeyo.com,xutingting@variflight.com",
            "receiver"  => "aviation@variflight.com",
        );
    }
    
    // 需要防止多次提交 IP限制;是否要求登录;时间间隔限制;
    public function input(){
        $ip = ip();
        $cur_time = time();
        $start_date = "2017-08-21";
        $end_date = "2017-11-21";
        $start_time = strtotime("{$start_date} 00:00:00");
        $end_time = strtotime("{$end_date} 23:59:59");
        // 判断是否截止
        if ($this->limit_date && ($start_time > $cur_time || $end_time < $cur_time)) {
            $this->json_exit(array("status"=>FALSE,"msg"=>"申请时间为{$start_date} ~ {$end_date}"));
        }
        // 同一IP 5分钟内只能提交一次
        if ($this->limit_min) {
            $info = $this->db->select("*")->from("try_use_apply")->where(array("ip"=>$ip))->order_by("create_time","DESC")->get()->row_array();
            if ($info && ($cur_time <= $info["create_time"] + $this->limit_min*60)) {
                $this->json_exit(array("status"=>FALSE,"msg"=>"由于您刚刚申请过,请于{$this->limit_min}分钟后提交"));   
            }
        }
        $post = $this->I();
        $post = $this->new_addslashes($post);
        $data = $this->new_html_special_chars($post);
        if (!is_array($data) || array_keys($data) !== array("name","phone","email","job","company","content")) {
            $this->json_exit(array('status'=>FALSE,'msg'=>'数据参数有错误！'));
        }
        $data["ip"] = $ip;
        $data["create_time"] = $cur_time;
        $tag = $this->db->insert("try_use_apply",$data);
        if (!$tag) {
            $this->json_exit(array('status'=>FALSE,'msg'=>'数据提交失败！'));
        }
        // 发邮件;
        $conf = $this->db->select("*")->from("system_config")->where(array("name"=>"email_config"))->get()->row_array();
        $conf = json_decode($conf["value"],TRUE);
        $this->mail_conf['config'] = array_merge($this->mail_conf['config'],$conf);
        $this->load->library('email');
        $this->email->initialize($this->mail_conf['config']);
        foreach (explode(",", $this->mail_conf["receiver"]) as $email) {
            $replace = array(
                "[name]"    => $data["name"],
                "[company]" => $data["company"],
                "[job]"     => $data["job"],
                "[phone]"   => $data["phone"],
                "[content]" => $data["content"],
                "[email]"   => $data["email"],
            );
            $mail_content = strtr($this->mail_conf['content'],$replace);
            $this->email->from($this->mail_conf["config"]["smtp_user"], 'ACDM ADMIN');
            $this->email->to($email);
            $this->email->subject('ACDM 申请试用');
            $this->email->message($mail_content);
            $tag = $this->email->send();
            $tag ? : writeLog("mail_error","发送给 {$email} 的邮件失败;邮件内容：".var_export($mail_content,TRUE));
        }
        $this->json_exit(array("status"=>TRUE,"msg"=>"申请成功"));
    }
    
    /**
     * 返回经htmlspecialchars处理过的字符串或数组
     * @param $obj 需要处理的字符串或数组
     * @return mixed
     */
    function new_html_special_chars($string) {
        $encoding = 'utf-8';
        //if(strtolower(CHARSET)=='gbk') $encoding = 'ISO-8859-15';
        if(!is_array($string)) return htmlspecialchars($string,ENT_QUOTES,$encoding);
        foreach($string as $key => $val) $string[$key] = $this->new_html_special_chars($val);
        return $string;
    }
    
    /**
     * 返回经addslashes处理过的字符串或数组
     * @param $string 需要处理的字符串或数组
     * @return mixed
     */
    function new_addslashes($string){
        if(!is_array($string)) return addslashes($string);
        foreach($string as $key => $val) $string[$key] = $this->new_addslashes($val);
        return $string;
    }
}