<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="/assets/web/css/list.css" />
<div class="am-download-wrap">
    <div class="am-download">
        <h4 class="download-tit">相关下载</h4>
        <ul>
            <?php foreach ($list as $key => $value):?>
                <li>
                    <span class="img"><img src="<?php echo empty($value['thumb']) ? '/assets/web/images/list/img3.jpg' : $value['thumb'];?>"></span>
                    <div class="top">
                        <a class="download-btn" href="<?php echo base_url("home/download/{$value['id']}");?>"><i></i>下载</a>
                        <p class="tit"><?php echo $value['title'];?></p>
                    </div>
                    <p class="info"><?php echo msubstr($value['description'],75);?></p>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="zh-page zh-mt-30">
        <div class="zh-page-content">
            <?php echo $pagesInfo;?>
        </div>
    </div>
</div>
<script type="text/javascript">
//自定义高度
var setHgt = (function set(){
    var wndHgt = $(window).height(),
        offsetT = $('.am-download-wrap').offset().top,
        footerH = $('.am-footer').height();
        $('.am-download-wrap').css('min-height',wndHgt - offsetT - footerH);
    return set;
})();
$(window).resize(setHgt);
</script>