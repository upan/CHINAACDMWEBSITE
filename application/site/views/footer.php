<div class="am-footer">
    <div class="link">
        <a href="<?php echo base_url('home/page/try_use_apply');?>">ACDM申请</a>
        <a href="<?php echo base_url('home/page/partner');?>">合作伙伴</a>
    </div>
    <p class="contact">
        <span><i class="icon-mail"></i>邮箱：Aviation@VariFlight.com</span>
        <span><i class="icon-tel"></i>电话：0551-65560363</span>
    </p>
    <p>Copyright©2005-2017 飞友科技有限公司 </p>
    <p class="friend-link">友情链接：
        <?php foreach ($this->friend_links as $key => $value):?>
            <a href="<?php echo $value["url"];?>"><?php echo $value["name"];?></a>
        <?php endforeach;?>
    </p>
</div>
<script>
    var MEMBER_AIRPORTS = <?php echo json_encode($this->member_airports);?>;
    var memberStr = MEMBER_AIRPORTS.toString().toLowerCase();
    $('body').on('input','.am-header .search-input input',function(){ //输入事件
        var val = $(this).val();
        if(val.length < 3){
            $(this).parents('.header-cont').find('.tips').addClass('hide');
            return;
        };
        if(memberStr.indexOf(val.toLowerCase()) == -1){ //输入文字不在数据中
            $(this).parents('.header-cont').find('.tips').removeClass('hide');
        }else{
            $(this).parents('.header-cont').find('.tips').addClass('hide');
        }
    });
    $(document).keydown(function(e){ //输入enter键
        var input = $('.am-header .search-input input:text');
        var val = input.val();
        if(e.keyCode == 13 && $(document.activeElement).is(input) && val.length>=3){
            if(memberStr.indexOf(val.toLowerCase()) == -1){ //输入文字不在数据中
                $(this).parents('.header-cont').find('.tips').removeClass('hide');
            }else{
                $(this).parents('.header-cont').find('.tips').addClass('hide');
                window.location.href="https://"+val+".goms.com.cn";
            }
        }
    });
    $('.am-header .search-input a').on('click',function(e){
        e.preventDefault();
        var val = $(this).siblings('input:text').val();
        if(val.length >= 3 && memberStr.indexOf(val.toLowerCase()) != -1){
            window.location.href="https://"+val+".goms.com.cn";   
        }
    });
</script>
</body>
</html>