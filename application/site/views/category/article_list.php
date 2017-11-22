<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="/assets/web/css/list.css" />
<div class="am-news-banner">
    <img src="/assets/web/images/list/img1.jpg">
    <!-- <div class="am-news-search">
        <input class="text" type="text" placeholder="请输入你感兴趣的内容">
        <span class="search-icon"></span>
        <input class="am-btn" type="button" value="确 定">
    </div> -->
</div>
<div class="am-news-main clearfix">
    <div class="am-news-left">
        <?php foreach ($list as $key => $value):?>
            <div class="am-news-list">
                <div class="list-top">
                    <?php if(empty(trim($value['author']))):?>
                        <span class="img">
                            <img src="<?php echo empty($value['create_userphoto']) ? '/assets/web/images/list/img2-1.jpg' : $value['create_userphoto'];?>">
                        </span>
                        <span class="tit"><?php echo empty($value['create_username']) ? "ACDM管理员" : $value['create_username'];?></span>
                    <?php else:?>
                        <span class="img">
                            <img src="/assets/web/images/list/img2-1.jpg">
                        </span>
                        <span class="tit"><?php echo $value['author'];?></span>
                    <?php endif;?>
                    <span class="time"><?php echo date("m月d日 H:i",$value["create_time"]);?></span>
                </div>
                <a href="<?php echo base_url("home/article/{$value['id']}");?>">
                    <span class="list-img"><img src="<?php echo $value['thumb'];?>"></span>
                    <span class="list-tit"><?php echo $value["title"];?></span>
                    <span class="list-info"><?php echo msubstr($value['description'],100);?></span>
                </a>
            </div>
        <?php endforeach;?>
        <div class="zh-page zh-mt-30 zh-mb-30">
            <div class="zh-page-content">
                <?php echo $pagesInfo;?>
            </div>
        </div>
    </div>
    <div class="am-news-right">
        <div class="am-news-hot">
            <h4 class="tit">热门文章</h4>
            <ul class="list">
                <?php foreach ($hot_articles as $key => $article):?>
                    <li>
                        <span class="num"><?php echo ++$key;?></span>
                        <a href="<?php echo base_url("home/article/{$article['id']}");?>"><?php echo $article["title"];?></a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <!-- <div class="am-news-hot">
            <h4 class="tit">热门标签</h4>
            <div class="tag">
                <?php foreach ($hot_tags as $tag):?>
                    <a href="#"><?php echo $tag["name"];?></a>    
                <?php endforeach;?>
            </div>
        </div> -->
    </div>
</div>
<script type="text/javascript">
//enter键搜索
$(document).on('keyup',function(e){
    // if(e.keyCode == 13 && $(document.activeElement).parents('.am-news-search').size() > 0){
    //     alert('搜索');
    // }
});
</script>