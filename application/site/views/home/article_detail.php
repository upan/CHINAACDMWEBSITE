<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="/assets/web/css/list.css" />
<div class="am-news-detail">
    <p class="img"><img src="<?php echo $article['thumb'];?>"></p>
    <h4 class="tit"><?php echo $article['title'];?></h4>
    <div class="issue-info">
        <?php if(empty(trim($article['author']))):?>
            <div class="issue-img">
                <img src="<?php echo empty($article['create_userphoto']) ? '/assets/web/images/list/img4.jpg' : $article['create_userphoto'];?>">
            </div>
            <span class="issue-user"><?php echo empty($article['create_username']) ? "ACDM管理员" : $article['create_username'];?></span>
        <?php else:?>
            <div class="issue-img"><img src="/assets/web/images/list/img4.jpg"></div>
            <span class="issue-user"><?php echo $article['author'];?></span>
        <?php endif;?>
        <span class="issue-time"><?php echo date("m月d日 H:i",$article['create_time']);?></span>
    </div>
    <div class="am-news-detail-cont">
        <?php echo $article['content'];?>
    </div>
    <div class="am-detial-other">
        <h4 class="other-tit">相关文章推荐</h4>
        <ul>
            <?php foreach ($related_articles as $key => $value):?>
            <li>
                <a href="<?php echo base_url("home/article/{$value['id']}");?>"> <?php echo $value['title'];?></a>
                <span class="time"><?php echo date("m月d日 H:i",$value['create_time']);?></span>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<script type="text/javascript">
//自定义高度
var setHgt = (function set(){
    var wndHgt = $(window).height(),
        offsetT = $('.am-news-detail').offset().top,
        footerH = $('.am-footer').height();
        $('.am-news-detail').css('min-height',wndHgt - offsetT - footerH);
    return set;
})();
$(window).resize(setHgt);
</script>