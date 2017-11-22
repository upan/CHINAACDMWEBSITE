<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 栏目 文章或下载 列表页 详情页;
* @author MAGENJIE(magenjie@feeyo.com)
* @datetime 2017-08-31T14:47:30+0800
*/
class Category extends Front_Controller {
    
    public function __construct(){
        parent::__construct();
    }

    // 列表页
    public function index($catid = 0){
        $catid = (int)$catid;
        empty($catid) && show_404();
        $page = $this->I("page",1);
        $navInfo = $this->m_home->get_one(array("id"=>$catid),"category");
        empty($navInfo) && show_404();
        switch ((int)$navInfo["type"]) {
            case 2:
                $table = "article";
                $template = "article_list";
                $fields = "t.id,t.catid,t.thumb,t.description,t.create_time,t.title,t.author,t.read,t.like,CONCAT(au.company,' ',au.truename) as create_username,au.photo as create_userphoto";
                break;
            case 3:
                $table = "download";
                $template = "download_list";
                $fields = "t.id,t.catid,t.title,t.thumb,t.author,t.description,t.create_time,t.path,CONCAT(au.company,' ',au.truename) as create_username,au.photo as create_userphoto";
                break;
            default:
                show_404();
                break;
        }
        $result = $this->m_home->get_content_list($catid,$fields,$page,$table);
        $hot_tags = $this->m_home->hot_tags();
        $hot_articles = $this->m_home->hot_articles();
        $data = array(
            "list"      => $result,
            "pagesInfo" => $this->m_home->pagesInfo,
            "hot_tags"  => $hot_tags,
            "hot_articles"  => $hot_articles,
        );
        $this->now_catid = $catid;
        $this->view($data,$template);
    }
    
}