<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* CHINA-ACDM 首页;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-31T14:47:30+0800
*/
class Home extends Front_Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){// 取出幻灯片;
        $result = $this->m_home->get_one(array("name"=>"imgscroll_config"),"system_config");
        $imgscroll = empty($result['value']) ? array() : json_decode($result['value'],TRUE);
        array_multisort(array_column($imgscroll,"sort"),SORT_ASC,SORT_NUMERIC,$imgscroll);
        $this->view(array('imgscroll'=>$imgscroll));
    }
    
    // 申请试用
    public function try_use_apply(){
        if ($this->input->is_ajax_request()) {
            $this->limit_date = FALSE;// 是否限制申请时间
            $this->limit_min = 5;// 限制同IP x min 只能提交一次;
            $this->mail_conf = array(
                "config"    => array(
                    'protocol'  => 'smtp',
                    'charset'   => 'utf-8',
                    'crlf'      => "\r\n",
                    'newline'   => "\r\n",
                    'smtp_crypto'=> 'ssl',
                ),
                "content"   => "姓名: [name] \r\n 公司：[company] \r\n 职位：[job] \r\n 电子邮件：[email] \r\n 电话：[phone] \r\n 留言内容：\r\n",
                "receiver"  => "magenjie@feeyo.com,xutingting@variflight.com,aviation@variflight.com",
            );
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
            $post = new_addslashes($post);
            $data = new_html_special_chars($post);
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
            $this->json_exit(array("status"=>TRUE,"msg"=>"申请成功！稍后我们会有专人联系您;"));
        }else{
            $this->view();    
        }
    }
    
    //单页：
    public function page($file = ""){
        if (empty($file)) {
            show_404();
        }
        $this->view(array(),$file);
    }
    
    // 文章详情页;
    public function article($id = 0){
        $id = (int)$id;
        empty($id) && show_404();
        $article = $this->m_home->get_article_detail($id);
        empty($article) && show_404();
        $related_articles = $this->m_home->related_articles(0,$id);
        $data = array(
            "article" => $article,
            "related_articles" => $related_articles,
        );
        //更新下 read
        $this->m_home->update(array('read'=>(int)($article['read'] + 1)),array("id"=>$id),'article');
        $this->now_catid = $article['catid'];
        $this->view($data,"article_detail");
    }
    
    // 文章搜索;
    public function search(){
        
    }

    // 统计下载次数;
    public function download($id = 0){
        $id = (int)$id;
        empty($id) && show_404();
        $info = $this->m_home->get_one(array('id'=>$id),'download');
        (empty($info) || empty($info['path'])) && show_404();
        $this->m_home->update(array('down'=>++$info['down']),array('id'=>$id),'download');
        redirect(base_url($info['path']));
    }
}