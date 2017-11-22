<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="am-banner">
    <div class="img-box">
        <a class="active" href="#"><img src="/assets/web/images/index/img1.jpg"></a>
        <a href="#"><img src="/assets/web/images/index/img2.jpg"></a>
        <a href="#"><img src="/assets/web/images/index/img3.jpg"></a>
    </div>
    <div class="num-box"><i class="active"></i><i></i><i></i></div>
</div>
<div class="am-page">
    <div class="am-page-width">
        <h2><span>CHINA ACDM 概述</span></h2>
        <p style="text-indent:30px;">CHINA ACDM系统是面向未来的新一代机场智能运行协同决策系统。用大数据和人工智能技术，通过对航班运行相关数据的收集、分析和智能决策，帮助机场建设大数据中心，从而提升机场地面运行效率，提高航班正点率。</p>
    </div>
    <div class="page-cont left-right">
        <div class="am-page-width">
            <div class="page-cont-text">
                <p class="line line-t"></p>
                <p>在完善数据收集的基础上，结合机场资源和人员调度，还拓展开发了</p>
                <h5>机位智能分配系统</h5>
                <h5>智慧地服调度等系统</h5>
                <p>通过人工智能，自动对资源和人员进行调度和优化，提升机位、廊桥、车辆等地面资源和人员的利用率。</p>
                <p class="line line-b"><span class="dot"><i></i><i></i><i></i><i></i></span></p>
            </div>
            <div class="page-cont-img">
                <img src="/assets/web/images/index/img4-1.jpg"/>
                <img src="/assets/web/images/index/img4-2.jpg"/>
                <img src="/assets/web/images/index/img4-3.jpg"/>
            </div>
        </div>
    </div>
    <div class="page-cont right-left">
        <div class="am-page-width">
            <div class="page-cont-text">
                <p class="line line-t"></p>
                <p>截至2017年，CHINA ACDM已经覆盖超</p>
                <h5>50家机场</h5>
                <h5>惠及旅客3.2亿人次</h5>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;占中国民航旅客吞吐量的四分之一<br/>预计到2018年，CHINA ACDM系统<br/>解决方案将被国内超过100家机场采用。</p>
                <p class="line line-b"><span class="dot"><i></i><i></i><i></i><i></i></span></p>
            </div>
            <div class="page-cont-img">
                <img src="/assets/web/images/index/img5-1.jpg"/>
                <img src="/assets/web/images/index/img5-2.jpg"/>
                <img src="/assets/web/images/index/img5-3.jpg"/>
            </div>
        </div>
    </div>
    <div class="page-cont left-right">
        <div class="am-page-width">
            <div class="page-cont-text">
                <p class="line line-t"></p>
                <p>在空管领域，飞常准准备通过新技术帮<br/>助空管提高</p>
                <h5>空中冲突的预警准确度</h5>
                <h5>空管运行的安全和效率</h5>
                <p class="line line-b"><span class="dot"><i></i><i></i><i></i><i></i></span></p>
            </div>
            <div class="page-cont-img">
                <img src="/assets/web/images/index/img6-1.jpg"/>
                <img src="/assets/web/images/index/img6-2.jpg"/>
                <img src="/assets/web/images/index/img6-3.jpg"/>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function bannerFn(){
    $('body').on('mouseover','.num-box i',function(){
        var _this = $(this);
        _this.removeClass('out');
        setTimeout(function(){
            _this.siblings('i').removeClass('out');
        });
        _this.addClass('active').siblings('i').removeClass('active');
        $('.am-banner .img-box a').eq(_this.index()).addClass('active').siblings().removeClass('active');
    }).on('mouseout','.num-box i',function(){
        $(this).addClass('out');
    });
    //自动播放
    var curIndex = $('.am-banner .img-box a.active').index();
    var len = $('.am-banner .img-box a').length;
    function autoPlay(){
        if(curIndex == len-1){
            curIndex = 0;
        }else{
            curIndex++;
        }
        $('.am-banner .img-box a').eq(curIndex).addClass('active').siblings().removeClass('active');
        $('.am-banner .num-box i').eq(curIndex).addClass('active').siblings().removeClass('active');
    }
    var timer = setInterval(autoPlay,3000);
    $('body').on('mouseover','.am-banner',function(){
        clearInterval(timer);
    }).on('mouseout','.am-banner',function(){
        curIndex = $('.am-banner .img-box a.active').index();
        timer = setInterval(autoPlay,3000);
    });
}
bannerFn();
</script>
